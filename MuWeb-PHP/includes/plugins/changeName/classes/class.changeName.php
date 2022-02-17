<?php
/**
 * 在线改名类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Connection;
use Validator;
use CreditSystem;
use Token;
use common;
use Character;

class changeName {

	private $_modulesPath = 'modules';
    private $serverFiles;
    private $common,$creditSystem,$web,$Config;

    /**
     * 构造函数
     * changeName constructor.
     * @throws Exception
     */
	public function __construct()
    {
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        $this->Config = $this->loadConfig();
        $this->common = new common();
        $this->creditSystem = new CreditSystem();
        $this->web = Connection::Database('Web');
    }

    /**
     * 改名模块
     * @param $group
     * @param $username
     * @param $char_name
     * @param $newName
     * @throws Exception
     */
    public function setNewCharName($group,$username,$char_name,$newName)
    {
        if(!Token::checkToken('changeName')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if(!$char_name) throw new Exception('出错了，请您重新输入！');
        if(!$newName) throw new Exception('请正确输入角色名！');
        if(!Validator::ChineseCharacter($newName))  throw new Exception('角色名包含非法符号，请重新输入!');

        if ($this->common->checkUserOnline($group, $username)) throw new Exception('您的账号已在线，请断开连接。');

        $muOnline = Connection::Database("MuOnline",$group);

        #验证是否有人使用
        $checkName = $muOnline->query_fetch_single("SELECT Name FROM Character WHERE Name = ?",[$newName]);
        if(is_array($checkName)) throw new Exception('对不起，您要使用的新角色名称已经有人使用了！');

        #验证战盟
        $GuildData = $muOnline->query_fetch_single("SELECT * FROM "._TBL_GUILDMEMB_." WHERE ["._CLMN_GUILDMEMB_CHAR_."] = ?",[$char_name]);
        if(is_array($GuildData)) throw new Exception('请先退出战盟才可使用改名功能！');

        #获取账号角色表数据
        $Character = new Character();
        $AccountData = $Character->getAccountCharacterNameForAccount($group, $username);
        if(!is_array($AccountData)) throw new Exception('[1]操作失败，请联系在线客服。');
        $keys = array_search($char_name, $AccountData);
        if (empty($keys)) throw new Exception('[2]操作失败，请联系在线客服。');

        $this->creditSystem->setConfigId($this->Config['credit_type']);
        $configSettings = $this->creditSystem->showConfigs(true);

        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            #减积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $this->creditSystem->setIdentifier($username);
                    break;
                case 'userid':
                    $this->creditSystem->setIdentifier($_SESSION['userid']);
                    break;
                case 'character':
                    $this->creditSystem->setIdentifier($char_name);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $this->creditSystem->subtractCredits($group,$this->Config['credit_price']);

            $muOnline->query("UPDATE [Character] SET [Name] = ? WHERE [Name] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [OptionData] SET [Name] = ? WHERE [Name] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [T_FriendList] SET [FriendName] = ? WHERE [FriendName] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [T_FriendMail] SET [FriendName] = ? WHERE [FriendName] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [T_FriendMain] SET [Name] = ? WHERE [Name] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [T_WaitFriend] SET [FriendName] = ? WHERE [FriendName] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [T_QUEST_MONSTERKILL] SET [CHAR_NAME] = ? WHERE [CHAR_NAME] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [T_StorageBox] SET [CharacterName] = ? WHERE [CharacterName] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [T_MasterLevelSystem] SET [CHAR_NAME] = ? WHERE [CHAR_NAME] = ?",[$newName, $char_name]);
            $muOnline->query("UPDATE [AccountCharacter] SET [".$keys."] = ? WHERE [".$keys."] = ?",[$newName, $char_name]);

            if($this->Config['log']) {
                $this->web->query("INSERT INTO [X_TEAM_CHANGE_NAME_LOG] ([AccountID],[servercode],[OLD_NAME],[NEW_NAME],[date]) VALUES (?, ?, ?, ?, ?)",[$username,getServerCodeForGroupID($group),$char_name,$newName,logDate()]);
            }
            $muOnline->commit();
            $this->web->commit();
            alert('usercp/myaccount','恭喜您角色名修改成功，点击确定将返回账号主界面！');

        }catch (Exception $exception){

            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }

    }

    /**
     * 获取货币类型
     * @param $PriceType
     * @return mixed|string
     * @throws Exception
     */
    public function getPriceType($PriceType){

        $creditConfigList = $this->creditSystem->showConfigs();
        if(is_array($creditConfigList)) {
            foreach($creditConfigList as $myCredits) {
                if($PriceType==$myCredits['config_id']){
                    return $myCredits['config_title'];
                }
            }
        }
        return '未知';
    }
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载在线改名插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载在线改名插件模块。');
		if(!@include_once(__PATH_CHANGE_NAME_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载在线改名插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_CHANGE_NAME_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_CHANGE_NAME_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('在线改名配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载在线改名插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}