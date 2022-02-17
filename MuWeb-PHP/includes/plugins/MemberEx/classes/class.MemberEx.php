<?php
/**
 * [兑换会员]类相关函数
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
use common;
use Token;
use CreditSystem;
use Plugin\equipment;

class MemberEx {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common,$creditSystem,$wareData,$NULL,$item;
    public $count = 0;#物品总数
    /**
     * 构造函数
     * MemberEx constructor.
     * @throws Exception
     */
	public function __construct()
    {
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        #网站库
        $this->web = Connection::Database("Web");
        #配置文件
        $this->config = $this->loadConfig();
        #基类函数
        $this->common = new common();
        #空物品值
        $this->NULL = str_pad("F",ITEM_SIZE,"F");
        $this->item = new equipment();
        $this->creditSystem = new CreditSystem();
        $this->getItemsCount($_SESSION['group']);
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */

    /***
     * @param $group
     * @param $username
     * @return int
     * @throws Exception
     */
    public function initialization($group, $username)
    {
        if(!check_value($group)) return 0;
        if(!check_value($username)) return 0;
        $data = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT [AccountID] FROM [T_VIPList] WHERE [AccountID] = ?",[$username]);
        if(!$data) Connection::Database("Me_MuOnline",$group)->query("INSERT INTO [T_VIPList] ([AccountID],[Date],[Type]) VALUES (?,?)",[$username,date('Y-m-d h:i:s',strtotime('+3650 day')),0]);
        return 1;
    }

    /**
     * @param $group
     * @param $username
     * @throws Exception
     */
    public function setMemberEx($group, $username)
    {
        if(!Token::checkToken('MemberEx')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('您的账号已在线，请断开连接。');
        $level = $this->getMemberLevel($group, $username);
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('您的账号已在线，请断开连接。');
        if($level >= $this->config['max_level']) throw new Exception('您当前会员等级已达到最大等级，无法购买。');
        #下一级
        $nextLevel = ($level >= $this->config['max_level']) ? $this->config['max_level'] : $level + 1;

        if($this->serverFiles == 'igcn') if(!$this->initialization($group, $username)) throw new Exception('会员初始化失败，请联系在线客服。');

        $data = Connection::Database("Web")->query_fetch_single("SELECT * FROM [X_TEAM_MEMBER_EXCHANGE] WHERE [vip_code] = ?",[$nextLevel]);

        if($this->count < $data['items_number']) throw new Exception("您所拥有的升级物品不足，请确保仓库中存有足够的物品数量。");
        $b = 0;
        for($i=0;$i<120;$i++){
            if($this->wareData[$i] == $this->NULL) continue;
            $items = $this->item->convertItem($this->wareData[$i]);
            if($items['type'] == $data['items_code']){
                $this->wareData[$i] = $this->NULL;
                $b++;
            }
            if($b == $data['items_number']) break;
        }

        #组装
        $newWarehouse = join("", $this->wareData);
        $muOnline = Connection::Database("MuOnline",$group);
        $Me_muOnline = Connection::Database("Me_MuOnline",$group);

        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            switch ($this->serverFiles){
                case "igcn":
                    $Me_muOnline->query("UPDATE [T_VIPList] SET [Type] = ? WHERE [AccountID] = ?",[$nextLevel,$username]);
                    break;
                case "muemu":
                    $Me_muOnline->query("UPDATE ["._TBL_MI_."] SET [AccountLevel] = ? WHERE ["._CLMN_USERNM_."] = ?",[$nextLevel,$username]);
                    break;
                default:
                    $Me_muOnline->query("UPDATE ["._TBL_MI_."] SET [vip] = ? WHERE ["._CLMN_USERNM_."] = ?",[$nextLevel,$username]);
                    break;
            }
            $muOnline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$newWarehouse,$username]);

            if($this->config['log']){
                $this->web->query("INSERT INTO [X_TEAM_MEMBER_EXCHANGE_LOG] ([AccountID],[Servercode],[buy_id],[items_code],[items_name],[items_number],[Date]) VALUES (?,?,?,?,?,?,?)",[$username,getServerCodeForGroupID($group),$data['ID'],$data['items_code'],$data['items_name'],$data['items_number'],logDate()]);
            }

            $muOnline->commit();
            $this->web->commit();
            alert('usercp/MemberEx',"会员升级成功，请点击确定将回到主界面。");

        }catch (Exception $exception){

            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }



    }

    /**
     * @param $group
     * @throws Exception
     */
    public function getItemsCount($group)
    {
        $ware = new warehouse($group);
        $this->wareData = $ware->warehouseList;
        if(!is_array($this->wareData)) throw new Exception("获取仓库失败!");
        foreach ($this->wareData as $list){
            if($list == $this->NULL) continue;
            $items = $this->item->convertItem($list);
            if($items['type'] == $this->config['items']) $this->count++;
        }
    }

    /**
     * @param $group
     * @param $username
     * @param $nextLevel
     * @return int
     * @throws Exception
     */
    public function setMemberLevel($group,$username,$nextLevel)
    {
        if(!check_value($group)) return 0;
        if(!check_value($username)) return 0;
        if(!check_value($nextLevel)) return 0;

        if(!$nextLevel) return 0;
        return 1;
    }
    /**
     * 获取当前会员等级
     * @param $group
     * @param $username
     * @return array|bool|void|null
     * @throws Exception
     */
    public function getMemberLevel($group, $username)
    {
        if(!check_value($group)) return 0;
        if(!check_value($username)) return 0;
        switch ($this->serverFiles){
            case "igcn":
                $level = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT [Type] as vip FROM [T_VIPList] WHERE [AccountID] = ?",[$username]);
                break;
            case "muemu":
                $level = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT [AccountLevel] as vip FROM ["._TBL_MI_."] WHERE ["._CLMN_USERNM_."] = ?",[$username]);
                break;
            default:
                $level = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT [vip] FROM ["._TBL_MI_."] WHERE ["._CLMN_USERNM_."] = ?",[$username]);
                break;
        }
        if(!is_array($level)) return 0;
        if(!check_value($level['vip'])) return 0;
        return $level['vip'];
    }

    /**
     * 获取配置列表
     * @param string $vip_code
     * @return array|bool|mixed|void|null
     */
    public function getMemberExList($vip_code = '')
    {
        if($vip_code){
            $data = $this->web->query_fetch_single("SELECT * FROM [X_TEAM_MEMBER_EXCHANGE] WHERE [vip_code] = ?",[$vip_code]);
        }else{
            $data = $this->web->query_fetch("SELECT * FROM [X_TEAM_MEMBER_EXCHANGE]");
        }
        if(!$data) return;
        return $data;
    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[兑换会员]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[兑换会员]插件模块。');
		if(!@include_once(__PATH_MEMBER_EXCHANGE_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[兑换会员]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_MEMBER_EXCHANGE_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_MEMBER_EXCHANGE_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[兑换会员]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[兑换会员]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}