<?php
/**
 * [货币转换]类相关函数
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

class changeCredit {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common,$creditSystem;

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
        #货币函数
        $this->creditSystem = new CreditSystem();
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */

    /**
     * @return string
     * @throws Exception
     */
    public function changeCreditPoint()
    {
        if (!Token::checkToken('changeCredit')) throw new Exception('[1]出错了，请您重新输入！');
        if (!check_value($_SESSION['group'])) throw new Exception('[ERROR]出错了，请您重新登陆！');
        if (!check_value($_SESSION['username'])) throw new Exception('[ERROR]出错了，请您重新登陆！');
        if($this->config['check_online']) if ($this->common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) throw new Exception('您的账号已在线，请断开连接。');
        if (!check_value($_REQUEST['credit_old'])) throw new Exception('[2]货币选择错误，请重新提交。');
        if (!check_value($_REQUEST['credit_new'])) throw new Exception('[3]货币选择错误，请重新提交。');
        if (!check_value($_REQUEST['number'])) throw new Exception('[4]请输入您需要转换的货币数量。');
        if (!Validator::UnsignedNumber($_REQUEST['number'])) throw new Exception('[5]货币数量必须是纯数字。');
        if ($_REQUEST['number'] < $this->config['min_price'] || $_REQUEST['number'] > $this->config['max_price']) throw new Exception('[6]每次使用该功能的金额必须在['.$this->config['min_price'].'~'.$this->config['max_price'].']之间。');
        if($_REQUEST['credit_old'] == $_REQUEST['credit_new']) throw new Exception('[7]相同货币不支持转换，请重新选择。');
        $id = $_REQUEST['credit_old'] == 1 ? explode(':',$this->config['price_1']) : explode(':',$this->config['price_2']);
        if (!is_array($id)) throw new Exception('[8]配置错误，请联系在线客服。');
        #验证余额是否足够
        $balance = $this->getAccountCreditPoint($_REQUEST['credit_old']);
        if($balance - ($_REQUEST['number']*$id[0]) < 0)  throw new Exception('[9]您输入的额度大于您要转换的余额，请您重新输入。');
        $name1 = $_REQUEST['credit_old'] == 1 ? $this->config['name_1'] : $this->config['name_2'];
        $name2 = $_REQUEST['credit_new'] == 1 ? $this->config['name_1'] : $this->config['name_2'];
        $muOnline = Connection::Database("MuOnline",$_SESSION['group']);
        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            $muOnline->query("UPDATE [".$this->config['table_'.$_REQUEST['credit_old']]."] SET [".$this->config['table_column_'.$_REQUEST['credit_old']]."] = ".$this->config['table_column_'.$_REQUEST['credit_old']]." - ".($_REQUEST['number']*$id[0])." WHERE ".$this->config['table_id_'.$_REQUEST['credit_old']]." = ?",[$_SESSION['username']]);
            $muOnline->query("UPDATE [".$this->config['table_'.$_REQUEST['credit_new']]."] SET [".$this->config['table_column_'.$_REQUEST['credit_new']]."] = ".$this->config['table_column_'.$_REQUEST['credit_new']]." + ".($_REQUEST['number']*$id[1])." WHERE ".$this->config['table_id_'.$_REQUEST['credit_new']]." = ?",[$_SESSION['username']]);
            if($this->config['log']) {
                $this->web->query("INSERT INTO [X_TEAM_CHANGE_CREDIT_LOG] ([AccountID],[servercode],[old_credit],[new_credit],[credit],[date]) VALUES (?, ?, ?, ?, ?, ?)",[$_SESSION['username'],getServerCodeForGroupID($_SESSION['group']),$name1,$name2,$_REQUEST['number'],logDate()]);
            }
            $muOnline->commit();
            $this->web->commit();
        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }
        return alert('usercp/changeCredit','恭喜您，您已成功将['.($_REQUEST['number']*$id[0]).']'.$name1.'转换为['.($_REQUEST['number']*$id[1]).']'.$name2.'，点击确定回到主界面。');
    }

    /**
     * 获取货币额度
     * @param $id
     * @return int
     * @throws Exception
     */
    public function getAccountCreditPoint($id)
    {
        if (!check_value($_SESSION['group'])) throw new Exception('[ERROR]出错了，请您重新登陆！');
        if (!check_value($_SESSION['username'])) throw new Exception('[ERROR]出错了，请您重新登陆！');
        $data = Connection::Database("MuOnline",$_SESSION['group'])->query_fetch_single("SELECT [".$this->config['table_column_'.$id]."] FROM [".$this->config['table_'.$id]."] WHERE ".$this->config['table_id_'.$id]." = ?",[$_SESSION['username']]);
        if(!is_array($data)) return 0;
        return $data[$this->config['table_column_'.$id]];
    }
    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('[货币转换]该模块不存在。');
		if(!$this->_moduleExists($module)) throw new Exception('[货币转换]该模块不存在。');
		if(!@include_once(__PATH_CHANGE_CREDIT_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('[货币转换]该模块不存在。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_CHANGE_CREDIT_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_CHANGE_CREDIT_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[货币转换]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('[货币转换]无法读取配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}