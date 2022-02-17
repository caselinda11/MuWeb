<?php
/**
 * 账号类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Account extends common {

    /**
     * 账号注册函数
     * @param $serverCode
     * @param $username
     * @param $password
     * @param $cPassword
     * @param $email
     * @throws Exception
     */
	public function setRegisterAccount($serverCode, $username, $password, $cPassword, $email,$sysConfig) {
        $regCfg = loadConfigurations('register');
        if (!Token::checkToken('register')) throw new Exception('出错了，请您重新输入！');
	    $group = getGroupIDForServerCode($serverCode);
		if(!check_value($username)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($password)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($cPassword)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($email)) throw new Exception('您必须填写表单中的所有字段。');
		if(!Validator::UsernameLength($username)) throw new Exception('用户名长度必须为4到10个字符。');
		if(!Validator::AlphaUsername($username)) throw new Exception('用户名只能是小写字母或数字。');
		if(!Validator::PasswordLength($password)) throw new Exception('密码长度可以是4到32字符。');
        if($password != $cPassword) throw new Exception('两次输入的密码不匹配，请重新输入。');
        #身份证验证
        $numb = 1111111111111;
        if($regCfg['register_enable_sno__numb']){
            if(isset($_REQUEST['sno__numb'])){
				
				$sno__numb=$_REQUEST['sno__numb'];
				
                if(!check_value($_REQUEST['sno__numb'])) throw new Exception('请正确填写身份证号。');
                if(strtolower(substr($sno__numb, -1))=='x'){
					if(!Validator::UnsignedNumber(substr($sno__numb,0,strlen($sno__numb)-1))) throw new Exception('请正确填写身份证号。');
				}
				else{
					
				if(!Validator::UnsignedNumber($sno__numb)) throw new Exception('请正确填写身份证号。');
				}
				
                if($regCfg['register_sno__numb_length'] != (strlen($_REQUEST['sno__numb']))) throw new Exception('身份证不可小于['.$regCfg['register_sno__numb_length'].']位!');
                if(!Validator::isValid($_REQUEST['sno__numb'])) throw new Exception('身份证号不正确，请重新输入。');
                if(!Validator::is_adult18($_REQUEST['sno__numb'])) throw new Exception('检测到您未满18周岁，禁止注册。');
                $numb = $_REQUEST['sno__numb'];
            }
        }
        #自动补全身份证长度
        $numb = str_pad($numb,18,1,STR_PAD_LEFT);#自动补全
        if($this->_serverFiles == 'igcn') $numb = substr($numb,13);

        #推荐人
        if(isset($_REQUEST['invite'])){
            if(check_value($_REQUEST['invite'])){
                if(!Validator::UnsignedNumber($_REQUEST['invite'])) throw new Exception('邀请码错误，如果没有可以不填写。');
                if(!$this->_checkUserIDExists($group,substr($_REQUEST['invite'],2))) throw new Exception('邀请码错误，如果没有可以不填写。');
            }
        }

        #手机号phon_numb phone
        $phone = 13800138000;
        if (isset($_REQUEST['phon_numb']) && check_value($_REQUEST['phon_numb'])){
            $phone = $_REQUEST['phon_numb'];
            $phoneCode = $_REQUEST['phon_code'];
            if (!Validator::UnsignedNumber($phone)) throw new Exception('手机号错误，请重新输入。');
            if (!Validator::UnsignedNumber($phoneCode)) throw new Exception('手机验证码错误，请重新输入。');
            if (!isset($_SESSION['phone']) || !isset($_SESSION['phone_code'])) throw new Exception('手机号或验证码提交错误，请重新输入。');
            if($_SESSION['phone'] != $phone || $_SESSION['phone_code'] != $phoneCode) throw new Exception('手机号与验证码不正确，请重新输入。');
            if($_SESSION['phone_count']){
                $accountPhone = $this->getRegisterCountPhone($group,$phone);
                if($_SESSION['phone_count'] <= $accountPhone) throw new Exception("该手机号已经注册超过上线，不可注册。");
                unset($_SESSION['phone'],$_SESSION['phone_code'],$_SESSION['phone_count']);
            }
        }

		if($regCfg['register_enable_qq_email']){
            if(!Validator::QQEmail($email)) throw new Exception('您输入的邮箱地址无效，请正确输入邮箱地址。');
        }else{
            if(!Validator::Email($email)) throw new Exception('您输入的邮箱地址无效，请正确输入邮箱地址。');
        }

        # 检查用户名/电子邮件是否存在
		if($this->_checkUsernameExists($group,$username)) throw new Exception('您输入的用户名已存在，请重新输入。');

		#使用允许重复电子邮箱
		if($regCfg['email_status']){
            if($this->_checkEmailExists($group,$email)) throw new Exception('您输入的邮箱地址已存在，请重新输入。');
        }

		# 网站系统邮箱验证系统 (EVS)
		if($regCfg['verify_email']) {
			# [邮箱验证]检查用户名/电子邮件是否存在
			if($this->checkUsernameEVS($username)) throw new Exception('[EMAIL]您输入的用户名已存在，请重新输入。');
			if($this->checkEmailEVS($email)) throw new Exception('[EMAIL]您输入的邮箱地址已存在，请重新输入。');
			
			# 生成验证码
			$verificationKey = $this->createRegistrationVerification($username,$password,$email,$numb);
			if(!check_value($verificationKey)) throw new Exception('[EMAIL]出现意外错误，请与我们的在线客服联系。');
			
			# 发送验证电子邮件
			$this->sendRegistrationVerificationEmail($group,$username,$email,$verificationKey);
            alert('/', '申请注册成功，已成功向您邮箱发送了一份验证信息，请您前往您的邮箱确认注册。点击确定将回到主界面。');
			return;
		}

        # 组装
        $data = [
            'username' => $username,
            'password' => $password,
            'name' => 'X TEAM CMS',
            'serial' => $numb,
            'email' => $email,
            'addr_info' => $numb,
            'addr_deta' => $numb,
            'servercode' => $serverCode,
            'phon_numb' => $phone,
        ];

        $Me_MuOnline = Connection::Database('Me_MuOnline',$group);
        $MuOnline = Connection::Database('MuOnline', $group);
        $Web = Connection::Database('Web');
        $mac = new Machine(PHP_OS);

        # 事务处理
        try{
            $Me_MuOnline->beginTransaction();
            $MuOnline->beginTransaction();
            $Web->beginTransaction();

            #插入账号
            if($this->_md5Enabled) {
                $query = "INSERT INTO ["._TBL_MI_."] ("._CLMN_USERNM_.", "._CLMN_PASSWD_.", "._CLMN_MEMBNAME_.", "._CLMN_SNONUMBER_.", "._CLMN_EMAIL_.",addr_info,addr_deta,"._CLMN_GROUP_.", "._CLMN_BLOCCODE_.", "._CLMN_CTLCODE_.",phon_numb) VALUES (:username, [dbo].[fn_md5]('".$password."', '".$username."'), :name, :serial, :email, :addr_info, :addr_deta, :servercode, 0, 0, :phon_numb)";
            } else {
                $query = "INSERT INTO ["._TBL_MI_."] ("._CLMN_USERNM_.", "._CLMN_PASSWD_.", "._CLMN_MEMBNAME_.", "._CLMN_SNONUMBER_.", "._CLMN_EMAIL_.",addr_info,addr_deta,"._CLMN_GROUP_.", "._CLMN_BLOCCODE_.", "._CLMN_CTLCODE_.",phon_numb) VALUES (:username, :password, :name, :serial, :email, :addr_info, :addr_deta,:servercode, 0, 0, :phon_numb)";
            }
           
    		$Me_MuOnline->query($query, $data);
            #插入仓库
            if($regCfg['money']){
                $MuOnline->query("INSERT INTO [warehouse] ([AccountID],[Money]) VALUES (?, ?)", [$username, $regCfg['money']]);
            }
			
		     if($sysConfig['sendType']==2){
				
				$find_data = $MuOnline->query_fetch_single("SELECT COUNT(*) as c FROM warehouse WHERE AccountID = ? ",[$username]);
	            if (!is_array($find_data)) {
				 $MuOnline->query("INSERT INTO warehouse (AccountID,Money) VALUES (?,?)", [$username,$regCfg["money"]]);
		        }
				$MuOnline->query("UPDATE warehouse SET Items=CONVERT(varbinary(3840),REPLICATE(char(0xFF),3840)) WHERE AccountID=?", [$username]);
				$MuOnline->query("INSERT INTO CashShopData (AccountID,WCoinC,WCoinP,GoblinPoint) VALUES (?,0,0,0 )", [$username]);
				
			 }
			

            #插入日志
            $Web->query("INSERT INTO [".X_TEAM_ACCOUNT."] (account, servercode, MachineID, CreateTime) VALUES (?, ?, ?, ?)", [$username, getServerCodeForGroupID($group), $mac->mac_addr, logDate()]);
            #插入推荐人信息
            $inviteConfig = loadConfigurations('usercp.myaccount');
            if($inviteConfig['invite']){
                $Web->query("UPDATE [".X_TEAM_ACCOUNT."] set [invite_ID] = ? where [account] = ?", [$_REQUEST['invite'], $username]);
            }

            $Me_MuOnline->commit();
            $MuOnline->commit();
            $Web->commit();
        }catch (Exception $exception){
            $Me_MuOnline->rollBack();
            $MuOnline->rollBack();
            $Web->rollBack();
            throw new Exception($exception->getMessage());
        }

        # 发送欢迎邮件
        if($regCfg['send_welcome_email']) {
            $this->sendWelcomeEmail($username, $email);
        }

        #--------------------------------
        #百度包含推广代码
        echo '<script type="text/javascript">';
        echo '$(function () {';
            echo "window._agl && window._agl.push(['track', ['success', {t: 3}]])";
        echo '});';
        echo '</script>';
        #--------------------------------

		alert("login",'您的账号已经创建成功。 请点击确定回到主界面!');
	}

    /**
     * 统计手机号注册次数
     * @param $group
     * @param $phone
     * @return int|mixed
     * @throws Exception
     */
    public function getRegisterCountPhone($group,$phone)
    {
        if(!check_value($group)) return 0;
        if(!check_value($phone)) return 0;
        $data = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT COUNT(*) as phone FROM ["._TBL_MI_."] WHERE phon_numb = ? AND servercode = ?",[$phone,getServerCodeForGroupID($group)]);
	    if (!is_array($data)) return 0;
        return $data['phone'];
    }

    /**
     * 检测手机号是否存在
     * @param $group
     * @param $username
     * @param $phone
     * @return bool
     * @throws Exception
     */
    public function _checkUsernamePhoneExists($group,$username,$phone)
    {
        if(!check_value($group)) return false;
        if(!Validator::UsernameLength($username)) return false;
        if(!Validator::AlphaNumeric($username)) return false;
        if(!Validator::UnsignedNumber($phone)) return false;
        if(!Validator::is_mobile($phone)) return false;
        $query = "SELECT ["._CLMN_USERNM_."] FROM "._TBL_MI_." WHERE "._CLMN_USERNM_."= ? AND phon_numb = ?";
        $result = Connection::Database('Me_MuOnline',$group)->query_fetch_single($query, [$username,$phone]);
        if(is_array($result)) return true;
        return false;
    }

    /**
     * 手机号找回密码
     * @param $serverCode
     * @param $username
     * @param $phone
     * @param $phoneCode
     * @throws Exception
     */
    public function setPhoneForGotPassword($serverCode, $username, $phone, $phoneCode)
    {
        if(!Token::checkToken('forgotpassword')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($serverCode)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        $group = getGroupIDForServerCode($serverCode);
        #手机号phon_numb phone
        if (!isset($_SESSION['phone']) || !isset($_SESSION['phone_code'])) throw new Exception('手机号或验证码错误，请重新输入。');
        if($_SESSION['phone'] != $phone || $_SESSION['phone_code'] != $phoneCode) throw new Exception('手机号与验证码不正确，请重新输入。');
        unset($_SESSION['phone'],$_SESSION['phone_code'],$_SESSION['phone_count']);

        if(!$this->_checkUsernamePhoneExists($group,$username,$phone)) throw new Exception('您输入的账号或手机号不存在。');

        $user_id = $this->getUserGIDForUsername($group,$username);
        if(!check_value($user_id)) throw new Exception('出现意外错误，请与我们的在线客服联系。');

        $accountData = $this->getUserInfoForUserGID($group,$user_id);
        if(!is_array($accountData)) throw new Exception('出现意外错误，请与我们的在线客服联系。');

        # 更新用户密码
        $new_password = rand(11111111,99999999);
        $update_pass = $this->changePassword($group, $accountData[_CLMN_MEMBID_], $username, $new_password);
        if(!$update_pass) throw new Exception('出现意外错误，请与我们的在线客服联系。');
        alert('login','您的新密码为['.$new_password.']，请您牢记。');
        return;
    }

    /**
     * 修改密码函数
     * @param $group
     * @param $userid
     * @param $username
     * @param $password
     * @param $new_password
     * @param $confirm_new_password
     * @throws Exception
     */
	public function setChangePasswordProcess($group, $userid, $username, $password, $new_password, $confirm_new_password) {
	    if(!Token::checkToken('mypassword')) throw new Exception('出错了，请您重新输入！');
		if(!check_value($userid)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($username)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($password)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($new_password)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($confirm_new_password)) throw new Exception('您必须填写表单中的所有字段。');
		if(!Validator::PasswordLength($new_password)) throw new Exception('密码长度可以是4到32字符。');
		if($new_password != $confirm_new_password) throw new Exception('两次输入的密码不匹配，请重新输入。');

		# 检查用户凭证
		if(!$this->validateUsername($group, $username, $password)) throw new Exception('您输入的密码不正确，请重新输入！');

		# 查看在线状态
		if($this->checkUserOnline($group,$username)) throw new Exception('您的账号已在线，请断开连接。');
        $regConfig = loadConfigurations('register');
        if($regConfig['register_enable_phone']){
            if(!isset($_REQUEST['phon_numb']) || !$_REQUEST['phon_numb'])  throw new Exception('手机号不能为空，请重新输入。');
            if(!isset($_REQUEST['phon_code']) || !$_REQUEST['phon_code'])  throw new Exception('验证码不能为空，请重新输入。');
            #手机号phon_numb phone
            if (!isset($_SESSION['phone']) || !isset($_SESSION['phone_code'])) throw new Exception('手机号或验证码错误，请重新输入。');
            if($_SESSION['phone'] != $_REQUEST['phon_numb'] || $_SESSION['phone_code'] != $_REQUEST['phon_code']) throw new Exception('手机号与验证码不正确，请重新输入。');
            unset($_SESSION['phone'],$_SESSION['phone_code'],$_SESSION['phone_count']);
            if(!$this->_checkUsernamePhoneExists($group,$username,$_REQUEST['phon_numb'])) throw new Exception('您输入的账号或手机号不符，请重新操作。');
        }
		# 更改密码
		if(!$this->changePassword($group,$userid, $username, $new_password)) throw new Exception('出现意外错误，请与我们的在线客服联系。');

		# 使用新密码发送电子邮件
		$accountData = $this->getUserInfoForUserGID($group,$userid);
		try {
			$email = new Email();
			$email->setTemplate('CHANGE_PASSWORD');
			$email->addVariable('{USERNAME}', $username);
			$email->addVariable('{NEW_PASSWORD}', $new_password);
			$email->addAddress($accountData[_CLMN_EMAIL_]);
			$email->send();
		} catch (Exception $ex) {}

        alert("usercp/mypassword",'您的账户密码已成功更新!');
	}


    /**
     * 改密码邮箱验证函数
     * @param $group
     * @param $userid
     * @param $username
     * @param $password
     * @param $new_password
     * @param $confirm_new_password
     * @param $ip_address
     * @throws Exception
     */
	public function setChangePasswordProcess_verifyEmail($group, $userid, $username, $password, $new_password, $confirm_new_password, $ip_address) {
        if(!Token::checkToken('mypassword')) throw new Exception('出错了，请您重新输入！');
	    if(!check_value($userid)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($username)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($password)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($new_password)) throw new Exception('您必须填写表单中的所有字段。');
		if(!check_value($confirm_new_password)) throw new Exception('您必须填写表单中的所有字段。');
		if(!Validator::PasswordLength($new_password)) throw new Exception('密码长度可以是4到32字符。');
		if($new_password != $confirm_new_password) throw new Exception('两次输入的密码不匹配，请重新输入。');
		
		# 加载改密码配置
		$myPasswordConfig = loadConfigurations('usercp.mypassword');
		
		# 检查用户凭证
		if(!$this->validateUsername($group,$username, $password)) throw new Exception('您输入的密码不正确，请重新输入！');
		
		# 查看在线状态
		if($this->checkUserOnline($group,$username)) throw new Exception('您的账号已在线，请断开连接。');
		
		# 检查用户是否有有效的密码更改请求
		if($this->hasActivePasswordChangeRequest($userid)) throw new Exception('您有一个有效的密码更改请求，请检查您的邮箱。');
		
		# 加载帐户数据
		$accountData = $this->getUserInfoForUserGID($group,$userid);
		if(!is_array($accountData)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		
		# 请求数据
		$auth_code = mt_rand(111111,999999);
		$link = $this->generatePasswordChangeVerificationURL($userid, $auth_code);
		
		# 向数据库添加请求
		$addRequest = $this->addPasswordChangeRequest($userid, $new_password, $auth_code);
		if(!$addRequest) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		
		# 发送验证电子邮件
		try {
			$email = new Email();
			$email->setTemplate('CHANGE_PASSWORD_EMAIL_VERIFICATION');
			$email->addVariable('{USERNAME}', $username);
			$email->addVariable('{DATE}', date("m/d/Y @ h:i a"));
			$email->addVariable('{IP_ADDRESS}', $ip_address);
			$email->addVariable('{LINK}', $link);
			$email->addVariable('{EXPIRATION_TIME}', $myPasswordConfig['change_password_request_timeout']);
			$email->addAddress($accountData[_CLMN_EMAIL_]);
			$email->send();
			message('success', '我们已向您的邮箱发送了一个包含验证链接的邮件，验证邮箱地址后，您的密码将会更新!');
		} catch (Exception $ex) {
			if($this->_debug) {
				throw new Exception($ex->getMessage());
			} else {
				throw new Exception('我们无法向您发送验证邮件，请与在线客服联系。');
			}
		}
		
	}

    /**
     * 改密码验证请求
     * @param $group
     * @param $user_id
     * @param $auth_code
     * @throws Exception
     */
	public function setChangePasswordVerificationProcess($group, $user_id, $auth_code) {
		if(!check_value($user_id)) throw new Exception('您的请求中提供的信息无效。');
		if(!check_value($auth_code)) throw new Exception('您的请求中提供的信息无效。');
		
		$userid = $user_id;
		$authCode = $auth_code;
		
		if(!Validator::UnsignedNumber($userid)) throw new Exception('您的请求无法完成，请再试一次。');
		if(!Validator::UnsignedNumber($authCode)) throw new Exception('您的请求无法完成，请再试一次。');
		
		$result = Connection::Database('Web')->query_fetch_single("SELECT * FROM [".X_TEAM_PASSCHANGE_REQUEST."] WHERE user_id = ?", array($userid));
		if(!is_array($result)) throw new Exception('您的请求无法完成，请再试一次。');
		
		# 加载改密码模块配置
		$mypassCfg = loadConfigurations('usercp.mypassword');
		$request_timeout = $mypassCfg['change_password_request_timeout'] * 3600;
		$request_date = $result['request_date'] + $request_timeout;
		
		# check request data
		if($request_date < time()) throw new Exception('您的密码更改请求已过期，请申请新密码。');
		if($result['auth_code'] != $authCode) throw new Exception('提供的授权码无效。');
		
		# account data
		$accountData = $this->getUserInfoForUserGID($group,$userid);
		$username = $accountData[_CLMN_USERNM_];
		$new_password = $result['new_password'];
		
		# check online status
		if($this->checkUserOnline($group,$username)) throw new Exception('您的账号已在线，请断开连接。');
		
		# update password
		if(!$this->changePassword($group,$userid, $username, $new_password)) throw new Exception('您的密码无法更改，请与在线客服联系。');
		
		# send email
		try {
			$email = new Email();
			$email->setTemplate('CHANGE_PASSWORD');
			$email->addVariable('{USERNAME}', $username);
			$email->addVariable('{NEW_PASSWORD}', $new_password);
			$email->addAddress($accountData[_CLMN_EMAIL_]);
			$email->send();
		} catch (Exception $ex) {
			if($this->_debug) {
				throw new Exception($ex->getMessage());
			}
		}
		
		# clear password change request
		$this->removePasswordChangeRequest($userid);
		
		# success message
		alert('usercp/myaccount',"您的账号密码已成功更改，点击确定回到主界面。");
	}

    /**
     * 密码找回请求
     * 将发送邮件到用户邮箱中
     * @param $serverCode
     * @param $username
     * @param $user_email
     * @param $ip_address
     * @throws Exception
     */
	public function setPasswordRecoveryProcess($serverCode, $username, $user_email, $ip_address) {
        if(!Token::checkToken('forgotpassword')) throw new Exception('出错了，请您重新输入！');
        $group = getGroupIDForServerCode($serverCode);
		if(!check_value($user_email)) throw new Exception('找不到提供的邮箱地址的账号。');
		if(!check_value($ip_address)) throw new Exception('找不到提供的邮箱地址的账号。');
		if(!Validator::Email($user_email)) throw new Exception('找不到提供的邮箱地址的账号。');
		if(!Validator::Ip($ip_address)) throw new Exception('找不到提供的邮箱地址的账号。');
		
		if(!$this->_checkUsernameExists($group,$username)) throw new Exception('您输入的账号不存在。');
		if(!$this->_checkEmailExists($group,$user_email)) throw new Exception('找不到提供的邮箱地址的账号。');

		$user_id = $this->getUserGIDForUsername($group,$username);
		if(!check_value($user_id)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$accountData = $this->getUserInfoForUserGID($group,$user_id);
		if(!is_array($accountData)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		# 生成恢复密码
		$arc = $this->generateAccountRecoveryCode($accountData[_CLMN_MEMBID_], $accountData[_CLMN_USERNM_]);

		# 生成恢复网址
		$aru = $this->generateAccountRecoveryLink($group,$accountData[_CLMN_MEMBID_], $accountData[_CLMN_EMAIL_], $arc);
		
		# 发送Email
		try {
			$email = new Email();
			$email->setTemplate('PASSWORD_RECOVERY_REQUEST');
			$email->addVariable('{USERNAME}', $accountData[_CLMN_USERNM_]);
			$email->addVariable('{DATE}', date("Y-m-d @ h:i a"));
			$email->addVariable('{IP_ADDRESS}', $ip_address);
			$email->addVariable('{LINK}', $aru);
			$email->addAddress($accountData[_CLMN_EMAIL_]);
			$email->send();
			$_POST = [];
			message('success', '我们向您发送了一封包含特殊链接的邮件，以恢复您的账号访问权限![如果没有收到邮件，请检查您的垃圾邮件/垃圾文件夹]');
		} catch (Exception $ex) {
			if($this->_debug) {
				throw new Exception($ex->getMessage());
			} else {
				throw new Exception('出现意外错误，请与我们的在线客服联系。');
			}
		}
	}

    /**
     * 密码找回函数
     * @param $group
     * @param $ui
     * @param $ue
     * @param $key
     * @throws Exception
     */
	public function setPasswordRecoveryVerificationProcess($group, $ui, $ue, $key) {
		if(!check_value($ui)) throw new Exception('密码恢复数据无效。');
		if(!check_value($ue)) throw new Exception('密码恢复数据无效。');
		if(!check_value($key)) throw new Exception('密码恢复数据无效。');
		
		$user_id = $ui; // 解码的用户ID
		if(!Validator::UnsignedNumber($user_id)) throw new Exception('密码恢复数据无效。');
		
		$user_email = $ue; // 解码后的电子邮件地址
		if(!$this->_checkEmailExists($group,$user_email)) throw new Exception('密码恢复数据无效。');
		
		$accountData = $this->getUserInfoForUserGID($group,$user_id);
		if(!is_array($accountData)) throw new Exception('密码恢复数据无效。');
		
		$username = $accountData[_CLMN_USERNM_];
		$gen_key = $this->generateAccountRecoveryCode($user_id, $username);
		
		# compare keys
		if($key != $gen_key) throw new Exception('密码恢复数据无效。');
		
		# 更新用户密码
		$new_password = rand(11111111,99999999);
		$update_pass = $this->changePassword($group,$user_id, $username, $new_password);
		if(!$update_pass) throw new Exception('出现意外错误，请与我们的在线客服联系。');

		try {
			$email = new Email();
			$email->setTemplate('PASSWORD_RECOVERY_COMPLETED');
			$email->addVariable('{USERNAME}', $username);
			$email->addVariable('{NEW_PASSWORD}', $new_password);
			$email->addAddress($accountData[_CLMN_EMAIL_]);
			$email->send();
			
			message('success', '我们已向您发送了一个包含新密码的邮件![如果没有收到邮件，请检查您的垃圾邮件/垃圾文件夹]');
		} catch (Exception $ex) {
			if($this->_debug) {
				throw new Exception($ex->getMessage());
			} else {
				throw new Exception('出现意外错误，请与我们的在线客服联系。');
			}
		}
	}

    /**
     * 改邮箱地址
     * @param $group
     * @param $accountId
     * @param $newEmail
     * @param $ipAddress
     * @throws Exception
     */
	public function changeEmailAddress($group,$accountId, $newEmail, $ipAddress) {
        if(!Token::checkToken('changeEmail')) throw new Exception('出错了，请您重新输入！');
		if(!check_value($accountId)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		if(!check_value($newEmail)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		if(!check_value($ipAddress)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		if(!Validator::Ip($ipAddress)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		if(!Validator::Email($newEmail)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');

		$regConfig = loadConfigurations('register');
		#是否允许重复邮箱
        if($regConfig['email_status']) {
            # 检查电子邮件是否已被使用
            if ($this->_checkEmailExists($group, $newEmail)) throw new Exception('您输入的邮箱地址已存在，请重新输入。');
        }
		#帐户信息
		$accountInfo = $this->getUserInfoForUserGID($group,$accountId);
		if(!is_array($accountInfo)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		
		$myEmailCfg = loadConfigurations('usercp.myemail');
		if($myEmailCfg['require_verification']) {
			# 需要验证
			$userName = $accountInfo[_CLMN_USERNM_];
			$userEmail = $accountInfo[_CLMN_EMAIL_];
			$requestDate = strtotime(date("m/d/Y 23:59"));
			$key = md5(md5($userName).md5($userEmail).md5($requestDate).md5($newEmail));
			$verificationLink = __BASE_URL__.'verifyemail/?op=3&uid='.$accountId.'&email='.$newEmail.'&key='.$key;
			
			# 发送验证电子邮件
			$sendEmail = $this->setChangeEmailVerificationMail($userName, $userEmail, $newEmail, $verificationLink, $ipAddress);
			if(!$sendEmail) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		} else {
			# 无需验证
			if(!$this->updateEmail($group,$accountId, $newEmail)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		}
	}

    /**
     * 更改邮箱验证请求
     * @param $group
     * @param $encodedId
     * @param $newEmail
     * @param $encryptedKey
     * @throws Exception
     */
	public function changeEmailVerificationProcess($group, $encodedId, $newEmail, $encryptedKey) {
		$userId = $encodedId;
		if(!Validator::UnsignedNumber($userId)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		if(!Validator::Email($newEmail)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		
		# check if email already in use
        $regCfg = loadConfigurations('register');
        if($regCfg['email_status']) {
            if ($this->_checkEmailExists($group, $newEmail)) throw new Exception('您输入的邮箱地址已存在，请重新输入。');
        }
        # account info
		$accountInfo = $this->getUserInfoForUserGID($group,$userId);
		if(!is_array($accountInfo)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		
		# check key
		$requestDate = strtotime(date("m/d/Y 23:59"));
		$key = md5(md5($accountInfo[_CLMN_USERNM_]).md5($accountInfo[_CLMN_EMAIL_]).md5($requestDate).md5($newEmail));
		if($key != $encryptedKey) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		
		# change email
		if(!$this->updateEmail($group,$userId, $newEmail)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
	}

    /**
     * 验证注册流程
     * @param $group
     * @param $username
     * @param $key
     * @throws Exception
     */
	public function verifyRegistrationProcess($group, $username, $key) {
		$verifyKey = Connection::Database('Web')->query_fetch_single("SELECT * FROM ".X_TEAM_REGISTER_ACCOUNT." WHERE registration_account = ? AND registration_key = ?", [$username,$key]);
		if(!is_array($verifyKey)) throw new Exception('您的请求无法完成，请再试一次。');
		
		# 加载注册配置
		$regCfg = loadConfigurations('register');
		
		# 插入数据
		$data = [
			'username' => $verifyKey['registration_account'],
			'password' => $verifyKey['registration_password'],
			'name' => $verifyKey['registration_account'],
			'serial' => $verifyKey['registration_serial'],
			'email' => $verifyKey['registration_email'],
            'group' =>  getServerCodeForGroupID($group)
		];

        $Me_MuOnline = Connection::Database("Me_MuOnline");
        $MuOnline = Connection::Database("MuOnline");
        $Web = Connection::Database("Web");
        $mac = new Machine(PHP_OS);
        # 事务处理
        try{
            $Me_MuOnline->beginTransaction();
            $MuOnline->beginTransaction();
            $Web->beginTransaction();

            #插入账号
            if($this->_md5Enabled) {
                $query = "INSERT INTO "._TBL_MI_." ("._CLMN_USERNM_.", "._CLMN_PASSWD_.", "._CLMN_MEMBNAME_.", "._CLMN_SNONUMBER_.", "._CLMN_EMAIL_.", "._CLMN_BLOCCODE_.", "._CLMN_CTLCODE_.", servercode) VALUES (:username, [dbo].[fn_md5]('".$data['password']."', '".$data['username']."'), :name, :serial, :email, 0, 0, :group)";
            } else {
                $query = "INSERT INTO "._TBL_MI_." ("._CLMN_USERNM_.", "._CLMN_PASSWD_.", "._CLMN_MEMBNAME_.", "._CLMN_SNONUMBER_.", "._CLMN_EMAIL_.", "._CLMN_BLOCCODE_.", "._CLMN_CTLCODE_.", servercode) VALUES (:username, :password, :name, :serial, :email, 0, 0, :group)";
            }

            $Me_MuOnline->query($query, $data);
            #插入仓库
            if($regCfg['money']){
                $MuOnline->query("INSERT INTO [warehouse] ([AccountID],[Money]) VALUES (?, ?)", [$username, $regCfg['money']]);
            }

            #插入日志
            $Web->query("INSERT INTO [".X_TEAM_ACCOUNT."] (account, servercode, MachineID, CreateTime) VALUES (?, ?, ?, ?)", [$username, getServerCodeForGroupID($group), $mac->mac_addr, logDate()]);

            $Me_MuOnline->commit();
            $MuOnline->commit();
            $Web->commit();
        }catch (Exception $exception){
            $Me_MuOnline->rollBack();
            $MuOnline->rollBack();
            $Web->rollBack();
            #写入日志
            @error_log($exception->getMessage() . "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
            throw new Exception($exception->getMessage());
        }

		# 删除验证请求
		$this->deleteRegistrationVerification($username);
		
		# 发送欢迎电子邮件
		if($regCfg['send_welcome_email']) {
			$this->sendWelcomeEmail($verifyKey['registration_account'],$verifyKey['registration_email']);
		}
		
		# 成功讯息
		alert('login', '您的账号已经创建成功。 请点击确定回到主界面!');
		
		# 重定向到登录（5秒）
		redirect(2,'login/',5);
	}

    /**
     * 唯一性ID拓展功能
     * @param $group
     * @param $email
     * @param bool $offset //验证是否单区一个,还是多区一个.
     * @return bool|void
     * @throws Exception
     */
    public function checkOnlyEmail($group,$email, $offset = false)
    {
        if(!check_value($group)) throw new Exception("操作失败，请重新提交。");
        if(!check_value($group)) throw new Exception("操作失败，请重新提交。");
        if(!check_value($email)) throw new Exception("请正确输入邮箱地址。");
        if(!Validator::Email($email)) throw new Exception("邮箱格式错误，请重新输入。");
        if(!file_exists("卡密.php")) throw new Exception("操作失败，无法读取到授权文件，请联系在线客服。");
        $fileArray = include_once("卡密.php");
        if(!in_array(strchr($email,'@',true), $fileArray)) throw new Exception("该邮箱未授权禁止注册，请联系在线客服申请。");
        $checkGroup = $offset ? " AND servercode = ?" : "";
        $result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT ["._CLMN_EMAIL_."] FROM ["._TBL_MI_."] WHERE ["._CLMN_EMAIL_."] = ?".$checkGroup, [$email,getServerCodeForGroupID($group)]);
        if(is_array($result)) throw new Exception("操作失败，该大区已使用过，禁止再次使用。");
        return true;
    }

    /**
     * 获取网站数据库中用户数据记录
     * @param $Quantity //请求数量
     * @return bool|void
     * @throws Exception
     */
    public function getWebRegistrationData($Quantity)
    {
        $result = Connection::Database('Web')->query_fetch("SELECT Top ".$Quantity." [account],[servercode],[CreateTime] FROM [".X_TEAM_ACCOUNT."] ORDER BY CreateTime DESC");
        if(!is_array($result)) return;
        return $result;
    }

    /**
     * 发送邮箱验证数据
     * @param $group
     * @param $username
     * @param $account_email
     * @param $key
     * @throws Exception
     */
	private function sendRegistrationVerificationEmail($group, $username, $account_email, $key) {
		$verificationLink = __BASE_URL__.'verifyemail/?op=2&group='.$group.'&user='.$username.'&key='.$key;
		try {
			$email = new Email();
			$email->setTemplate('WELCOME_EMAIL_VERIFICATION');
			$email->addVariable('{USERNAME}', $username);
			$email->addVariable('{LINK}', $verificationLink);
			$email->addAddress($account_email);
			$email->send();
		} catch (Exception $ex) {
			if($this->_debug) {
				throw new Exception($ex->getMessage());
			}
		}
	}

    /**
     * 发送欢迎加入邮件
     * @param $username
     * @param $address
     * @throws Exception
     */
	private function sendWelcomeEmail($username,$address) {
		try {
			$email = new Email();
			$email->setTemplate('WELCOME_EMAIL');
			$email->addVariable('{USERNAME}', $username);
			$email->addAddress($address);
			$email->send();
		} catch (Exception $ex) {
			if($this->_debug) {
				throw new Exception($ex->getMessage());
			}
		}
	}

    /**
     * 创建注册验证数据
     * @param $username
     * @param $password
     * @param $email
     * @param $serial
     * @return string|void
     * @throws Exception
     */
	private function createRegistrationVerification($username,$password,$email,$serial) {
		if(!check_value($username)) return;
		if(!check_value($password)) return;
		if(!check_value($email)) return;
		if(!check_value($email)) return;

		$key = uniqid();
		$data = array(
			$username,
			$password,
			$email,
            $serial,
			time(),
			$_SERVER['REMOTE_ADDR'],
			$key
		);
		
		$query = "INSERT INTO [".X_TEAM_REGISTER_ACCOUNT."] (registration_account,registration_password,registration_email,registration_serial,registration_date,registration_ip,registration_key) VALUES (?,?,?,?,?,?,?)";
		
		$result = Connection::Database('Web')->query($query, $data);
		if(!$result) return;
		return $key;
	}

    /**
     * 删除注册请求过程
     * @param $username
     * @return bool|void
     * @throws Exception
     */
	private function deleteRegistrationVerification($username) {
		if(!check_value($username)) return;
		$delete = Connection::Database('Web')->query("DELETE FROM ".X_TEAM_REGISTER_ACCOUNT." WHERE registration_account = ?", [$username]);
		if($delete) return true;
		return;
	}

    /**
     * [邮箱验证]检查账号是否已经申请
     * @param $username
     * @return bool|void
     * @throws Exception
     */
	private function checkUsernameEVS($username) {
		if(!check_value($username)) return;
		$result = Connection::Database('Web')->query_fetch_single("SELECT * FROM ".X_TEAM_REGISTER_ACCOUNT." WHERE registration_account = ?", array($username));
		
		$configs = loadConfigurations('register');
		if(!is_array($configs)) return;
		
		$timelimit = $result['registration_date']+$configs['verification_timelimit']*60*60;
		if($timelimit > time()) return true;
		
		$this->deleteRegistrationVerification($username);
		return false;
	}

    /**
     * [邮箱验证]检查邮箱是否已经申请
     * @param $email
     * @return bool|void
     * @throws Exception
     */
	private function checkEmailEVS($email) {
		if(!check_value($email)) return;
		$result = Connection::Database('Web')->query_fetch_single("SELECT * FROM ".X_TEAM_REGISTER_ACCOUNT." WHERE registration_email = ?", [$email]);
		
		$configs = loadConfigurations('register');
		if(!is_array($configs)) return;
		
		$timelimit = $result['registration_date']+$configs['verification_timelimit']*60*60;
		if($timelimit > time()) return true;
		
		$this->deleteRegistrationVerification($result['registration_account']);
		return false;
	}

    /**
     * 更改电子邮件验证电子邮件
     * @param $userName
     * @param $emailAddress
     * @param $newEmail
     * @param $verificationLink
     * @param $ipAddress
     * @return bool|void
     * @throws Exception
     */
	private function setChangeEmailVerificationMail($userName, $emailAddress, $newEmail, $verificationLink, $ipAddress) {
		try {
			$email = new Email();
			$email->setTemplate('CHANGE_EMAIL_VERIFICATION');
			$email->addVariable('{USERNAME}', $userName);
			$email->addVariable('{IP_ADDRESS}', $ipAddress);
			$email->addVariable('{NEW_EMAIL}', $newEmail);
			$email->addVariable('{LINK}', $verificationLink);
			$email->addAddress($emailAddress);
			$email->send();
			
			return true;
		} catch (Exception $ex) {
			if($this->_debug) {
				throw new Exception($ex->getMessage());
			}
			return;
		}
	}

    /**
     * 生成密码找回链接
     * @param $group
     * @param $userId
     * @param $email
     * @param $recovery_code
     * @return string|void
     */
	private function generateAccountRecoveryLink($group,$userId,$email,$recovery_code) {
		if(!check_value($userId)) return;
		if(!check_value($recovery_code)) return;
		
		$build_url = __BASE_URL__;
		$build_url .= 'forgotpassword/';
        $build_url .= '?id=';
        $build_url .= $group;
		$build_url .= '&ui=';
		$build_url .= $userId;
		$build_url .= '&ue=';
		$build_url .= $email;
		$build_url .= '&key=';
		$build_url .= $recovery_code;
		return $build_url;
	}
	
}
?>