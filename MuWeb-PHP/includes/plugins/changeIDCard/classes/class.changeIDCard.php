<?php
/**
 * [changeIDCard]类相关函数
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

class changeIDCard {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;

    /**
     * 构造函数
     * changeIDCard constructor.
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
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */

    /**
     * @param $group
     * @param $username
     * @param $old_id
     * @param $new_id
     * @throws Exception
     */
    public function setChangeIDCard($group, $username, $old_id, $new_id)
    {
        if(!Token::checkToken('changeIDCard')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($old_id)) throw new Exception('请正确输入旧身份证号码后七位。');
        if(!$this->common->checkLevel2Password($group,$username,$old_id)) throw new Exception('身份证后七位错误，请重新输入。');
        if(!check_value($new_id)) throw new Exception('请正确输入新身份证号码，由15-18位组成。');
        if(!Validator::isValid($new_id)) throw new Exception('新身份证号不正确，请重新输入。');
        if(!Validator::is_adult18($new_id)) throw new Exception('检测到您未满18周岁，禁止操作。');
        $me_muOnline = Connection::Database("Me_MuOnline",$group);
        try{
            $me_muOnline->beginTransaction();
            $this->web->beginTransaction();
            $me_muOnline->query("UPDATE [MEMB_INFO] SET [sno__numb] = ? ,[addr_info] = ? ,[addr_deta] = ? WHERE [servercode] =? AND [memb___id] = ?",[$new_id,$new_id,$new_id,getServerCodeForGroupID($group),$username]);
            $this->web->query("INSERT INTO [X_TEAM_CHANGE_ID_CARD_LOG] ([AccountID],[servercode],[OLD_ID],[NEW_ID],[date]) VALUES (?,?,?,?,?)",[$username,$group,$old_id,$new_id,logDate()]);
            $me_muOnline->commit();
            $this->web->commit();
        }catch (Exception $exception){
            $me_muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }
        alert('usercp/myaccount','恭喜您身份证号码修改成功，点击确定返回用户面板。');
    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[身份证修改]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[身份证修改]插件模块。');
		if(!@include_once(__PATH_CHANGE_ID_CARD_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[身份证修改]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_CHANGE_ID_CARD_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_CHANGE_ID_CARD_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[身份证修改]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[身份证修改]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}