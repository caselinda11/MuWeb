<?php
/**
 * [MonthlyCard]类相关函数
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
class MonthlyCard {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config;
    private $creditSystem;
    /**
     * 构造函数
     * MonthlyCard constructor.
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
        $this->creditSystem = new CreditSystem();
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */

    /**
     * 购买月卡
     * @param $group
     * @param $username
     * @param $UID
     * @throws Exception
     */
    public function setBuyMonthlyCard($group, $username, $UID)
    {
        if(!check_value($group)) throw new Exception('提交失败，请重新操作。');
        if(!check_value($username)) throw new Exception('提交失败，请重新操作。');
        if(!check_value($UID)) throw new Exception('提交失败，请重新操作。');
        if(!Token::checkToken('MonthlyCard'.$UID)) throw new Exception('出错了，请您重新输入！');
        $data = $this->getMonthlyCardList($UID);
        if(!is_array($data)) throw new Exception('该月卡已不存在，请选择其他月卡购买。');
        $check = $this->getMonthlyCardBuyList($username);
        if(is_array($check)) throw new Exception('您已购买过月卡套餐，无法叠加购买。');

        #-减积分-------------------------------------------
        $this->creditSystem->setConfigId($data['credit_type']);
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

        $sql = $this->web->query("INSERT INTO [X_TEAM_MONTHLY_CARD] ([buy_id],[buy_username],[buy_day],[buy_date]) VALUES (?,?,?,?)",[$UID,$username,$data['day'],time()]);
        if($this->config['log']){
            $this->web->query("INSERT INTO [X_TEAM_MONTHLY_CARD_LOG] ([AccountID],[servercode],[project_name],[day],[price],[credit_type],[date]) VALUES (?,?,?,?,?,?,?)",[$username,getServerCodeForGroupID($group),$data['project_name'],$data['day'],$data['price'],$data['credit_type'],time()]);
        }
        if(!$sql) throw new Exception("操作失败，请确保数据的正确性。");
        alert('usercp/MonthlyCard','恭喜购买成功，点击确定返回主页面。');

    }

    /**
     * @param $group
     * @param $username
     * @throws Exception
     */
    public function setSalaryMonthlyCard($group, $username)
    {
        if(!check_value($group)) throw new Exception('提交失败，请重新操作。');
        if(!check_value($username)) throw new Exception('提交失败，请重新操作。');
        if(!Token::checkToken('SalaryCard')) throw new Exception('出错了，请您重新输入！');

        $data = $this->getMonthlyCardBuyList($username);

        if(!is_array($data)) throw new Exception("抱歉，您已经领取完该月卡无法再次使用。");
        $list = $this->getMonthlyCardList($data['buy_id']);
        if(!is_array($list)) throw new Exception("无法获取该套餐信息，请联系在线客服。");

        #验证时间
        if(!empty($data['next_date'])){
            //$toDay = strtotime(date("Y-m-d"),time());
            //$day = date("Y-m-d H:i:s",$toDay);//当日凌晨的时间戳
            //$nextToDay = strtotime(date("Y-m-d",strtotime("+1 day")),time());
            //$nextDay = date("Y-m-d H:i:s",$nextToDay);//明日凌晨的时间戳

            //$LastDay = strtotime(date("Y-m-d",$data['next_date'])); #最后领取日期
            $nextDay = strtotime(date("Y-m-d",strtotime("+1 day")),$data['next_date']); #下一次领取日期

            if($data['next_date'] > time()) throw new Exception("您今日已经领取过该套餐，请[".date("Y-m-d H:i:s",$nextDay)."]后再来！");
        }else{
            $nextDay = strtotime(date("Y-m-d",strtotime("+1 day")),time()); #现在时间+1天
        }
//        die;
        $newTime = $data['buy_day'] - 1;

        $sql = $this->web->query("UPDATE [X_TEAM_MONTHLY_CARD] SET [buy_day] = ?,[next_date] = ? WHERE [buy_username] = ?",[$newTime,$nextDay,$username]);
        if(!$sql) throw new Exception("[1]操作失败，请确保数据的正确性。");

        #-加积分-------------------------------------------
        $this->creditSystem->setConfigId($list['salary_type']);
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
        $this->creditSystem->addCredits($group,$list['daily_salary']);
        #-------------------------------------------------

        $msn = '恭喜您领取成功，点击确定返回主页面。';
        if(!$newTime){
            $sql1 = $this->web->query("DELETE FROM [X_TEAM_MONTHLY_CARD] WHERE [buy_username] = ?",[$username]);
            if(!$sql1) throw new Exception("[2]操作失败，请确保数据的正确性。");
            $msn = "您已经领取完该套餐，点击确定返回主页面。";
        }

        alert('usercp/MonthlyCard',$msn);
    }

    /**
     * @param string $username
     * @return array|bool|mixed|void|null
     */
    public function getMonthlyCardBuyList($username = '')
    {
        if($username){
            $data = $this->web->query_fetch_single("SELECT * FROM [X_TEAM_MONTHLY_CARD] WHERE [buy_username] = ?",[$username]);
        }else{
            $data = $this->web->query_fetch("SELECT * FROM [X_TEAM_MONTHLY_CARD]");
        }
        if(!$data) return;
        return $data;
    }

    /**
     * @param int $where
     * @return array|bool|void|null
     */
    public function getMonthlyCardList($where = 0)
    {
        if($where){
            $data = $this->web->query_fetch_single("SELECT * FROM [X_TEAM_MONTHLY_CARD_CONFIG] WHERE [ID] = ?",[$where]);
        }else{
            $data = $this->web->query_fetch("SELECT * FROM [X_TEAM_MONTHLY_CARD_CONFIG]");
        }
        if(!$data) return;
        return $data;
    }
    /***********************************************************************/
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
		if(!Validator::Alpha($module)) throw new Exception('无法加载[MonthlyCard]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[MonthlyCard]插件模块。');
		if(!@include_once(__PATH_MonthlyCard_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[MonthlyCard]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_MonthlyCard_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_MonthlyCard_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[MonthlyCard]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[MonthlyCard]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}