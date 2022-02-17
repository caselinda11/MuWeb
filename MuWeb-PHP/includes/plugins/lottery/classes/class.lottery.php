<?php
/**
 * 在线夺宝类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Connection;
use Plugin\Market\warehouse;
use Validator;
use CreditSystem;

class lottery {

	private $_modulesPath = 'modules';
    private $serverFiles;
    private $tConfig;
    private $web;
    private $NULL;
    /**
     * 构造函数
     * lottery constructor.
     * @throws Exception
     */
	public function __construct()
    {
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        $this->tConfig = $this->loadConfig('config');
        $this->web = Connection::Database("Web");

        #空物品值
        $this->NULL = str_pad("F",ITEM_SIZE,"F");
    }

    /**
     * 水晶兑换物品
     * @param $group
     * @param $username
     * @param $id
     * @return string
     * @throws Exception
     */
    public function setLotteryReceiveShop($group, $username ,$id)
    {
        if(!check_value($group)) throw new Exception('');
        if(!check_value($username)) throw new Exception('');
        if(!check_value($id))  throw new Exception('');
        if(!($id))  throw new Exception('');
        #拥有的水晶数量
        $crystal = $this->getCountLotteryLog($username);
        #调取稀有物品
        $item = $this->web->query_fetch_single("SELECT * FROM [X_TEAM_LOTTERY_SHOP] WHERE [ID] = ? AND [status] = ?",[$id,1]);
        if(!is_array($item)) throw new Exception("该稀有物品已经下架,请选择其他稀有物品!");

        if($item['reward_item_price'] > $crystal) throw new Exception("您的水晶不足，无法兑换该物品！");
        #先给水晶找到ID
        $SELECT = $this->web->query_fetch("SELECT [ID] FROM [X_TEAM_LOTTERY_LOG] WHERE [win_code] = '水晶' AND [status] = 0 AND [username] = ?",[$username]);
//        debug($SELECT);

        $ware = new warehouse($group);
        #仓库增加物品
        $NewWarehouseData = $ware->warehouseAddItem($item['reward_item_code']);
        $muonline = Connection::Database("MuOnline",$group);

        try{
            $muonline->beginTransaction();
            $this->web->beginTransaction();
            $muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$NewWarehouseData,$username]);
            for($i=0;$i<$item['reward_item_price'];$i++){
                $this->web->query("UPDATE [X_TEAM_LOTTERY_LOG] SET [status] = ?,[receive_date] = ? WHERE [win_code] = '水晶' AND [username] = ? AND [ID] = ?",[1,logDate(),$username,$SELECT[$i]['ID']]);
            }
            $muonline->commit();
            $this->web->commit();

            return alert('usercp/lotteryShop','稀有物品兑换成功！');

        }catch (Exception $exception){
            $muonline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }


    /**
     * 领取奖励
     * @param $group
     * @param $username
     * @param $id
     * @return string
     * @throws Exception
     */
    public function setLotteryReceive($group, $username, $id)
    {
        if(!check_value($group)) throw new Exception('');
        if(!check_value($username)) throw new Exception('');
        if(!check_value($id))  throw new Exception('');
        $itemCode = $this->web->query_fetch_single("SELECT [win_code] FROM [X_TEAM_LOTTERY_LOG] WHERE [username] = ? AND [ID] = ? AND [status] = ?",[$username,$id,0]);
        if(!is_array($itemCode)) throw new Exception("该物品已经领取过了！");
        $ware = new warehouse($group);
        #仓库增加物品
        $NewWarehouseData = $ware->warehouseAddItem($itemCode['win_code']);
        $muOnline = Connection::Database("MuOnline",$group);
        try{
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            $muOnline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$NewWarehouseData,$username]);
            $this->web->query("UPDATE [X_TEAM_LOTTERY_LOG] SET [status] = ?,[receive_date] = ? WHERE [username] = ? AND [ID] = ?",[1,logDate(),$username,$id]);
            $muOnline->commit();
            $this->web->commit();

            return alert('usercp/lotteryLog?state=1','物品领取成功！');

        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 获取夺宝稀有商店列表
     * @return array|bool|void|null
     * @throws Exception
     */
    public function getLotteryShop()
    {
        $reward = $this->web->query_fetch("SELECT * FROM [X_TEAM_LOTTERY_SHOP] ORDER BY [ID] ASC");
        if($reward) return $reward;
        return;
    }

    /**
     * 插入中奖名单
     * @param $group
     * @param $username
     * @param $Win_code
     * @return bool
     * @throws Exception
     */
    public function setLotteryLog($group, $username, $Win_code)
    {
        if(!check_value($group)) return false;
        if(!check_value($username)) return false;
        if(!check_value($Win_code)) return false;
        $data = [
            'servercode' => getServerCodeForGroupID($group),
            'username'  => $username,
            'win_code'  => $Win_code,
            'date' => logDate()
        ];
        $SQL = $this->web->query("INSERT INTO [X_TEAM_LOTTERY_LOG] ([servercode],[username],[win_code],[date]) VALUES (:servercode, :username ,:win_code, :date)",$data);
        if($SQL) return true;
        return false;
    }


    /**
     * 获取抽奖列表
     * @param bool $where
     * @param string $username
     * @param int $show
     * @return array|bool|void|null
     * @throws Exception
     */
    public function getLotteryLog($where = true, $username = '',$show = 0)
    {
        if($where){
            switch ($show){
                case 1 :
                    if(!Validator::UnsignedNumber($show)) throw new Exception('请求错误!');
                    $status = ' AND [status] = 0';
                    break;
                case 2:
                    if(!Validator::UnsignedNumber($show)) throw new Exception('请求错误!');
                    $status = ' AND [status] = 1';
                    break;
                default:
                    $status = '';
                    break;
            }
            if(!check_value($username)) return;
            $query  = "SELECT * FROM [X_TEAM_LOTTERY_LOG] WHERE [username] = '".$username."'".$status."";
        }else{
            $query  = "SELECT * FROM [X_TEAM_LOTTERY_LOG]";
        }

        $SELECT = $this->web->query_fetch($query);
        if(!is_array($SELECT)) return;
        return $SELECT;
    }

    /**
     * 获取水晶数量
     * @param $username
     * @return bool|int|void
     */
    public function getCountLotteryLog($username)
    {
        if(!check_value($username)) return ;
        $SELECT = $this->web->query_fetch_single("SELECT COUNT(*) AS Crystal FROM [X_TEAM_LOTTERY_LOG] WHERE [win_code] = '水晶' AND [status] = 0 AND [username] = ?",[$username]);
        if (!is_array($SELECT)) return 0;
        return $SELECT['Crystal'];
    }

    /**
     * 获取幸运值
     * @param $username
     * @return bool|int|void
     */
    public function getCountLuckLotteryLog($username)
    {
        if(!check_value($username)) return 0;
        $SELECT = $this->web->query_fetch_single("SELECT COUNT(*) AS Luck FROM [X_TEAM_LOTTERY_LOG] WHERE [username] = ?",[$username]);
        if (!is_array($SELECT)) return 0;

        if($SELECT['Luck'] >= 100){
//            $this->web->query("UPDATE [X_TEAM_LOTTERY_LOG] SET [status] = ? WHERE [username] = ? AND [win_code] = '水晶'",[1,$username]);
            #大于等于100
            return $SELECT['Luck'] % 100;
        }
        return $SELECT['Luck'];
    }

    /**
     * 返回余额
     * @param $group
     * @param $username
     * @param $many
     * @return mixed
     * @throws Exception
     */
    public function setCredit($group, $username,$many = false)
    {
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId((int)$this->tConfig['credit_type']);
        $configSettings = $creditSystem->showConfigs(true);
        $price = $many ? $this->tConfig['many_price'] : $this->tConfig['price'];
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

        # 删减积分
        $creditSystem->subtractCredits($group, $price);
        $credit = 0;
        $credit = $creditSystem->getCredits($group);
        return $credit;
    }

    /**
     * 获取货币类型
     * @param $PriceType
     * @return mixed|string
     * @throws Exception
     */
    public function getPriceType($PriceType){
        $creditSystem = new CreditSystem();
        $creditConfigList = $creditSystem->showConfigs();
        if(is_array($creditConfigList)) {
            foreach($creditConfigList as $myCredits) {
                if($PriceType==$myCredits['config_id']){
                    return $myCredits['config_title'];
                }
            }
        }
        return '未知';
    }
/**********************************************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载在线抽奖插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载在线抽奖插件模块。');
		if(!@include_once(__PATH_LOTTERY_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载在线抽奖插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_LOTTERY_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

    /**
     * 加载配置文件
     * @param string $file
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($file='config')
    {
        $xmlPath = __PATH_LOTTERY_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('在线抽奖配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载在线抽奖插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}