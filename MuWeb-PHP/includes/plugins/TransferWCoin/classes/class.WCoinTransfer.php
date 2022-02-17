<?php
/**
 * [WCoinTransfer]类相关函数
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

class WCoinTransfer {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;

    /**
     * 构造函数
     * WCoinTransfer constructor.
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
     * 发送货币
     * @param $group
     * @param $username
     * @param $receive_username
     * @param $creditType
     * @param $creditPrice
     * @throws Exception
     */
    public function setWCoinTransfer($group, $username, $receive_username, $creditPrice, $creditType)
    {
        if(!Token::checkToken('TransferWCoin')) throw new Exception('123');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($receive_username)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($creditType)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($creditPrice)) throw new Exception('出错了，请您重新输入！');
        if(!$this->common->_checkUsernameExists($group,$receive_username)) throw new Exception("接受方账号错误，请重新输入。");
        if($username == $receive_username) throw new Exception("操作失败，您不能转移给自己。");
        if($this->common->checkUserOnline($group,$username)) throw new Exception("检测到您的账号在线，请断开连接再尝试。");
        if($this->common->checkUserOnline($group,$receive_username)) throw new Exception("检测到接受方账号在线，请联系对方断开连接再尝试。");

        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($creditType);
        $configSettings = $creditSystem->showConfigs(true);

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
        $creditSystem->subtractCredits($group,$creditPrice);

        if($this->config['log']) {
            $this->web->query("INSERT INTO [X_TEAM_WCOIN_TRANSFER_LOG] ([send_username],[servercode],[credit_price],[credit_type],[receive_username],[date]) VALUES (?,?,?,?,?,?)", [$username, getServerCodeForGroupID($group), $creditType, $creditPrice, $receive_username, logDate()]);
        }

        #增加积分
        switch ($configSettings['config_user_col_id']) {
            case 'username':
                $creditSystem->setIdentifier($receive_username);
                break;
            default:
                throw new Exception("[货币系统]无效的标识符。");
        }
        $creditSystem->addCredits($group,$creditPrice);
        alert('usercp/TransferWCoin','恭喜您'.getPriceType($creditType).'转移成功，'.getPriceType($creditType).'已转移至对方账号，点击确定回到主界面！');
    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[货币转移]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[货币转移]插件模块。');
		if(!@include_once(__PATH_WCOIN_TRANSFER_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[货币转移]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_WCOIN_TRANSFER_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_WCOIN_TRANSFER_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[货币转移]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[货币转移]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}