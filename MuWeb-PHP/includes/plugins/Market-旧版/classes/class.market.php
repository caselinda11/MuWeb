<?php
/**
 * 交易市场类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin\Market;

use Exception;
use Plugin\equipment;
use Validator;
use Connection;
use Character;
use CreditSystem;
use Token;
use common;

class Market{
    /************************ 插件必备函数 ************************/
	private $_modulesPath = 'modules';
    /************************ 插件必备函数 ************************/
    private $serverFiles;
    private $_MarketMenu;
    private $WAREHOUSE_LENGTH = 7680;
    private $NULL;
    private $equipment,$character;
    private $Web,$common;
    private $_ItemMarketConfig,$_CharMarketConfig,$_FlagMarketConfig;
    private $_CharacterData,$_InventoryData;
    public $itemType= [
            1 =>'武器',
            2 =>'防具/装备',
            3 =>'翅膀',
            4 =>'首饰',
            5 =>'材料',
            6 =>'宝石',
            7 =>'宠物/坐骑',
            0 =>'其他',
    ];
	public $Link;
    /**
     * 构造函数
     * Market constructor.
     * @throws Exception
     */
	public function __construct()
    {
        #服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        #仓库BUFF长度
        if($this->serverFiles =="igcn") $this->WAREHOUSE_LENGTH = $this->WAREHOUSE_LENGTH*2;
        #空物品值
        $this->NULL = str_pad("F",ITEM_SIZE,"F");

        $this->_ItemMarketConfig = $this->loadConfig('item');
        $this->_CharMarketConfig = $this->loadConfig('char');
        $this->_FlagMarketConfig = $this->loadConfig('flag');

        $this->equipment = new equipment();
        $this->character = new Character();
        $this->common = new common();
		#链接
        $this->Link = [
            ['角色寄售', 'CharacterSell','CharacterBuy'],
            ['物品寄售', 'ItemSell','ItemBuy'],
            ['旗帜寄售', 'flagSell','itemBuy'],
        ];
        #导航菜单
        $this->_MarketMenu = [
            #名称,#地址,#状态,#[服务器类型]
            ["角色市场",'Character',$this->_CharMarketConfig['active']],
            ["物品市场",'Item',     $this->_ItemMarketConfig['active']],
            ["旗帜市场",'flag',     $this->_FlagMarketConfig['active']],
        ];
        $this->Web = Connection::Database('Web');
    }

    /************************ 旗帜物品块 ************************/

    /**
     * 获取角色背包
     * @param $group
     * @param $username
     * @return mixed|void|null
     * @throws Exception
     */
    public function getCharacterInventory($group,$username)
    {
        if (!check_value($group)) return;
        if (!check_value($username)) return;
        $query = "SELECT Name,Class,CONVERT(varchar(max),Inventory,2) AS Inventory FROM Character WHERE AccountID = ? AND CtlCode = 0";
        $result = Connection::Database('MuOnline', $group)->query_fetch($query,[$username]);
        if (!is_array($result)) return;
        $this->_InventoryData = $result;
        foreach ($result as &$item){
            #过滤返回
            $item['Inventory'] = array_filter((array)$this->equipment->setEquipmentCode($item['Inventory']));
        }
        $this->_CharacterData = $result;
        return $this->_CharacterData;
    }


    /**
     * 识别物品类型
     * @param $itemType
     * @return mixed
     */
    public function getItemCategory($itemType)
    {
        foreach ($this->itemType as $id=>$name){
            if($itemType == $id) return $name;
        }
        return $this->itemType[0];
    }


    /**
     * 排除物品
     * @param $itemCode
     * @return bool|string|void
     */
    private function excludeItem($itemCode){
        if(!check_value($this->_ItemMarketConfig['exclude'])) return false;
        $excludedItem = explode(",", (string)$this->_ItemMarketConfig['exclude']);
        if(!is_array($excludedItem)) return false;
        $itemType = $this->getItemType($itemCode);
        $itemName = '未知物品';
        foreach($excludedItem as $itemExcludeCode) {
            if ($itemType[0] == $itemExcludeCode){
                $itemName = $this->equipment->getItemName($itemType[1],$itemType[2],$itemType[3]);
                return $itemName;
            }
        }
        return false;
    }

    /**
     * 获取物品总编码
     * @param $ItemData
     * @return int|mixed|void
     */
    public function getItemType($ItemData){
        if($ItemData == ($this->NULL)) return;
        if(empty($ItemData)) return;
        $dataInv = str_split($ItemData, 2); //划切2
        $i = 0;
        while($i<=16){//放进数组
            $item[$i] = hexdec($dataInv[$i]); //16进10
            $i++;
        }
        if(empty($item)) return;
        #解算
        $m_type = $item[0];
        $m_type |= ($item[9] & 0xF0) << 5;
        $m_type |= ($item[7] & 0x80) << 1;		//算总类
        $m_Section = ($item[9] & 0xF0) <<5;
        $m_Section = $m_Section / 512;			//分类
        $m_index = $item[0] % 256;
        $m_index |= ($item[7] & 0x80) * 2;		//序号
        $m_level = (($item[1]) >> 3) & 0xF;  	//等级
        return [$m_type,$m_Section,$m_index,$m_level];
    }

    /**
     * 获取市场售卖列表
     * @param int $type
     * @return array|bool|void|null
     */
    public function getMarketItemList($type=0)
    {
        $data = $this->Web->query_fetch("SELECT [ID],[servercode],[username],[name],[item_code],[item_type],[price],[price_type],[flag],[date] FROM [X_TEAM_MARKET_ITEM] WHERE status = 0 AND [flag] = ?",[$type]);
        if(!is_array($data)) return null;
        return $data;
    }


    /************************ 寄售角色快 ************************/

    /**
     * 获取角色详细信息从ID
     * @param $id
     * @param bool $models
     * @return mixed|void|null
     * @throws Exception
     */
    public function _getCharInfoForMarketName($id,$models=false)
    {
        if(!check_value($id)) throw new Exception("数据错误，请重新尝试！");
        if(!Validator::Number($id)) throw new Exception("数据错误，请重新尝试！");
        if($models){
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_ITEM] WHERE [ID]= ?";
        }else{
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE [ID]= ?";
        }
        $data = $this->Web->query_fetch_single($query,[$id]);
        if(!is_array($data)) return;
        $group = getGroupIDForServerCode($data['servercode']);
        $result = $this->character->getCharacterDataForCharacterName($group,$data['name']);
        if(!is_array($result)) return;
        $result['servercode'] = $data['servercode'];
        $result['price'] = $data['price'];
        $result['price_type'] = $data['price_type'];
        $result['ID'] = $data['ID'];
        return $result;
    }

    /**
     * 获取市场售卖列表
     * @param bool $models
     * @return array|bool|void|null
     */
    public function getMarketCharList($models=false)
    {
        if($models){
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_ITEM] WHERE [status] = 0 ORDER BY [date] DESC";
        }else{
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE [status] = 0 ORDER BY [date] DESC";
        }
        $data = $this->Web->query_fetch($query);
        if(!is_array($data)) return null;
        return $data;
    }

    /**
     * 获取市场寄售对象
     * @param $id
     * @param bool $models
     * @return mixed|void|null
     */
    public function getMarketCharForId($id,$models=false)
    {
        if(!$id) return;
        if($models){
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_ITEM] WHERE [status] = 0 AND [ID] = ?";
        }else{
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE [status] = 0 AND [ID] = ?";
        }
        $data = $this->Web->query_fetch_single($query,[$id]);
        if(!is_array($data))  return;
        return $data;
    }

    /**
     * 获取用户寄售中的角色列表
     * @param $username
     * @param bool $models
     * @return array|bool|null
     * @throws Exception
     */
    public function getMyMarketCharList($username,$models=false)
    {
        if($models){
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_ITEM] WHERE  [status] = 0 AND [username] = ? AND [servercode] = ?";
        }else{
            $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE  [status] = 0 AND [username] = ? AND [servercode] = ?";
        }
        $data = $this->Web->query_fetch($query,[$username,getServerCodeForGroupID($_SESSION['group'])]);
        if(!is_array($data)) return null;
        return $data;
    }

    /**
     * 输出背包物品
     * @param $inventory
     * @return null|$img|$invData
     * @throws Exception
     */
    public function _getInventoryData($inventory)
    {
        $item = $this->equipment->setEquipmentCode($inventory);
        if (!is_array($item)) return null;
        $data = array_filter($item);
        foreach ($data as $key=>$invData){
            $img = $this->equipment->ItemsUrl($invData);
            $result[$key] = [
                $img,
                $invData,
            ];
        }
        if(empty($result)) return null;
        return $result;
    }

    /**
     * 寄售角色
     * @param $group
     * @param $username
     * @param $name
     * @param $price
     * @param $priceType
     * @param $code
     * @param bool $models
     * @throws Exception
     */
    public function setCharSell($group,$username,$name,$price,$priceType,$code,$models=false)
    {
        if(!Token::checkToken('market_char'.$code)) throw new Exception("提交错误，请重新尝试。");
        if(!check_value($group)) throw new Exception("发布失败，请重新登陆！");
        if(!check_value($username)) throw new Exception("发布失败，请重新登陆！");
        if(!check_value($name)) throw new Exception("发布失败，请重新登陆！");
        if(!check_value($price)) throw new Exception("您必须输入物品价格!");
        if ($this->common->checkUserOnline($group, $username)) throw new Exception("您的账号当前游戏在线，请先断开连接。");
        if($models){
            $charData = $this->character->getCharacterDataForCharacterName($group,$name);
            $Inventory = $this->_getInventoryData($charData['Inventory']);
            if(!is_array($Inventory)) throw new Exception("您的背包为空无法寄售。");
            foreach ($Inventory as $item){
                $items = $this->equipment->convertItem($item[1]);
                #输入物品总编号
                if($items['type'] == 6676 && $items['level'] == 3) throw new Exception("背包中包含纪念戒指，该角色无法出售。");
                if($items['type'] == 6720) throw new Exception("背包中包含强化恶魔，该角色无法出售。");
				
				$GetItemStrengthenOption = ($items['jewelOfHarmonyOption'] & 0xF0) >> 4;
				if($GetItemStrengthenOption > 0 && $GetItemStrengthenOption < 255) throw new Exception("背包或角色身上包含强化物品，该角色无法出售。"); 
				
                #会员物品限制
                if($items['vip']) throw new Exception("背包中包含会员物品，无法寄售！");
            }
			if($price < $this->_ItemMarketConfig['min_price'] || $price > $this->_ItemMarketConfig['max_price']) throw new Exception("发布失败，售价必须在[".$this->_ItemMarketConfig['min_price']."~".$this->_ItemMarketConfig['max_price'].']之间!');
        
        }else{
			if($price < $this->_CharMarketConfig['min_price'] || $price > $this->_CharMarketConfig['max_price']) throw new Exception("发布失败，售价必须在[".$this->_CharMarketConfig['min_price']."~".$this->_CharMarketConfig['max_price'].']之间!');
        
		}
        if(!$priceType) throw new Exception("发布失败，请选择价格类型!!");
        if($this->checkSellChar($name) >= $this->_CharMarketConfig['frequency']) throw new Exception("该角色已经超过出售次数，无法再次出售!");
        $muonline = Connection::Database("MuOnline",$group);

        $servercode = getServerCodeForGroupID($group);

        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [Character] SET [CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[1,$username,$name]);
            if($models){
                $this->Web->query("INSERT INTO [X_TEAM_MARKET_ITEM] ([servercode],[username],[name],[price],[price_type],[date]) VALUES (?,?,?,?,?,?)",[$servercode,$username,$name,$price,$priceType,logDate()]);
            }else{
                $this->Web->query("INSERT INTO [X_TEAM_MARKET_CHAR] ([servercode],[username],[name],[price],[price_type],[date]) VALUES (?,?,?,?,?,?)",[$servercode,$username,$name,$price,$priceType,logDate()]);
            }
            $muonline->commit();
            $this->Web->commit();
            if($models) {
                return alert('Market/ItemSell', '背包寄售成功!');
            }else{
                return alert('Market/CharacterSell','寄售成功!');
            }
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
                throw new Exception($exception->getMessage());
        }
    }

    /**
     * 角色下架
     * @param $group
     * @param $username
     * @param $id
     * @param bool $models
     * @throws Exception
     */
    public function setCharSellOff($group, $username, $id, $models=false)
    {
        if(!check_value($group)) throw new Exception("[0]数据错误，请重新登陆！");
        if(!check_value($username)) throw new Exception("[0]数据错误，请重新登陆！");
        if(!check_value($id)) throw new Exception("[0]数据错误，请重新登陆！");
        if(!Validator::Number($id)) throw new Exception("[0]数据错误，请重新尝试！");
        if ($this->common->checkUserOnline($group, $username)) throw new Exception("您的账号当前游戏在线，请先断开连接。");
        $data = ($models) ? $this->getMarketCharForId($id,true) : $this->getMarketCharForId($id);
        if(!is_array($data)) throw new Exception("[1]数据错误，请重新登陆！");

        $muonline = Connection::Database("MuOnline",$group);
        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [Character] SET [CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[0,$username,$data['name']]);
            if($models){
                $this->Web->query("DELETE FROM [X_TEAM_MARKET_ITEM] WHERE [username] = ? AND [name] = ?",[$username,$data['name']]);
            }else{
                $this->Web->query("DELETE FROM [X_TEAM_MARKET_CHAR] WHERE [username] = ? AND [name] = ?",[$username,$data['name']]);
            }
            $muonline->commit();
            $this->Web->commit();
            if($models){
                return alert('Market/ItemSell','['.$data['name'].'] 已经下架!');
            }else{
                return alert('Market/CharacterSell','['.$data['name'].'] 已经下架!');
            }

        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
                throw new Exception($exception->getMessage());
        }
    }

    /**
     * 购买角色
     * @param $group
     * @param $buy_username
     * @param $sell_id
     * @param bool $models
     * @throws Exception
     */
    public function setBuyCharacter($group,$buy_username,$sell_id,$models=false)
    {
        if(!Token::checkToken('market_character')) throw new Exception("提交错误，请重新尝试。");
        if(!check_value($group)) throw new Exception("数据错误，请重新尝试！");
        if(!check_value($buy_username)) throw new Exception("数据错误，请重新尝试！");
        if(!Validator::Number($sell_id)) throw new Exception("数据错误，请重新尝试！");
        if ($this->common->checkUserOnline($group, $buy_username)) throw new Exception("您的账号当前游戏在线，请先断开连接。");
        $data = ($models) ? $this->getMarketCharForId($sell_id,true) : $this->getMarketCharForId($sell_id);
        if(!is_array($data))  throw new Exception("您所选的角色已经下架!");
        #卖家数据#取键值
        $sell_accountData = $this->character->getAccountCharacterNameForAccount($group,$data['username']);
        if(!check_value($sell_accountData)) throw new Exception("数据错误，请重新尝试！");
        $sell_accountDataKey = array_search($data['name'],$sell_accountData);
        if(!$sell_accountDataKey) throw new Exception("该角色已经下架，请选择其他角色！");
        #买家数据#取键值
        $buy_accountData = $this->character->getAccountCharacterNameForAccount($group,$buy_username);
        if(!check_value($buy_accountData)) throw new Exception("您还是一个新的账号,请先创建一个角色再来尝试!");
        $buy_accountDataKey = array_search('',$buy_accountData);
        if(!$buy_accountDataKey) throw new Exception("您没有足够的位置储存新角色!");
        #售价-手续费
		if($models){
			$price = ($this->_ItemMarketConfig['price_type']) ? ($data['price'] - round($this->_ItemMarketConfig['price_rate'] * $data['price'] / 100)) : ($data['price'] - $this->_ItemMarketConfig['price']);
		}else{
			$price = ($this->_CharMarketConfig['price_type']) ? ($data['price'] - round($this->_CharMarketConfig['price_rate'] * $data['price'] / 100)) : ($data['price'] - $this->_CharMarketConfig['price']);
		}
        $muonline = Connection::Database("MuOnline",$group);
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($data['price_type']);
        $configSettings = $creditSystem->showConfigs(true);

        try {
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            #减积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($buy_username);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group,$data['price']);

            $muonline->query("UPDATE [AccountCharacter] SET ".$sell_accountDataKey." = ? WHERE Id = ?",[null,$data['username']]);
            $muonline->query("UPDATE [AccountCharacter] SET ".$buy_accountDataKey." = ? WHERE Id = ?",[$data['name'],$buy_username]);
            $muonline->query("UPDATE [Character] SET [AccountID] = ?,[CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[$buy_username,0,$data['username'],$data['name']]);
            if ($models){
                $this->Web->query("UPDATE [X_TEAM_MARKET_ITEM] SET [status] = ?,[buy_username] = ?,[buy_date] = ? WHERE [ID] = ? AND [name] = ?",[1,$buy_username,logDate(),$data['ID'],$data['name']]);

            }else{
                $this->Web->query("UPDATE [X_TEAM_MARKET_CHAR] SET [status] = ?,[buy_username] = ?,[buy_date] = ? WHERE [ID] = ? AND [name] = ?",[1,$buy_username,logDate(),$data['ID'],$data['name']]);

            }
            #加积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($data['username']);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->addCredits($group,$price);
            $muonline->commit();
            $this->Web->commit();
            if($models){
                return alert('Market/Item','恭喜您购买成功，['.$data['name'].']已经加入您的麾下！');
            }else{
                return alert('Market/Character','恭喜您购买成功，['.$data['name'].']已经加入您的麾下！');
            }

        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
                throw new Exception($exception->getMessage());
        }
    }

    /**
     * 检测有几条数据
     * @param $name
     * @return mixed|void
     */
    public function checkSellChar($name)
    {
        if(!$name) return;
        $result = $this->Web->query_fetch_single("SELECT COUNT(*) as result FROM [X_TEAM_MARKET_CHAR] where [name] = ?",[$name]);
        if(!is_array($result)) return;
        return $result['result'];
    }

    /************************ 插件必备函数 ************************/
    /**
     * 导航栏
     */
    public function MarketMenu() {
        echo '<div class="card">';
            echo '<div class="row mb-3">';
                echo '<div class="btn-group col-md-12" role="group">';
                foreach($this->_MarketMenu as $menuData) {
                    if(is_array($menuData[3])) {
                        if(!in_array($this->serverFiles, $menuData[3])) continue;
                    }
                    if($menuData[2]) {
                        if($_REQUEST['subpage'] == $menuData[1]) {
                            echo '<a href="'.__PLUGIN_MARKET_HOME__.$menuData[1].'" class="btn btn-outline-dark active">';
                            echo $menuData[0];
                            echo '</a>';
                        } else {
                            echo '<a href="'.__PLUGIN_MARKET_HOME__.$menuData[1].'" class="btn btn-outline-dark">';
                            echo $menuData[0];
                            echo '</a>';
                        }
                    }
                }
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    /**
     * @throws Exception
     */
    public function marketSellMenu()
    {
        if(isLoggedIn()) {
            echo '<div class="card">';
                echo '<div class="row mb-3">';
                    echo '<div class="btn-group col-md-12" role="group">';
                    if ($this->_CharMarketConfig['active']) {
                        $active = ($_REQUEST['subpage'] == $this->Link[0][1]) ? "active" : "";
                        echo '<a href="' . __PLUGIN_MARKET_HOME__ . $this->Link[0][1].'" class="btn btn-outline-success '.$active.'">';
                        echo '角色寄售</a>';
                    }
                    if ($this->_ItemMarketConfig['active']) {
                        $active = ($_REQUEST['subpage'] == $this->Link[1][1]) ? "active" : "";
                        echo '<a href="' . __PLUGIN_MARKET_HOME__ . $this->Link[1][1].'" class="btn btn-outline-success '.$active.'">';
                        echo '物品寄售</a>';
                    }
                    if ($this->_FlagMarketConfig['active']) {
                        $active = ($_REQUEST['subpage'] == $this->Link[2][1]) ? "active" : "";
                        echo '<a href="' . __PLUGIN_MARKET_HOME__ . $this->Link[2][1].'" class="btn btn-outline-success '.$active.'">';
                        echo '旗帜寄售</a>';
                    }
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }
    }

    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载交易市场插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载交易市场插件模块。');
		if(!@include_once(__PATH_PLUGIN_MARKET_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new Exception('无法加载交易市场插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_PLUGIN_MARKET_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

    /**
     * 加载配置文件
     * @param $fileName
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($fileName='config')
    {
        $xmlPath = __PATH_PLUGIN_MARKET_ROOT__.$fileName.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('交易市场配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载交易市场插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }

    /************************ 插件必备函数 ************************/
}