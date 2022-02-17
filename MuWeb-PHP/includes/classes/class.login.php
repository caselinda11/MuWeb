<?php
/**
 * 登陆模块相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class login {
	
	private $_config;
    private $common;
    private $MuWeb;
    /**
     * 构造函数
     * @throws Exception
     */
    function __construct() {
        #加载基类
		$this->common = new common();
		#加载网站数据库
		$this->MuWeb = Connection::Database('Web');
		#加载登陆模块配置文件
        $loginConfigs = loadConfigurations('login');
        #如果配置文件为空
		if(!is_array($loginConfigs)) throw new Exception('登录配置丢失。');
		$this->_config = $loginConfigs;
	}

    /**
     * 验证登陆
     * @param $serverCode
     * @param $username
     * @param $password
     * @param int $levelPassword
     * @throws Exception
     */
	public function validateLogin($serverCode, $username, $password, $levelPassword = 1111111) {
        if(Token::checkToken('login') || Token::checkToken('login_sidebar')){
            $group = getGroupIDForServerCode($serverCode);
            if(!check_value($username)) throw new Exception('您必须填写表单中的所有字段。');
            if(!check_value($password)) throw new Exception('您必须填写表单中的所有字段。');
            if(!$this->common->_checkUsernameExists($group,$username)) throw new Exception('您提供的账号或密码不正确，请重试。');
            if(!$this->canLogin($_SERVER['REMOTE_ADDR'])) throw new Exception('该账号已达到最大登录失败次数，因此已被暂时冻结，请过15分钟后尝试！');
            if(!$this->checkUsernameBan($group, $username)) throw new Exception('该账号已被封停,如有疑问请联系在线客服!!');
            if($this->common->validateUsername($group,$username,$password)) {
                #验证二级密码
                $regConfig = loadConfigurations('register');
                if($regConfig['register_enable_sno__numb']){
                    if(!$this->common->checkLevel2Password($group,$username,$levelPassword)) throw new Exception('二级密码错误，请重新输入。');
                }
                #获取用户ID
                $userID = $this->common->getUserGIDForUsername($group,$username);
                if(!check_value($userID)) throw new Exception('无法检索您的账号信息，请稍后再试。');
                #获取最后登陆角色
                $character = new Character();
                $character_name = $character->getSessionCharacterName($group,$username);
                # 登陆成功
                $this->removeFailedLogin($_SERVER['REMOTE_ADDR']);
                session_regenerate_id();
                $_SESSION['valid']      = true;             //是否登陆
                $_SESSION['timeout']    = time();           //登录时间
                $_SESSION['userid']     = $userID;          //用户ID
                $_SESSION['group']      = $group;           //用户分组
                $_SESSION['username']   = $username;        //用户账号
                $_SESSION['character']  = $character_name;  //用户最后登陆角色
                if(mconfig('enable_redirect')){
                    #回到之前的页面
                    header('Location:'.$_SERVER['HTTP_REFERER']);
                }else{
                    # 跳转到个人面板
                    redirect(1,'usercp/myaccount');
                }
            } else {
                # 登录失败
                $this->addFailedLogin($username,$_SERVER['REMOTE_ADDR']);
                message('error','您提供的账号或密码不正确，请重试。');
                message('warning', '您已尝试登录['.$this->checkFailedLogin($_SERVER['REMOTE_ADDR']).']次，还可尝试['.(mconfig('max_login_attempts')-$this->checkFailedLogin($_SERVER['REMOTE_ADDR'])).']次，使用完将'.mconfig('failed_login_timeout').'分钟内限制登录，请谨慎操作！');
            }
        }else{
            throw new Exception('出错了，请您重新输入！');
        }
	}

    /**
     * 验证账号是否被封停
     * @param $group
     * @param $username
     * @return bool|void
     * @throws Exception
     */
    public function checkUsernameBan($group,$username)
    {
        if(!check_value($group)) return false;
        if(!check_value($username)) return false;
        $result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT [bloc_code] FROM "._TBL_MI_." WHERE "._CLMN_USERNM_." = ? AND [bloc_code] = ?", [$username,0]);
        if(is_array($result)) return true;
        return false;
	}
    /**
     * 验证IP是否被封停
     * @param $ipAddress
     * @return bool|void
     */
	public function canLogin($ipAddress) {
		if(!Validator::Ip($ipAddress)) return;
		$failedLogin = $this->checkFailedLogin($ipAddress);
		if($failedLogin < $this->_config['max_login_attempts']) return true;
		
		$result = $this->MuWeb->query_fetch_single("SELECT * FROM ".X_TEAM_FLA." WHERE ip_address = ? ORDER BY id DESC", array($ipAddress));
		if(!is_array($result)) return true;
		if(time() < $result['unlock_timestamp']) return;
		
		$this->removeFailedLogin($ipAddress);
		return true;
	}

    /**
     * 检查失败的登录
     * @param $ipAddress
     * @return mixed|void
     */
	public function checkFailedLogin($ipAddress) {
		if(!Validator::Ip($ipAddress)) return;
		$result = $this->MuWeb->query_fetch_single("SELECT * FROM ".X_TEAM_FLA." WHERE ip_address = ? ORDER BY id DESC", array($ipAddress));
		if(!is_array($result)) return;
		return $result['failed_attempts'];
	}

    /**
     * 登陆失败记录到数据表中
     * @param $username
     * @param $ip
     * @throws Exception
     */
	public function addFailedLogin($username, $ip) {
		if(!Validator::UsernameLength($username)) return;
		if(!Validator::AlphaNumeric($username)) return;
		if(!Validator::Ip($ip)) return;

		$failedLogins = $this->checkFailedLogin($ip);
		$timeout = time()+$this->_config['failed_login_timeout']*60;
		
		if($failedLogins >= 1) {
			# 更新
			if(($failedLogins+1) >= $this->_config['max_login_attempts']) {
				# 最大尝试失败次数->阻止
				$this->MuWeb->query("UPDATE [".X_TEAM_FLA."] SET username = ?, ip_address = ?, failed_attempts = failed_attempts + 1, unlock_timestamp = ?, timestamp = ? WHERE ip_address = ?", [$username, $ip, $timeout, time(), $ip]);
			} else {
				$this->MuWeb->query("UPDATE [".X_TEAM_FLA."] SET username = ?, ip_address = ?, failed_attempts = failed_attempts + 1, timestamp = ? WHERE ip_address = ?", [$username, $ip, time(), $ip]);
			}
		} else {
			# 插入
			$data = [$username, $ip, 0, 1, time()];
			$this->MuWeb->query("INSERT INTO [".X_TEAM_FLA."] (username, ip_address, unlock_timestamp, failed_attempts, timestamp) VALUES (?, ?, ?, ?, ?)", $data);
		}
	}

    /**
     * @param $ip
     */
	public function removeFailedLogin($ip) {
		if(!Validator::Ip($ip)) return;
		$this->MuWeb->query("DELETE FROM [".X_TEAM_FLA."] WHERE [ip_address] = ?", [$ip]);
	}

    /**
     * 登出账号
     */
	public function logout() {
		$_SESSION = [];
		session_destroy();
		redirect();
	}
}