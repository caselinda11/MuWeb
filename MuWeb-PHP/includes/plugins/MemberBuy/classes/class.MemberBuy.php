<?php
/**
 * [会员升级]类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Connection;
use Validator;
use common;
use Token;
use CreditSystem;

class MemberBuy {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common,$creditSystem;

    /**
     * 构造函数
     * MemberBuy constructor.
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

        $this->creditSystem = new CreditSystem();
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
    public function setMemberBuy($group, $username)
    {
        if(!Token::checkToken('MemberBuy')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('您的账号已在线，请断开连接。');
        $level = $this->getMemberLevel($group, $username);
        //if(empty($level)) throw new Exception('出错了，请您重新输入！');
        if($level >= $this->config['max_level']) throw new Exception('您当前会员等级已达到最大等级，无法购买。');
        #下一级
        $nextLevel = ($level >= $this->config['max_level']) ? $this->config['max_level'] : $level + 1;

        if($this->serverFiles == 'igcn') if(!$this->initialization($group, $username)) throw new Exception('会员初始化失败，请联系在线客服。');

        $data = Connection::Database("Web")->query_fetch_single("SELECT * FROM [X_TEAM_MEMBER_BUY] WHERE [vip_code] = ?",[$nextLevel]);
        #-减积分-------------------------------------------
        $this->creditSystem->setConfigId($data['price_type']);
        $configSettings = $this->creditSystem->showConfigs(true);
        switch ($configSettings['config_user_col_id']) {
            case 'username':
                $this->creditSystem->setIdentifier($username);
                break;
            case 'userid':
                $this->creditSystem->setIdentifier($_SESSION['userid']);
                break;
            case 'character':
                $this->creditSystem->setIdentifier($_SESSION['character']);
                break;
            default:
                throw new Exception("[货币系统]无效的标识符。");
        }
        $this->creditSystem->subtractCredits($group,$data['price']);
        #-------------------------------------------------
        #操作等级
        if(!$this->setMemberLevel($group,$username,$nextLevel)) throw new Exception('操作失败，请确保数据的正确性！');
        if($this->config['log']){
            Connection::Database("Web")->query("INSERT INTO [X_TEAM_MEMBER_BUY_LOG] ([AccountID],[Servercode],[buy_id],[price],[price_type],[Date]) VALUES (?,?,?,?,?,?)",[$username,getServerCodeForGroupID($group),$data['ID'],$data['price'],$data['price_type'],logDate()]);
        }

        alert('usercp/Member',"会员升级成功，请点击确定将回到主界面。");
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
        switch ($this->serverFiles){
            case "igcn":
                $level = Connection::Database("Me_MuOnline",$group)->query("UPDATE [T_VIPList] SET [Type] = ? WHERE [AccountID] = ?",[$nextLevel,$username]);
                break;
            case "muemu":
                $level = Connection::Database("Me_MuOnline",$group)->query("UPDATE ["._TBL_MI_."] SET [AccountLevel] = ? WHERE ["._CLMN_USERNM_."] = ?",[$nextLevel,$username]);
                break;
            default:
                $level = Connection::Database("Me_MuOnline",$group)->query("UPDATE ["._TBL_MI_."] SET [vip] = ? WHERE ["._CLMN_USERNM_."] = ?",[$nextLevel,$username]);
                break;
        }
        if(!$level) return 0;
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
     * @param string $where
     * @return array|bool|mixed|void|null
     */
    public function getMemberBuyList($where = '')
    {
        if($where){
            $data = $this->web->query_fetch_single("SELECT * FROM [X_TEAM_MEMBER_BUY] WHERE [ID] = ?",[$where]);
        }else{
            $data = $this->web->query_fetch("SELECT * FROM [X_TEAM_MEMBER_BUY]");
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
		if(!Validator::Alpha($module)) throw new Exception('无法加载[会员升级]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[会员升级]插件模块。');
		if(!@include_once(__PATH_MEMBER_BUY_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[会员升级]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_MEMBER_BUY_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_MEMBER_BUY_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[会员升级]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[会员升级]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}