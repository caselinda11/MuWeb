<?php
/**
 * [CreateClass]类相关函数
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
use Character;

class CreateClass {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;

    /***
     * 构造函数
     * CreateClass constructor.
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
     * @param $char_class
     * @param $char_name
     * @throws Exception
     */
    public function setCreateNewClass($group, $username, $char_class, $char_name)
    {
        if(!Token::checkToken('CreateClass')) throw new Exception('出错了，请您重新输入！');
        if (!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if (!check_value($username)) throw new Exception('出错了，请您重新输入！');
        global $custom;
        if (!array_key_exists($char_class,$custom['character_class'])) throw new Exception('出错了，请您重新输入！');
        if (!check_value($char_name)) throw new Exception("角色名不可为空，请正确输入角色名。");
        if (!Validator::ChineseCharacter($char_name)) throw new Exception("角色名不可包含特殊符号，且长度为2~5个中文字符。");
        $char = new Character();
        #验证名字是否重复
        if($char->_checkCharacterExists($group,$char_name)) throw new Exception("非常抱歉，该角色名：[".$char_name."]已存在，请尝试使用其他角色名。");

        #确认创建什么角色
        switch ($char_class){
            case 48:#魔剑
                $classLevelReq = $this->config['mg_level_req'];
                $create_price = $this->config['mg_credit_price'];
                $price_type = $this->config['mg_credit_type'];
                break;
            case 64:#圣导
                $classLevelReq = $this->config['dl_level_req'];
                $create_price = $this->config['dl_credit_price'];
                $price_type = $this->config['dl_credit_type'];
                break;
            default:
                throw new Exception('[!]'.'出错了，请您重新输入！');
            break;
        }

        #验证账号是否有足够位置储存新的角色
        $check_char = $char->getAccountCharacterNameForAccount($group, $username);
        if(!is_array($check_char)) throw new Exception("您还是一个新的账号，无法使用该功能。");
        $accountDataKey = array_search('',$check_char);
        if(!$accountDataKey) throw new Exception('[!]'."您没有足够的位置储存新角色！");

        #验证账号是否有角色已经达到等级要求
        $levelReq = false;
        foreach (array_filter($check_char) as $charData){
            $data = $char->getCharacterDataForCharacterName($group,$charData);
            $level = $data['cLevel'] + $data['mLevel'];
            if($level >= $classLevelReq){
                $levelReq = true;
                break;
            }
        }
        if(!$levelReq) throw new Exception("您的现有角色中未能达到创建该职业的等级要求，请满足条件再来。");

        #扣除积分
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($price_type);
        $configSettings = $creditSystem->showConfigs(true);
        $muOnline = Connection::Database("MuOnline");

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
                    $creditSystem->setIdentifier($_SESSION['character']);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group, $create_price);

            #添加角色
            //$muOnline->exec("UPDATE [AccountCharacter] SET ".$accountDataKey." = ? WHERE Id = ?",[$char_name,$username]);
            $muOnline->query("DECLARE @RC int DECLARE @AccountID varchar(10) DECLARE @Name varchar(10) DECLARE @Class tinyint EXECUTE @RC = [WZ_CreateCharacter] @AccountID = ?,@Name = ?,@Class	= ?",[$username,$char_name,$char_class]);

            if ($this->config['log']) {
                $price = $create_price.getPriceType($price_type);
                $this->web->query("INSERT INTO [X_TEAM_CREATE_CLASS_LOG]([username],[create_name],[create_class],[servercode],[create_price],[date])  VALUES (?, ?, ?, ?, ?, ?)", [$username,$char_name,$char_class,getServerCodeForGroupID($group),$price,logDate()]);
            }
            $muOnline->commit();
            $this->web->commit();
            alert('usercp/CreateClass', '恭喜您角色创建成功，点击确定将返回账号主界面！');
        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param $group
     * @param $username
     * @throws Exception
     */
    private function _checkAccountSlot($group, $username)
    {
        if(!check_value($group)) return;
        if(!check_value($username)) return;

    }
    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[角色创建]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[角色创建]插件模块。');
		if(!@include_once(__PATH_CREATE_CLASS_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[角色创建]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_CREATE_CLASS_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_CREATE_CLASS_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[角色创建]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[角色创建]插件类库。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}