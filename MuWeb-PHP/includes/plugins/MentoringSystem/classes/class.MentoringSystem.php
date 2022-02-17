<?php
/**
 * [师徒系统]类相关函数
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
use creditSystem;
use Character;

class MentoringSystem {

    private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;

    /**
     * 构造函数
     * myPlugin constructor.
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
     * @param $master
     * @return string
     * @throws Exception
     */
    public function Bind($master)
    {
        if(!Token::checkToken('MentoringSystem')) throw new Exception('提交错误，请重新输入。');
        if(!check_value($_SESSION['group'])) throw new Exception('提交错误，请重新输入。');
        if(!check_value($_SESSION['username'])) throw new Exception('提交错误，请重新输入。');
        if(!check_value($master)) throw new Exception('请正确输入师傅账号。');
        if(!Validator::UsernameLength($master)) throw new Exception('师傅账号错误，请正确输入。');
        if(!Validator::UsernameLength($master)) throw new Exception('师傅账号错误，请正确输入。');

        #验证当前账号师傅已经绑定
        if($this->checkUsernameBind($_SESSION['username'])) throw new Exception('检测到您当前账号已绑定师傅，如需换绑请先解除。');
        if($this->checkMasterBind($master) >= $this->config['frequency']) throw new Exception('该师傅账号已绑定['.$this->config['frequency'].']个徒弟，无法绑定。');
        if ($master == $_SESSION['username']) throw new Exception("您不可自己绑定自己，请重新输入。");
        if(!$this->common->_checkUsernameExists($_SESSION['group'],$master)) throw new Exception("该大区师傅账号不存在，请重新输入。");

        try{
            $this->web->beginTransaction();
            $this->web->query("INSERT INTO [X_TEAM_Mentoring_System_LOG] ([Master],[Servercode],[apprentice],[status],[Date]) VALUES (?,?,?,?,?)",[$master,getServerCodeForGroupID($_SESSION['group']),$_SESSION['username'],0,logDate()]);
            $this->web->commit();
        }catch (Exception $exception){
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }
        return alert('usercp/MentoringSystem',"您已成功递交申请，请您耐心等待师傅确认。<br>您可以主动联系师傅登陆网站确认，点击确定回到主界面。");
    }

    /**
     * 同意
     * @param $apprentice
     * @throws Exception
     */
    public function applyBind($apprentice)
    {
        if(!check_value($_SESSION['group'])) throw new Exception('[1]提交错误，请重新输入。');
        if(!check_value($_SESSION['username'])) throw new Exception('[2]提交错误，请重新输入。');
        if(!check_value($apprentice)) throw new Exception('[3]提交错误，请重新输入。');
        if(!Validator::UnsignedNumber($apprentice)) throw new Exception('徒弟账号错误，请正确输入。');
        if(!Validator::UsernameLength($apprentice)) throw new Exception('徒弟账号错误，请正确输入。');

        if(!$this->common->_checkUsernameExists($_SESSION['group'],$apprentice)) throw new Exception('[4]提交错误，请重新输入。');
        #检测徒弟是否已绑定
        if($this->checkUsernameBind($apprentice)) throw new Exception('该徒弟已拜其他人为师，无法再收Ta为徒。');
        #检测当前账号绑定次数
        if($this->checkMasterBind($_SESSION['username']) >= $this->config['frequency']) throw new Exception('您收徒已达最大上线，最多可收取['.$this->config['frequency'].']个徒弟。');

        $muonline = Connection::Database("Me_MuOnline",$_SESSION['group']);

        try{
            $this->web->beginTransaction();
            $muonline->beginTransaction();
            $muonline->query("UPDATE [MEMB_INFO] SET [shifu] = ? WHERE [memb___id] = ?",[$_SESSION['username'],$apprentice]);
            $this->web->query("UPDATE [X_TEAM_Mentoring_System_LOG] SET [status] = ? WHERE [status] = ? AND [Master] = ? AND [apprentice] = ?",[1,0,$_SESSION['username'],$apprentice]);
            $this->web->commit();
            $muonline->commit();
        }catch (Exception $exception){
            $this->web->rollBack();
            $muonline->rollBack();
            throw new Exception($exception->getMessage());
        }

        return alert('usercp/MentoringSystem',"恭喜您用户[".$apprentice."]成为您的徒弟，点击确定回到主界面。");
    }

    /**
     * 拒绝
     * @param $apprentice
     * @throws Exception
     */
    public function applyUnBind($apprentice)
    {
        if(!check_value($_SESSION['group'])) throw new Exception('[1]提交错误，请重新输入。');
        if(!check_value($_SESSION['username'])) throw new Exception('[2]提交错误，请重新输入。');
        if(!check_value($apprentice)) throw new Exception('[3]提交错误，请重新输入。');
        if(!Validator::UnsignedNumber($apprentice)) throw new Exception('徒弟账号错误，请正确输入。');
        if(!Validator::UsernameLength($apprentice)) throw new Exception('徒弟账号错误，请正确输入。');
        if(!$this->common->_checkUsernameExists($_SESSION['group'],$apprentice)) throw new Exception('[4]提交错误，请重新输入。');

        try{
            $this->web->beginTransaction();
            $this->web->query("DELETE FROM [X_TEAM_Mentoring_System_LOG] WHERE [Master] = ? AND [apprentice] = ?",[$_SESSION['username'],$apprentice]);
            $this->web->commit();
        }catch (Exception $exception){
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }

        return alert('usercp/MentoringSystem',"您已拒绝[".$apprentice."]成为您的徒弟，点击确定回到主界面。");
    }
    /**
     * @return array|bool|void|null
     * @throws Exception
     */
    public function getApplyList()
    {
        if(!check_value($_SESSION['group'])) throw new Exception('提交错误，请重新输入。');
        if(!check_value($_SESSION['username'])) throw new Exception('提交错误，请重新输入。');
        $data = $this->web->query_fetch("SELECT * FROM [X_TEAM_Mentoring_System_LOG] WHERE [Master] = ? AND [status] = ? ORDER BY Date DESC",[$_SESSION['username'],0]);
        if(!is_array($data)) return;
        return $data;
    }

    /**
     * 解除绑定
     * @return string
     * @throws Exception
     */
    public function unBind()
    {
        if(!Token::checkToken('MentoringSystem')) throw new Exception('提交错误，请重新输入。');
        if(!check_value($_SESSION['group'])) throw new Exception('提交错误，请重新输入。');
        if(!check_value($_SESSION['username'])) throw new Exception('提交错误，请重新输入。');
        if(!$this->checkUsernameBind($_SESSION['username'])) throw new Exception('当前账号未绑定师傅，无需解除绑定。');
        $muonline = Connection::Database("Me_MuOnline",$_SESSION['group']);
        $creditSystem = new creditSystem();
        $creditSystem->setConfigId($this->config['credit_type']);
        $configSettings = $creditSystem->showConfigs(true);
        try{
            $this->web->beginTransaction();
            $muonline->beginTransaction();
            #减积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($_SESSION['username']);
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
            $creditSystem->subtractCredits($_SESSION['group'],$this->config['credit_price']);

            $muonline->query("UPDATE [MEMB_INFO] SET [shifu] = ? WHERE [memb___id] = ?",[null,$_SESSION['username']]);

            $this->web->query("DELETE FROM [X_TEAM_Mentoring_System_LOG] WHERE [apprentice] = ?",[$_SESSION['username']]);

            $this->web->commit();
            $muonline->commit();
        }catch (Exception $exception){
            $this->web->rollBack();
            $muonline->rollBack();
            throw new Exception($exception->getMessage());
        }
        return alert('usercp/MentoringSystem',"恭喜您已经成功解绑，花费[<strong>".$this->config['credit_price']."</strong>]".getPriceType($this->config['credit_type'])."，点击确定回到主界面。");
    }

    /**
     * 解除绑定
     * @param $master
     * @return string
     * @throws Exception
     */
    public function unBindForMaster($master)
    {
        if(!check_value($_SESSION['group'])) throw new Exception('提交错误，请重新输入。');
        if(!check_value($_SESSION['username'])) throw new Exception('提交错误，请重新输入。');
        if(!check_value($master)) throw new Exception('徒弟账号错误，请重新提交。');
        if(!Validator::UnsignedNumber($master)) throw new Exception('徒弟账号错误，请正确输入。');
        if(!Validator::UsernameLength($master)) throw new Exception('徒弟账号错误，请正确输入。');
        $muonline = Connection::Database("Me_MuOnline",$_SESSION['group']);
        $creditSystem = new creditSystem();
        $creditSystem->setConfigId($this->config['credit_type']);
        $configSettings = $creditSystem->showConfigs(true);
        try{
            $this->web->beginTransaction();
            $muonline->beginTransaction();
            #减积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($_SESSION['username']);
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
            $creditSystem->subtractCredits($_SESSION['group'],$this->config['credit_price']);

            $muonline->query("UPDATE [MEMB_INFO] SET [shifu] = ? WHERE [memb___id] = ? AND [shifu] = ?",[null,$master,$_SESSION['username']]);

            $this->web->query("DELETE FROM [X_TEAM_Mentoring_System_LOG] WHERE [apprentice] = ? AND [Master] = ?",[$master,$_SESSION['username']]);

            $this->web->commit();
            $muonline->commit();
        }catch (Exception $exception){
            $this->web->rollBack();
            $muonline->rollBack();
            throw new Exception($exception->getMessage());
        }
        return alert('usercp/MentoringSystem',"恭喜您已经成功解绑，花费[<strong>".$this->config['credit_price']."</strong>]".getPriceType($this->config['credit_type'])."，点击确定回到主界面。");
    }

    /**
     * 获取所有徒弟
     * @return mixed|void
     * @throws Exception
     */
    public function getMentoringInfo()
    {
        if(!check_value($_SESSION['group'])) return;
        if(!check_value($_SESSION['username'])) return;
        $data = Connection::Database("Me_MuOnline",$_SESSION['group'])->query_fetch("SELECT [memb___id],[memb_guid] FROM [MEMB_INFO] WHERE [shifu] = ?",[$_SESSION['username']]);
        if(!is_array($data)) return;
        $character = new Character();
        $newData = [];
        foreach ($data as $key => $master){
            $new = $character->getCharacterDataForUsername($_SESSION['group'],$master['memb___id']);
            $newData[$master['memb___id']] = $new;
        }
        return $newData;
    }

    /**
     * 检测师傅绑定次数
     * @param $master
     * @return int|mixed
     * @throws Exception
     */
    public function checkMasterBind($master)
    {
        if(!check_value($_SESSION['group'])) return 0;
        if(!check_value($master)) return 0;
        $data = Connection::Database("Me_MuOnline",$_SESSION['group'])->query_fetch_single("SELECT count(*) as Master FROM [MEMB_INFO] WHERE [shifu] = ?",[$master]);
        if(is_array($data)) return $data['Master'];
        return 0;
    }

    /**
     * 从师傅获取徒弟列表
     * @param $master
     * @return mixed|null
     * @throws Exception
     */
    public function checkApprenticeBind($master)
    {
        if(!check_value($_SESSION['group'])) throw new Exception('校验错误，请重新登陆。');
        if(!check_value($master)) throw new Exception('校验错误，请重新登陆。');
        $data = Connection::Database("Me_MuOnline",$_SESSION['group'])->query_fetch_single("SELECT [memb___id] FROM [MEMB_INFO] WHERE [shifu] = ?",[$master]);
        if(is_array($data)) return $data['memb___id'];
        return 0;
    }

    /**
     * 从徒弟获取师傅列表
     * @param $apprentice
     * @return mixed|null
     * @throws Exception
     */
    public function checkUsernameBind($apprentice)
    {
        if(!check_value($_SESSION['group'])) throw new Exception('校验错误，请重新登陆。');
        if(!check_value($apprentice)) throw new Exception('校验错误，请重新登陆。');
        $data = Connection::Database("Me_MuOnline",$_SESSION['group'])->query_fetch_single("SELECT [shifu] FROM [MEMB_INFO] WHERE [memb___id] = ?",[$apprentice]);
        if(is_array($data)) return $data['shifu'];
        return 0;
    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
    public function loadModule($module) {
        if(!Validator::Alpha($module)) throw new Exception('[师徒系统]该模块不存在。');
        if(!$this->_moduleExists($module)) throw new Exception('[师徒系统]该模块不存在。');
        if(!@include_once(__PATH_MentoringSystem_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('[师徒系统]该模块不存在。');
    }

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
    private function _moduleExists($module) {
        if(!check_value($module)) return;
        if(!file_exists(__PATH_MentoringSystem_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_MentoringSystem_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[师徒系统]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('[师徒系统]无法读取配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}