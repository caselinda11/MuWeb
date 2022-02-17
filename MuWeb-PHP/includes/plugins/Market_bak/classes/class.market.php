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
    private $_CharacterData,$_InventoryData,$flagCode;
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
        $this->Web = Connection::Database('Web');
        #链接
        $this->Link = [
            ['角色寄售', 'CharacterSell','CharacterBuy'],
            ['物品寄售', 'ItemSell','ItemBuy'],
            ['旗帜寄售', 'flagSell','itemBuy'],
        ];
        #导航菜单
        $this->_MarketMenu = [
            #名称,#地址,#状态,#[服务器类型]
            ["角色市场",'character',$this->_CharMarketConfig['active']],
            ["物品市场",'item',$this->_ItemMarketConfig['active']],
            ["旗帜市场",'flag',$this->_FlagMarketConfig['active']],
        ];

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
     * 寄售纪念戒指
     * @param $group
     * @param $username
     * @param $id
     * @param $price
     * @param $price_type
     * @param int $flag
     * @throws Exception
     */
    public function setCharacterFlagSell($group, $username, $id, $price, $price_type, $flag = 1){
        if (!Token::checkToken('market_flag')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($id)) throw new Exception('您暂无可用角色，无法使用此功能！');
        if($price < $this->_FlagMarketConfig['min_price'] || $price > $this->_FlagMarketConfig['max_price']) throw new Exception('寄售的价格必须在['.$this->_FlagMarketConfig['min_price'].'~'.$this->_FlagMarketConfig['max_price']."]之间!");
        if(!$this->checkInventoryFlag($id)) throw new Exception('您的角色[<strong>'.$this->_CharacterData[$id]['Name'].'</strong>]中没有找到<strong>纪念戒指</strong>!');
        if(!$this->flagCode) throw new Exception('无法识别您背包中的<strong>纪念戒指</strong>，请联系在线客服。');

        #重组背包数据
        $new_Inventory = null;
        $Inventory = str_split($this->_InventoryData[$id]['Inventory'], ITEM_SIZE);
        $itemId = array_search($this->flagCode,$Inventory);
        $Inventory[$itemId] = $this->NULL;
        $new_Inventory = implode('',$Inventory);

        $data = [
            'servercode'   =>   getServerCodeForGroupID($group),
            'AccountID'    =>   $username,
            'Character'    =>   $this->_CharacterData[$id]['Name'],
            'item_code'    =>   $this->flagCode,
            'item_type'    =>   4,
            'price'        =>   $price,
            'price_type'   =>   $price_type,
            'flag'         =>   $flag,
            'date'         =>   logDate()
        ];

        $muonline = Connection::Database("MuOnline",$group);

        # 启动事务
        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [Character] SET [Inventory] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ? AND [Name] = ?",[$new_Inventory, $username, $data['Character']]);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_ITEM] ([servercode],[username],[name],[item_code],[item_type],[price],[price_type],[flag],[date]) VALUES (:servercode, :AccountID, :Character, :item_code, :item_type, :price, :price_type, :flag, :date)",$data);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_ITEM_LOG] ([servercode],[sell_username],[sell_item_code],[sell_status],[sell_price],[sell_price_type],[flag],[date]) VALUES (?,?,?,?,?,?,?,?)", [$data['servercode'],$username,$this->flagCode,'上架',$price,$price_type,$flag,$data['date']]);
            $muonline->commit();
            $this->Web->commit();

            return alert('Market/flagSell','恭喜您，旗帜已经寄售成功！');

        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 校验角色背包中是否有旗帜
     * @param $id
     * @return int
     * @throws Exception
     */
    private function checkInventoryFlag($id){
        if (!check_value($id)) return 0;
        if (!is_array($this->_CharacterData)) return 0;
        if(isset($this->_CharacterData[$id])){
            if(!empty($this->_CharacterData[$id]['Inventory'])){
                if(is_array($this->_CharacterData[$id]['Inventory'])){
                    foreach ($this->_CharacterData[$id]['Inventory'] as $inventory){
                        $data = $this->equipment->convertItem($inventory);
                        if(is_array($data)){
                            if($data['type'] == 6676 && $data['level'] == 3) {
                                $this->flagCode = $inventory;
                                return 1;
                            }
                        }
                    }
                }

            }
        }
        $this->flagCode = 0;
        return 0;
    }
    /************************ 寄售物品块 ************************/

    /**
     * 寄售物品
     * @param $group
     * @param $username
     * @param $char_name
     * @param $itemCode
     * @param $itemType
     * @param $itemPrice
     * @param $priceType
     * @param int $flag
     * @return string
     * @throws Exception
     */
    public function setItemSell($group,$username,$char_name,$itemCode,$itemType,$itemPrice,$priceType,$flag=0)
    {
        if(!check_value($group)) throw new Exception("数据错误，请重新尝试！");
        if(!check_value($username)) throw new Exception("数据错误，请重新尝试！");
        if(!Token::checkToken('market_item')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($itemCode)) throw new Exception("没有检测到您的物品信息!");
        if(!check_value($char_name)) throw new Exception("提交出错，未能识别到您的角色名！");
        if(!check_value($itemType)) throw new Exception("提交出错，请选择您的货币类型！");
        if($this->excludeItem($itemCode)) throw new Exception("该物品禁止寄售,请您选择其他物品!");
        if ($this->common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) throw new Exception("您的账号当前游戏在线，请先断开连接。");
        if($itemPrice < $this->_ItemMarketConfig['min_price'] || $itemPrice > $this->_ItemMarketConfig['max_price'])  throw new Exception('寄售的价格必须在['.$this->_ItemMarketConfig['min_price'].'~'.$this->_ItemMarketConfig['max_price'].']之间！');
        #验证是否会员道具
        if(!$this->_ItemMarketConfig['vip_items']){
            if (substr($itemCode,6,2) == "FF") throw new Exception("提交出错，会员道具禁止出售。");
        }
        #验证是否为宝石
        #-----------------------------------------------
        if($this->_ItemMarketConfig['jewel']) {
            $item = $this->equipment->convertItem($itemCode);
            if (   $item['type'] !== 6174 #祝福宝石组合
                || $item['type'] !== 6175 #灵魂宝石组合
                || $item['type'] !== 6280 #生命宝石组合
                || $item['type'] !== 6281 #创造宝石组合
                || $item['type'] !== 6282 #守护宝石组合
                || $item['type'] !== 6283 #再生原石组合
                || $item['type'] !== 6284 #再生宝石组合
                || $item['type'] !== 6285 #玛雅之石组合
                || $item['type'] !== 6286 #低级进化宝石组合
                || $item['type'] !== 6287 #高级进化宝石组合
                || $item['type'] !== 7181 #祝福宝石
                || $item['type'] !== 7182 #灵魂宝石
                || $item['type'] !== 7184 #生命宝石
                || $item['type'] !== 7190 #创造宝石
                || $item['type'] !== 7200 #守护宝石
                || $item['type'] !== 7209 #再生原石
                || $item['type'] !== 7210 #再生宝石
                || $item['type'] !== 7211 #初级进化宝石
                || $item['type'] !== 7212 #高级进化宝石
                || $item['type'] !== 6159 #玛雅之石
            ) throw new Exception("该物品禁止寄售，仅支持出售宝石系列产品。");
        }
        #-----------------------------------------------

        $data = [
         'servercode'   =>   getServerCodeForGroupID($group),
         'AccountID'    =>   $username,
         'Character'    =>   $char_name,
         'item_code'    =>   $itemCode,
         'item_type'    =>   $itemType,
         'price'        =>   $itemPrice,
         'price_type'   =>   $priceType,
         'flag'         =>   $flag,
         'date'         =>   logDate()
        ];
        #仓库中移除该物品
        $warehouse = new warehouse($group);
        $warehouseData = ($warehouse->warehouseRemoveItem($itemCode));
        $muonline = Connection::Database("MuOnline",$group);
        # 启动事务
        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$warehouseData, $username]);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_ITEM] ([servercode],[username],[name],[item_code],[item_type],[price],[price_type],[flag],[date]) VALUES (:servercode, :AccountID, :Character, :item_code, :item_type, :price, :price_type, :flag, :date)",$data);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_ITEM_LOG] ([servercode],[sell_username],[sell_item_code],[sell_status],[sell_price],[sell_price_type],[flag],[date]) VALUES (?,?,?,?,?,?,?,?)", [$data['servercode'],$username,$itemCode,'上架',$itemPrice,$priceType,$flag,$data['date']]);
            $muonline->commit();
            $this->Web->commit();

            return alert('Market/ItemSell','物品发布成功！');

        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 购买物品
     * @param $group
     * @param $username
     * @param $id
     * @return string
     * @throws Exception
     */
    public function setBuyItem($group,$username,$id){
        if(!Token::checkToken('market_item')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception("数据错误，请重新尝试！");
        if(!check_value($username)) throw new Exception("数据错误，请重新尝试！");
        if(!check_value($id)) throw new Exception("数据错误，请重新尝试！");
        if(!Validator::Number($id)) throw new Exception("数据错误，请重新尝试！");
        if(!check_value($_SESSION['userid'])) throw new Exception("数据错误，请重新尝试！");
        if(!check_value($_REQUEST['character_name'])) throw new Exception("数据错误，请重新尝试！");
        $data = $this->getItemInfoForId($id);
        if(!is_array($data)) throw new Exception("您所选的物品已经下架！");
        #判断是旗帜还是普通物品
        if ($data['flag']) {
            $price = ($this->_FlagMarketConfig['price_type']) ? ($data['price'] - round($this->_FlagMarketConfig['price_rate'] * $data['price'] / 100)) : ($data['price'] - $this->_FlagMarketConfig['price_rate']);
            $flag = true;
        }else{
            $price = ($this->_ItemMarketConfig['price_type']) ? ($data['price'] - round($this->_ItemMarketConfig['price_rate'] * $data['price'] / 100)) : ($data['price'] - $this->_ItemMarketConfig['price_rate']);
            $flag = false;
        }
        if(!$price) throw new Exception('价格错误，请联系在线客服。');

        #操作数据
        $itemCode = $this->equipment->convertItem($data['item_code']);
        $StorageBox = [
            'UserGuid'      => $_SESSION['userid'],
            'CharacterName' => $_REQUEST['character_name'],
            'Type'          => 1,
            'ItemCode'      => $itemCode['type'],
            'ItemData'      => $data['item_code'],
            'ValueType'     => '-1',
            'ValueCnt'      => 0,
            'CustomData'    => 0,
            'GetDate'       => logDate(),
            'ExpireDate'    => logDate('+7 day'),
            'UsedInfo'      => 1, # 0已接收,1未接收
            //物品信息
            'index'                 =>  $itemCode['index'],#小编吗
            'level'                 =>  $itemCode['level'],#等级
            'skill'                 =>  $itemCode['skill'],#技能
            'lucky'                 =>  $itemCode['lucky'],#幸运
            'option'                =>  $itemCode['option'],#追加
            'durability'            =>  $itemCode['durability'],#耐久
            'section'               =>  $itemCode['section'],#大编码
            'setOption'             =>  $itemCode['setOption'],#套装
            'newOption'             =>  $itemCode['newOption'],#卓越
            'itemOptionEx'          =>  $itemCode['itemOptionEx'],#380
            'jewelOfHarmonyOption'  =>  $itemCode['jewelOfHarmonyOption'],#再生强化属性
            'socketOption1'         =>  $itemCode['socketOption'][1],#镶嵌[0-4]
            'socketOption2'         =>  $itemCode['socketOption'][2],#镶嵌[0-4]
            'socketOption3'         =>  $itemCode['socketOption'][3],#镶嵌[0-4]
            'socketOption4'         =>  $itemCode['socketOption'][4],#镶嵌[0-4]
            'socketOption5'         =>  $itemCode['socketOption'][5],#镶嵌[0-4]
            'socketBonusPotion'     =>  $itemCode['socketBonusPotion'],#荧光
            'periodItemOption'      =>  $itemCode['periodItemOption'],#时限
        ];
        if (empty($StorageBox))  throw new Exception('[10] - '.'出错了，请您重新输入！');

        $muonline = Connection::Database("MuOnline",$group);
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($data['price_type']);
        $configSettings = $creditSystem->showConfigs(true);
        #操作仓库
//        $warehouse = new warehouse($group);
//        $newWarehouse = $warehouse->warehouseAddItem($data['item_code']);
        try {
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            #减积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($username);
                    break;
                case 'userid':
                    $creditSystem->setIdentifier($_SESSION['userid']);
                    break;
                case 'character':
                    $creditSystem->setIdentifier($_SESSION['character']);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group,$data['price']);
            switch ($this->serverFiles){
                case "egames":
                    $query = "INSERT INTO [T_StorageBox] ([UserGuid],[CharacterName],[Type],[ItemCode],[ItemData],[ValueType],[ValueCnt],[CustomData],[GetDate],[ExpireDate],[UsedInfo]) VALUES (".$StorageBox['UserGuid'].", '".$StorageBox['CharacterName']."', ".$StorageBox['Type'].", '".$StorageBox['ItemCode']."', '".$StorageBox['ItemData']."', '".$StorageBox['ValueType']."', ".$StorageBox['ValueCnt'].", ".$StorageBox['CustomData'].", '".$StorageBox['GetDate']."', '".$StorageBox['ExpireDate']."', ".$StorageBox['UsedInfo'].")";
                    $muonline->query($query);
                    break;
                case "igcn":
                    $muonline->query("INSERT INTO [IGC_GremoryCase] ([AccountID],[Name],[GCType],[GiveType],[ItemType],[ItemIndex],[Level],[ItemDur],[Skill],[Luck],[Opt],[SetOpt],[NewOpt],[BonusSocketOpt],[SocketOpt1],[SocketOpt2],[SocketOpt3],[SocketOpt4],[SocketOpt5],[UsedInfo],[Serial],[RecvDate],[ReceiptDate],[RecvExpireDate],[ItemExpireDate],[RecvDateConvert],[RecvExpireDateConvert],[ItemExpireDateConvert],[ItemOptionEx],[ItemSerial]) VALUES ('".$username."','".$StorageBox['CharacterName']."', 2, 100, ".$StorageBox['section'].", ".$StorageBox['index'].", ".$StorageBox['level'].", ".$StorageBox['durability'].", ".$StorageBox['skill'].", ".$StorageBox['lucky'].", ".$StorageBox['option'].", ".$StorageBox['setOption'].", ".$StorageBox['newOption'].", ".$StorageBox['socketBonusPotion'].", ".$StorageBox['socketOption1'].", ".$StorageBox['socketOption2'].", ".$StorageBox['socketOption3'].", ".$StorageBox['socketOption4'].", ".$StorageBox['socketOption5'].", ".$StorageBox['UsedInfo'].", 0, '".$StorageBox['GetDate']."', NULL, '".$StorageBox['ExpireDate']."', '1970-01-01 00:00:00', ".time().", ".strtotime($StorageBox['ExpireDate']).", 0, 0, 0)");
                    break;
                default:
                    throw new Exception("暂不支持您的游戏版本。");
            }
            //发到仓库
//            $muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$newWarehouse, $username]);
            $this->Web->query("UPDATE [X_TEAM_MARKET_ITEM] SET [status] = ?,[buy_username] = ?,[buy_date] = ? WHERE [ID] = ? AND [name] = ?",[1,$username,logDate(),$data['ID'],$data['name']]);
            $this->Web->query("UPDATE [X_TEAM_MARKET_ITEM_LOG] SET [sell_status] = ?, [buy_price] = ? ,[buy_username] = ?,[buy_date] = ? WHERE [sell_item_code] = ? AND [sell_username] = ?",['购买',$price,$username,logDate(),$data['item_code'],$data['username']]);
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
            if($flag){
                return alert('Market/flag','恭喜您购买成功，物品已经存入您的角色储物柜中！');
            }else{
                return alert('Market/Item','恭喜您购买成功，物品已经存入您的角色储物柜中！');
            }
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
                throw new Exception($exception->getMessage());
        }
    }

    /**
     * 物品下架
     * @param $group
     * @param $username
     * @param $id
     * @param int $type
     * @return string
     * @throws Exception
     */
    public function setItemSellOff($group, $username, $id ,$type = 0)
    {
        if(!check_value($group)) throw new Exception("[1]数据错误，请重新尝试！");
        if(!check_value($username)) throw new Exception("[2]数据错误，请重新尝试！");
        if(!check_value($id)) throw new Exception("[3]数据错误，请重新尝试！");
        if(!Validator::Number($id)) throw new Exception("[4]数据错误，请重新尝试！");
        $data = $this->getItemInfoForId($id);

        if(!is_array($data)) throw new Exception("您所选的物品已经下架!");
        #组装仓库代码
        $warehouse = new warehouse($group);
        $newWarehouse = $warehouse->warehouseAddItem($data['item_code']);
        $muonline = Connection::Database("MuOnline",$group);

        $insert = [
            'servercode'        => getServerCodeForGroupID($group),
            'sell_username'     => $username,
            'sell_item_code'    => $data['item_code'],
            'sell_status'       => "下架",
            'sell_price'        => $data['price'],
            'sell_price_type'   => $data['item_type'],
            'sell_date'         => $data['date'],
            'flag'              => $type,
        ];
        try {
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$newWarehouse,$username]);
            $this->Web->query("DELETE FROM [X_TEAM_MARKET_ITEM] WHERE [ID] = ?",[$id]);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_ITEM_LOG] ([servercode],[sell_username],[sell_item_code],[sell_status],[sell_price],[sell_price_type],[flag],[date]) VALUES (:servercode, :sell_username, :sell_item_code, :sell_status, :sell_price, :sell_price_type, :flag, :sell_date)",$insert);
            $muonline->commit();
            $this->Web->commit();
            if($type){
                return alert('Market/flag','物品已成功下架！');
            }else{
                return alert('Market/Item','物品已成功下架！');
            }
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
                throw new Exception($exception->getMessage());
        }
    }

    /**
     * 识别物品类型
     * @param $itemType
     * @return mixed
     */
    public function getItemCategory($itemType)
    {
        if(!array_key_exists($itemType,$this->itemType)) return $this->itemType[0];
        return $this->itemType[$itemType];
    }

    /**
     * 排除物品
     * @param $itemCode
     * @return bool
     * @throws Exception
     */
    private function excludeItem($itemCode){
        $itemConfig = $this->loadConfig('item');
        if(!check_value($itemConfig['exclude'])) return false;

        $excludedItem = explode(",", (string)$itemConfig['exclude']);
        if(!is_array($excludedItem)) return false;
        $itemType = $this->getItemType($itemCode);
        foreach($excludedItem as $itemExcludeCode) {
            if ($itemType == $itemExcludeCode) return true;
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
        return $m_type;
    }

    /**
     * 获取市场售卖列表
     * @param int $type
     * @return array|null
     * @throws Exception
     */
    public function getMarketItemList($type=0)
    {
        $data = $this->Web->query_fetch("SELECT [ID],[servercode],[username],[name],[item_code],[item_type],[price],[price_type],[flag],[date] FROM [X_TEAM_MARKET_ITEM] WHERE status = 0 AND [flag] = ?",[$type]);
        if(!is_array($data)) return null;
        foreach ($data as $key=>$datum){
            $item = $this->equipment->convertItem($datum['item_code']);
            $itemName = $this->equipment->getItemName($item['section'],$item['index'],$item['level']);
            $data[$key]['item_name'] = $itemName;
        }
        //根据时间降序
        return arraySortByKey($data,'date',0);
    }

    /**
     * 获取用户寄售中的物品列表
     * @param $username
     * @return array|bool|null
     * @throws Exception
     */
    public function getMyMarketItemList($username)
    {
        $data = $this->Web->query_fetch("SELECT [ID],[servercode],[username],[name],[item_code],[item_type],[price],[price_type],[flag],[date] FROM [X_TEAM_MARKET_ITEM] WHERE [username]= ? AND status = 0 AND [servercode] = ?",[$username,getServerCodeForGroupID($_SESSION['group'])]);
        if(!is_array($data)) return null;
        return $data;
    }

    /**
 * 从ID获取物品信息
 * @param $id
 * @return mixed|void|null
 * @throws Exception
 */
    public function getItemInfoForId($id)
    {
        if(!check_value($id)) throw new Exception("数据错误，请重新尝试！");
        if(!Validator::Number($id)) throw new Exception("数据错误，请重新尝试！");
        $data = $this->Web->query_fetch_single("SELECT [ID],[servercode],[username] ,[name],[item_code],[item_type],[price],[price_type],[flag],[date] FROM [X_TEAM_MARKET_ITEM] WHERE status = 0 and [ID] = ? AND [servercode] = ?",[$id,$_SESSION['group']]);
        if(!is_array($data)) return;
        return $data;
    }

    /************************ 寄售角色快 ************************/

    /**
     * 获取角色详细信息从ID
     * @param $id
     * @return mixed|void|null
     * @throws Exception
     */
    public function _getCharInfoForMarketName($id)
    {
        if(!check_value($id)) throw new Exception("数据错误，请重新尝试！");
        if(!Validator::Number($id)) throw new Exception("数据错误，请重新尝试！");
        $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE [ID]= ?";
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
     * @return array|bool|null
     * @throws Exception
     */
    public function getMarketCharList()
    {
        $data = $this->Web->query_fetch("SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE [status] = 0");
        if(!is_array($data)) return null;
        foreach ($data AS $key=>$item){
            $newData = $this->character->getCharacterDataForCharacterName(getGroupIDForServerCode($item['servercode']),$item['name']);
            if(!is_array($newData)) continue;
            $char_data[$key] = [
                'ID'            => $item['ID'],
                'username'      => $item['username'],
                'servercode'    => $item['servercode'],
                'group'         => getGroupIDForServerCode($item['servercode']),
                'name'          => $item['name'],
                'price'         => $item['price'],
                'price_type'    => $item['price_type'],
                'date'          => $item['date'],
                'cLevel'        => $newData['cLevel'],
                'mLevel'        => $newData['mLevel'],
                'Class'         => $newData['Class'],
                'Money'         => $newData['Money'],
                'PkLevel'       => $newData['PkLevel'],
                'CtlCode'       => $newData['CtlCode'],
            ];
        }
        if(empty($char_data)) return null;
        //从发布时间排序
        return arraySortByKey($char_data,"date",0);
    }


    /**
     * 获取市场寄售对象
     * @param $id
     * @return mixed|void|null
     */
    public function getMarketCharForId($id)
    {
        if(!$id) return;
        $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE [status] = 0 AND [ID] = ?";
        $data = $this->Web->query_fetch_single($query,[$id]);
        if(!is_array($data))  return;
        return $data;
    }


    /**
     * 获取用户寄售中的角色列表
     * @param $username
     * @return array|bool|null
     * @throws Exception
     */
    public function getMyMarketCharList($username)
    {
        $query = "SELECT [ID],[servercode],[username],[name],[price],[price_type],[date] FROM [X_TEAM_MARKET_CHAR] WHERE  [status] = 0 AND [username] = ? AND [servercode] = ?";
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
     * @return string
     * @throws Exception
     */
    public function setCharSell($group,$username,$name,$price,$priceType,$code)
    {
        if(!Token::checkToken('market_char'.$code)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception("发布失败，请重新登陆！");
        if(!check_value($username)) throw new Exception("发布失败，请重新登陆！");
        if(!check_value($name)) throw new Exception("发布失败，请重新登陆！");
        if(!check_value($price)) throw new Exception("您必须输入物品价格!");
        if($price < $this->_CharMarketConfig['min_price'] || $price > $this->_CharMarketConfig['max_price']) throw new Exception("发布失败，售价必须在[".$this->_CharMarketConfig['min_price']."~".$this->_CharMarketConfig['max_price'].']之间!');
        if(!$priceType) throw new Exception("发布失败，请选择价格类型!!");
        if($this->checkSellChar($name) >= $this->_CharMarketConfig['frequency']) throw new Exception("该角色已经超过出售次数，无法再次出售!");
        $muonline = Connection::Database("MuOnline",$group);

        $servercode = getServerCodeForGroupID($group);

        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [Character] SET [CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[1,$username,$name]);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_CHAR] ([servercode],[username],[name],[price],[price_type],[date]) VALUES (?,?,?,?,?,?)",[$servercode,$username,$name,$price,$priceType,logDate()]);
            $muonline->commit();
            $this->Web->commit();
            return alert('Market/CharacterSell','寄售成功!');
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
     * @throws Exception
     */
    public function setCharSellOff($group, $username, $id)
    {
        if(!check_value($group)) throw new Exception("数据错误，请重新登陆！");
        if(!check_value($username)) throw new Exception("数据错误，请重新登陆！");
        if(!check_value($id)) throw new Exception("数据错误，请重新登陆！");
        if(!Validator::Number($id)) throw new Exception("数据错误，请重新尝试！");
        $data = $this->getMarketCharForId($id);
        if(!is_array($data))  throw new Exception("数据错误，请重新登陆！");

        $muonline = Connection::Database("MuOnline",$group);
        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [Character] SET [CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[0,$username,$data['name']]);
            $this->Web->query("DELETE FROM [X_TEAM_MARKET_CHAR] WHERE [username] = ? AND [name] = ?",[$username,$data['name']]);
            $muonline->commit();
            $this->Web->commit();
            return alert('Market/CharacterSell','['.$data['name'].'] 已经下架!');
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
                throw new Exception($exception->getMessage());
        }
    }


    /**
     * 购买角色
     * @param $group
     * @param $buy_username|买家ID
     * @param $sell_id|卖家数据ID
     * @throws Exception
     */
    public function setBuyCharacter($group,$buy_username,$sell_id)
    {
        if(!Token::checkToken('market_character')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception("数据错误，请重新尝试！");
        if(!check_value($buy_username)) throw new Exception("数据错误，请重新尝试！");
        if(!Validator::Number($sell_id)) throw new Exception("数据错误，请重新尝试！");
        $data = $this->getMarketCharForId($sell_id);
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
        $price = ($this->_CharMarketConfig['price_type']) ? ($data['price'] - round($this->_CharMarketConfig['price_rate'] * $data['price'] / 100)) : ($data['price'] - $this->_CharMarketConfig['price_rate']);

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
                case 'userid':
                    $creditSystem->setIdentifier($_SESSION['userid']);
                    break;
                case 'character':
                    $creditSystem->setIdentifier($_SESSION['character']);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group,$data['price']);

            $muonline->query("UPDATE [AccountCharacter] SET ".$sell_accountDataKey." = ? WHERE Id = ?",[null,$data['username']]);
            $muonline->query("UPDATE [AccountCharacter] SET ".$buy_accountDataKey." = ? WHERE Id = ?",[$data['name'],$buy_username]);
            $muonline->query("UPDATE [Character] SET [AccountID] = ?,[CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[$buy_username,0,$data['username'],$data['name']]);
            $this->Web->query("UPDATE [X_TEAM_MARKET_CHAR] SET [status] = ?,[buy_username] = ?,[buy_date] = ? WHERE [ID] = ? AND [name] = ?",[1,$buy_username,logDate(),$data['ID'],$data['name']]);
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
            return alert('Market/Character','恭喜您购买成功，['.$data['name'].']已经加入您的麾下！');

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

    /************************ 插件必备函数 ************************/
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