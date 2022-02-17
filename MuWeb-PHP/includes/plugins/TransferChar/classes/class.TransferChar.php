<?php
/**
 * [CharTransfer]类相关函数
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
use Character;
use CreditSystem;
class TransferChar {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;

    /**
     * 构造函数
     * CharTransfer constructor.
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
     * 发送角色
     * @param $group
     * @param $username
     * @param $char_name
     * @param $receive_char
     * @throws Exception
     */
    public function setCharacterTransfer($group, $username, $char_name, $receive_username)
    {
        if(!Token::checkToken('CharTransfer')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($char_name)) throw new Exception("操作失败，您没有可以转移的角色。");
        if(!check_value($receive_username)) throw new Exception('出错了，请您重新输入！');
        if(!$this->common->_checkUsernameExists($group,$receive_username)) throw new Exception('接收方账号错误，请重新输入。');
        if($username == $receive_username) throw new Exception("操作失败，您不能转移角色给自己。");
        if($this->common->checkUserOnline($group,$username)) throw new Exception("检测到您的账号在线，请断开连接再尝试。");
        if($this->common->checkUserOnline($group,$receive_username)) throw new Exception("检测到接受方账号在线，请联系对方断开连接再尝试。");
        #获取角色信息
        $Character = new Character();
        //验证是否有指定职业
        $accountCount= $Character->getCharacterDataForCharacterName($group,$char_name);
        if(!is_array($accountCount)) throw new Exception('出错了，请您重新输入！');
        foreach ($accountCount as $key=>$item){
            $countLevel = $item['cLevel'] + $item['mLevel'];
            if($char_name == $item['Name']){
                if($countLevel < $this->config['min_level'] || $countLevel > $this->config['max_level']) throw new Exception("要转移的角色必须达到[".$this->config['min_level']."~".$this->config['max_level']."]级之间才可使用该功能!!");
            }
        }

        //限制角色次数
        $transferCount = $this->web->query_fetch_single("SELECT COUNT(*) as transferCount FROM [X_TEAM_CHAR_TRANSFER_LOG] WHERE [send_Name] = ? AND [servercode] = ?",[$char_name,getGroupIDForServerCode($group)]);
        if (is_array($transferCount)) if($transferCount['transferCount']) if($transferCount['transferCount'] >= $this->config['transfer_cont']) throw new Exception("该角色的交易次数已达上线。");

        #发送方数据获取
        $SendCharacterData = $Character->getAccountCharacterNameForAccount($group,$username);
        if(!is_array($SendCharacterData)) throw new Exception('[1] - '.'出错了，请您重新输入！');
        $sendAccountDataKey = array_search($char_name,$SendCharacterData);
        if(!$sendAccountDataKey) throw new Exception('[2]'.'出错了，请您重新输入！');
        #接收方数据获取
        $receiveCharacterData = $Character->getAccountCharacterNameForAccount($group,$receive_username);
        if(!is_array($receiveCharacterData)) throw new Exception('[3] - 接收方还是一个新号，请通知对方创建一个角色后再来。');
        $receiveAccountDataKey = array_search('',$receiveCharacterData);
        if(!$receiveAccountDataKey) throw new Exception('[4]'.'出错了，请您重新输入！');
        $muOnline = Connection::Database("MuOnline",$group);
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($this->config['credit_type']);
        $configSettings = $creditSystem->showConfigs(true);
        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            #减积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($username);
                    break;
                case 'userid':
                    $creditSystem->setIdentifier($_SESSION['userid']);
                    break;
                case 'character':
                    $creditSystem->setIdentifier($char_name);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group,$this->config['credit_price']);

            $muOnline->query("UPDATE [AccountCharacter] SET ".$sendAccountDataKey." = ? WHERE Id = ?",[null,$username]);
            $muOnline->query("UPDATE [AccountCharacter] SET ".$receiveAccountDataKey." = ? WHERE Id = ?",[$char_name,$receive_username]);
            $muOnline->query("UPDATE [Character] SET [AccountID] = ? WHERE [AccountID] = ? AND [Name] = ?",[$receive_username,$username,$char_name]);

            if($this->config['log']) {
                $this->web->query("INSERT INTO [X_TEAM_CHAR_TRANSFER_LOG] ([send_username],[send_Name],[servercode],[receive_username],[date]) VALUES (?,?,?,?,?)", [$username, $char_name, getServerCodeForGroupID($group), $receive_username, logDate()]);
            }
            $muOnline->commit();
            $this->web->commit();
            alert('usercp/TransferChar','恭喜您角色转移成功，['.$char_name.']已转移至对方账号，点击确定回到主界面！');

        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }

    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[角色转移]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[角色转移]插件模块。');
		if(!@include_once(__PATH_CHAR_TRANSFER_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[角色转移]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_CHAR_TRANSFER_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_CHAR_TRANSFER_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[角色转移]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[角色转移]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}