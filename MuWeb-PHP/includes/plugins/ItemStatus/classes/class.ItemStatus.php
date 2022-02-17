<?php
/**
 * 角色铸造类相关函数
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
use creditSystem;
use common;
use Token;

class ItemStatus {

	private $_modulesPath = 'modules';
    private $serverFiles;
    private $creditSystem,$common;
    private $config,$Web;

    /**
     * 构造函数
     * WCoinStatus constructor.
     * @throws Exception
     */
	public function __construct()
    {
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        $this->creditSystem = new creditSystem();
        $this->config = $this->loadConfig();
        $this->common = new common();
        $this->Web = Connection::Database('Web');
    }

    /**
     *
     * @param $group
     * @param $username
     * @param $char_name
     * @param $point
     * @throws Exception
     */
    public function setWCoinStatus($group, $username, $char_name, $point)
    {
        if(!check_value($group)) throw new Exception('提交失败，请重新操作。');
        if(!check_value($username)) throw new Exception('提交失败，请重新操作。');
        if(!check_value($char_name)) throw new Exception('提交失败，请重新操作。');
        if(!Token::checkToken('ItemStatus')) throw new Exception('出错了，请您重新输入！');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('您的账号已在线，请断开连接。');
        $data = $this->getCharStatus($group,$username,$char_name);
        if (!check_value($data)) throw new Exception('提交失败，请重新操作。');
        if($data['cLevel'] > $this->config['min_level']) throw new Exception('当前角色等级低于'.$this->config['min_level'].'级，不能锻造属性点！');

        if($data['Point1'] < $this->config['points_1']){
            if(($point+$data['Point1']) > $this->config['points_1']) throw new Exception('铸造点数不得超过[1]阶段最大点数('.$this->config['points_1'].'),请重新输入!');
            $Stage = 1;
            $query = "Point1 = Point1 + ".$point;
        }
        else if($data['Point2'] < $this->config['points_2']){
            if(($point+$data['Point2']) > $this->config['points_2']) throw new Exception('铸造点数不得超过[2]阶段最大点数('.$this->config['points_2'].'),请重新输入!');
            $Stage = 2;
            $query = "Point2 = Point2 + ".$point;
        }
        else if($data['Point3'] < $this->config['points_3']){
            if(($point+$data['Point3']) > $this->config['points_3']) throw new Exception('铸造点数不得超过[3]阶段最大点数('.$this->config['points_3'].'),请重新输入!');
            $Stage = 3;
            $query = "Point3 = Point3 + ".$point;
        }
        else if($data['Point4'] < $this->config['points_4']){
            if(($point+$data['Point4']) > $this->config['points_4']) throw new Exception('铸造点数不得超过[4]阶段最大点数('.$this->config['points_4'].'),请重新输入!');
            $Stage = 4;
            $query = "Point4 = Point4 + ".$point;
        }
        else if($data['Point5'] < $this->config['points_5']){
            if(($point+$data['Point5']) > $this->config['points_5']) throw new Exception('铸造点数不得超过[5]阶段最大点数('.$this->config['points_5'].'),请重新输入!');
            $Stage = 5;
            $query = "Point5 = Point5 + ".$point;
        }else{
            throw new Exception('该角色已完成了所有阶段，请选择其他角色!');
        }
        #仓库所需物品数量
        $ware = new warehouse($group, $username);
        $itemCount = $ware->getWarehouseItemNumberForItem($this->config['item_'.$Stage]);
        if($itemCount < ($this->config['item_number_'.$Stage]*$point)) throw new Exception("操作失败，您仓库中的".$this->config['item_name_'.$Stage]."不足支付此笔费用。");
        if (!$ware->deleteWarehouseItemForItem($this->config['item_'.$Stage],$this->config['item_number_'.$Stage]*$point)) throw new Exception("操作失败，请确保仓库中有足够物品数量。");
        $newWarehouse = join("",$ware->warehouseList).$ware->extendWarehouseData;
        $muOnline = Connection::Database("MuOnline",$group);
        try {
            $muOnline->beginTransaction();
            $this->Web->beginTransaction();
            $muOnline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$newWarehouse,$username]);
            $muOnline->query("update [Character] set [LevelUpPoint] = LevelUpPoint+".$point.",".$query." WHERE [AccountID] = ? AND [Name] = ?",[$username,$char_name]);

            #写入日志
            if($this->config['log']) {
                $this->Web->query("INSERT INTO [X_TEAM_ITEMSTATUS_LOG] ([AccountID],[Servercode],[Name],[Stage],[item],[item_number],[Date]) VALUES (?,?,?,?,?,?,?)",[$username,getServerCodeForGroupID($group),$char_name,$Stage,$this->config['item_name_'.$Stage],$this->config['item_number_'.$Stage],logDate()]);
            }

            $muOnline->commit();
            $this->Web->commit();
            alert('usercp/ItemStatus','恭喜您角色铸造成功，点击确定将返回主界面！');

        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->Web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }



    /**
     * 获取角色铸造数据
     * @param $group
     * @param $username
     * @param $char_name
     * @return mixed|null
     * @throws Exception
     */
    public function getCharStatus($group,$username,$char_name = '')
    {
        if($char_name){
            $data = Connection::Database('MuOnline',$group)->query_fetch_single("SELECT [cLevel],[Point1],[Point2],[Point3],[Point4],[Point5] FROM [Character] WHERE [CtlCode] = 0 AND [AccountID] = ? AND [Name] = ?",[$username,$char_name]);
        }else{
            $data = Connection::Database('MuOnline',$group)->query_fetch("SELECT [Name],[Point1],[Point2],[Point3],[Point4],[Point5] FROM [Character] WHERE [CtlCode] = 0 AND [AccountID] = ?",[$username]);
        }

        if(!is_array($data)) return null;
        return $data;
    }

    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载角色铸造插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载角色铸造插件模块。');
		if(!@include_once(__PATH_ITEM_STATUS_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载角色铸造插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_ITEM_STATUS_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_ITEM_STATUS_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('角色铸造配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载角色铸造插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}