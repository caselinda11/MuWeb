<?php
/**
 * 基础常用类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class common {
	
	protected $_md5Enabled;
	protected $_serverFiles = 'igcn';
	protected $_debug = false;
	protected $_blockedIpCache = 'blocked_ip.cache';

    /**
     * 构造函数
     * common constructor.
     * @throws Exception
     */
	function __construct() {
		// 加载配置文件
		$this->_serverFiles = config('server_files');
		$this->_md5Enabled = config('SQL_ENABLE_MD5');
		$this->_debug = config('error_reporting');
	}

    /**
     * 验证邮箱是否存在
     * @param $group
     * @param $email
     * @return bool|void
     * @throws Exception
     */
	public function _checkEmailExists($group, $email) {
		if(!Validator::Email($email)) return;
		$result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT ["._CLMN_EMAIL_."] FROM ["._TBL_MI_."] WHERE ["._CLMN_EMAIL_."] = ?", [$email]);
		if(is_array($result)) return true;
		return;
	}

    /**
     * 验证账号是否存在
     * @param $group
     * @param $username
     * @return bool|void
     * @throws Exception
     */
	public function _checkUsernameExists($group, $username) {
        if(!check_value($group)) return false;
		if(!Validator::UsernameLength($username)) return false;
		if(!Validator::AlphaNumeric($username)) return false;
        $query = "SELECT ["._CLMN_USERNM_."] FROM ["._TBL_MI_."] WHERE ["._CLMN_USERNM_."] = ?";
        $result = Connection::Database('Me_MuOnline',$group)->query_fetch_single($query, [$username]);
        if(is_array($result)) return true;
		return false;
	}

    /**
     * 验证账号ID是否存在
     * @param $group
     * @param $userID
     * @return bool|void
     * @throws Exception
     */
    public function _checkUserIDExists($group, $userID) {
        if(!check_value($group)) return false;
        if(!Validator::UnsignedNumber($userID)) return false;
        $query = "SELECT ["._CLMN_MEMBID_."] FROM ["._TBL_MI_."] WHERE ["._CLMN_MEMBID_."] = ?";
        $result = Connection::Database('Me_MuOnline',$group)->query_fetch_single($query, [$userID]);
        if(is_array($result)) return true;
        return false;
    }
    /**
     * 验证账号密码是否存在
     * @param $group
     * @param $username
     * @param $password
     * @return bool|void
     * @throws Exception
     */
	public function validateUsername($group, $username, $password) {
	    if(!serverGroupIDExists($group)) return;
		if(!Validator::UsernameLength($username)) return;
		if(!Validator::AlphaNumeric($username)) return;
		if(!Validator::PasswordLength($password)) return;
		$servercode = getServerCodeForGroupID($group);

		$data = [
		    'servercode'    =>  $servercode,
			'username'      =>  $username,
			'password'      =>  $password
		];
		
		if($this->_md5Enabled) {
			$query = "SELECT "._CLMN_GROUP_.","._CLMN_USERNM_.","._CLMN_PASSWD_." FROM "._TBL_MI_." WHERE "._CLMN_GROUP_." = :servercode AND "._CLMN_USERNM_." = :username AND "._CLMN_PASSWD_." = [dbo].[fn_md5]('".$password."', '".$username."')";
		} else {
			$query = "SELECT "._CLMN_GROUP_.","._CLMN_USERNM_.","._CLMN_PASSWD_." FROM "._TBL_MI_." WHERE "._CLMN_GROUP_." = :servercode AND "._CLMN_USERNM_." = :username AND "._CLMN_PASSWD_." = :password";
		}
		
		$result = Connection::Database('Me_MuOnline',$group)->query_fetch_single($query, $data);
		if(is_array($result)) return true;
		return false;
	}

    /**
     * 从账号获取账号组ID
     * @param $group
     * @param $username
     * @return mixed|void
     * @throws Exception
     */
	public function getUserGIDForUsername($group, $username) {
		if(!Validator::UsernameLength($username)) return;
		if(!Validator::AlphaNumeric($username)) return;
		$result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT "._CLMN_MEMBID_." FROM "._TBL_MI_." WHERE "._CLMN_USERNM_." = ?", [$username]);
		if(is_array($result)) return $result[_CLMN_MEMBID_];
		return;
	}

    /**
     * 获取账号从邮箱地址
     * @param $group
     * @param $email
     * @return mixed|void
     * @throws Exception
     */
	public function getUserGIDForEmail($group, $email) {
		if(!$this->_checkEmailExists($group,$email)) return;
		$result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT "._CLMN_MEMBID_." FROM "._TBL_MI_." WHERE "._CLMN_EMAIL_." = ?", [$email]);
		if(is_array($result)) return $result[_CLMN_MEMBID_];
		return;
	}

    /**
     * 获取账号信息从账号ID
     * @param $group
     * @param $Gid
     * @return mixed|void|null
     * @throws Exception
     */
	public function getUserInfoForUserGID($group, $Gid) {
		if(!Validator::Number($group)) return;
		if(!check_value($Gid)) return;
		$result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT * FROM "._TBL_MI_." WHERE "._CLMN_MEMBID_." = ?", [$Gid]);
		if(!is_array($result)) return;
		return $result;
	}

    /**
     * @param $group
     * @param $guid
     * @return mixed|void
     * @throws Exception
     */
    public function getUsernameForUserGID($group, $guid)
    {
        if(!Validator::Number($group)) return;
        if(!check_value($guid)) return;
        $result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT "._CLMN_USERNM_." FROM "._TBL_MI_." WHERE "._CLMN_MEMBID_." = ?", [$guid]);
        if(!is_array($result)) return;
        return $result[_CLMN_USERNM_];
	}

    /**
     * 获取账号是否在线
     * @param $group
     * @param $username
     * @return bool|void
     * @throws Exception
     */
	public function checkUserOnline($group, $username) {
		if(!Validator::UsernameLength($username)) return;
		if(!Validator::AlphaNumeric($username)) return;
		$result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT "._CLMN_CONNSTAT_." FROM "._TBL_MS_." WHERE "._CLMN_USERNM_." = ? AND "._CLMN_CONNSTAT_." = ?", [$username, 1]);
		if(is_array($result)) return true;
		return false;
	}

    /**
     * @param $group
     * @param $id
     * @param $username
     * @param $new_password
     * @return bool|void
     * @throws Exception
     */
	public function changePassword($group,$id,$username,$new_password) {
		if(!Validator::UnsignedNumber($id)) return;
		if(!Validator::UsernameLength($username)) return;
		if(!Validator::AlphaNumeric($username)) return;
		if(!Validator::PasswordLength($new_password)) return;
		
		if($this->_md5Enabled) {
			$data = [
			    'userid' => $id,
                'username' => $username,
                'password' => $new_password
            ];
			$query = "UPDATE "._TBL_MI_." SET "._CLMN_PASSWD_." = [dbo].[fn_md5]('".$new_password."', '".$username."') WHERE "._CLMN_MEMBID_." = :userid";
		} else {
			$data = array('userid' => $id, 'password' => $new_password);
			$query = "UPDATE "._TBL_MI_." SET "._CLMN_PASSWD_." = :password WHERE "._CLMN_MEMBID_." = :userid";
		}

		$result = Connection::Database('Me_MuOnline',$group)->query($query, $data);
		if($result) return true;
		return;
	}

    /**
     * @param $userid
     * @param $new_password
     * @param $auth_code
     * @return bool|void
     * @throws Exception
     */
	public function addPasswordChangeRequest($userid,$new_password,$auth_code) {
		if(!check_value($userid)) return;
		if(!check_value($new_password)) return;
		if(!check_value($auth_code)) return;
		if(!Validator::PasswordLength($new_password)) return;
		
		$data = array(
			$userid,
			$new_password,
			$auth_code,
			time()
		);
		
		$query = "INSERT INTO [".X_TEAM_PASSCHANGE_REQUEST."] (user_id,new_password,auth_code,request_date) VALUES (?, ?, ?, ?)";
		$result = Connection::Database('Web')->query($query, $data);
		if($result) return true;
		return;
	}

    /**
     * @param $userid
     * @return bool|void
     * @throws Exception
     */
	public function hasActivePasswordChangeRequest($userid) {
		if(!check_value($userid)) return;
		
		$result = Connection::Database('Web')->query_fetch_single("SELECT user_id FROM [".X_TEAM_PASSCHANGE_REQUEST."] WHERE user_id = ?", [$userid]);
		if(!is_array($result)) return;
		
		$configs = loadConfigurations('usercp.mypassword');
		if(!is_array($configs)) return;
		
		$request_timeout = $configs['change_password_request_timeout'] * 3600;
		$request_date = $result['request_date'] + $request_timeout;
		if(time() < $request_date) return true;
		
		$this->removePasswordChangeRequest($userid);
		return;
	}

    /**
     * @param $userid
     * @return bool|void
     * @throws Exception
     */
	public function removePasswordChangeRequest($userid) {
		$result = Connection::Database('Web')->query("DELETE FROM [".X_TEAM_PASSCHANGE_REQUEST."] WHERE user_id = ?", [$userid]);
		if($result) return true;
		return;
	}

    /**
     * @param $user_id
     * @param $auth_code
     * @return string
     */
	public function generatePasswordChangeVerificationURL($user_id,$auth_code) {
		$build_url = __BASE_URL__;
		$build_url .= 'verifyemail/';
		$build_url .= '?op='; // operation
		$build_url .= 1;
		$build_url .= '&uid=';
		$build_url .= $user_id;
		$build_url .= '&ac=';
		$build_url .= $auth_code;
		return $build_url;
	}

    /**
     * 禁用账号
     * @param $group
     * @param $userid
     * @return bool|void
     * @throws Exception
     */
	public function blockAccount($group,$userid) {
		if(!check_value($userid)) return;
		if(!Validator::UnsignedNumber($userid)) return;
		$result = Connection::Database('Me_MuOnline',$group)->query("UPDATE "._TBL_MI_." SET "._CLMN_BLOCCODE_." = ? WHERE "._CLMN_MEMBID_." = ?", array(1, $userid));
		if($result) return true;
		return;
	}

    /**
     * @param $transaction_id
     * @param $user_id
     * @param $payment_amount
     * @param $paypal_email
     * @param $order_id
     * @return bool|void
     * @throws Exception
     */
	public function payPalTransaction($transaction_id,$user_id,$payment_amount,$paypal_email,$order_id) {
		if(!check_value($transaction_id)) return;
		if(!check_value($user_id)) return;
		if(!check_value($payment_amount)) return;
		if(!check_value($paypal_email)) return;
		if(!check_value($order_id)) return;
		if(!Validator::UnsignedNumber($user_id)) return;
		
		$data = array(
			$transaction_id,
			$user_id,
			$payment_amount,
			$paypal_email,
			time(),
			1,
			$order_id
		);
		
		$query = "INSERT INTO [".X_TEAM_PAYPAL_TRANSACTIONS."] (transaction_id, user_id, payment_amount, paypal_email, transaction_date, transaction_status, order_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
		$result = Connection::Database('Web')->query($query, $data);
		if($result) return true;
		return;
	}

    /**
     * 背包交易请求更新状态
     * @param $order_id
     * @return bool|void
     * @throws Exception
     */
	public function payPalTransactionReversedUpdateStatus($order_id) {
		if(check_value($order_id)) return;
		$result = Connection::Database('Web')->query("UPDATE [".X_TEAM_PAYPAL_TRANSACTIONS."] SET transaction_status = ? WHERE order_id = ?", array(0, $order_id));
		if($result) return true;
		return;
	}

    /**
     * 生成密码请求代码
     * @param $userid
     * @param $username
     * @return string|void
     */
	public function generateAccountRecoveryCode($userid,$username) {
		if(!check_value($userid)) return;
		if(!check_value($username)) return;
		return md5($userid . $username . date("m-d-Y"));
	}

    /**
     * 校验是否已被屏蔽
     * @param $ip
     * @return bool|void
     * @throws Exception
     */
	public function isIpBlocked($ip) {
		if(!Validator::Ip($ip)) return true;
		$result = Connection::Database('Web')->query_fetch_single("SELECT * FROM ".X_TEAM_BLOCKED_IP." WHERE block_ip = ?",[$ip]);
		if(!is_array($result)) return;
		return true;
	}

    /**
     * 封停IP地址
     * @param $ip
     * @param $user
     * @return bool|void
     * @throws Exception
     */
	public function blockIpAddress($ip,$user) {
		if(!check_value($user)) return;
		if(!Validator::Ip($ip)) return;
		if($this->isIpBlocked($ip)) return;
		$result = Connection::Database('Web')->query("INSERT INTO ".X_TEAM_BLOCKED_IP." (block_ip,block_by,block_date) VALUES (?,?,?)", [$ip,$user,time()]);
		if(!$result) return;
		$this->_updateBlockedIpCache();
		return true;
	}

    /**
     * 检索被封停的IP
     * @return array|bool|void|null
     * @throws Exception
     */
	public function retrieveBlockedIPs() {
        $result = Connection::Database('Web')->query_fetch("SELECT * FROM ".X_TEAM_BLOCKED_IP." ORDER BY id DESC");
        if(!is_array($result)) return;
        return $result;
	}

    /**
     * 解封IP地址
     * @param $id
     * @return bool|void
     * @throws Exception
     */
	public function unblockIpAddress($id) {
		if(!check_value($id)) return;
		$result = Connection::Database('Web')->query("DELETE FROM ".X_TEAM_BLOCKED_IP." WHERE id = ?", array($id));
		if(!$result) return;
		
		$this->_updateBlockedIpCache();
		return true;
	}

    /**
     * 更新禁用IP的缓存数据
     * @throws Exception
     */
	protected function _updateBlockedIpCache() {
		$blockedIps = $this->retrieveBlockedIPs();
		if(!is_array($blockedIps)) {
			updateCacheFile($this->_blockedIpCache, "");
			return;
		}
        $ipList = [];
		foreach($blockedIps as $row) {
			$ipList[] = $row['block_ip'];
		}
		if(!is_array($ipList)) return;
		$cacheData = encodeCache($ipList);
		updateCacheFile($this->_blockedIpCache, $cacheData);
	}

    /**
     * 更新邮件地址
     * @param $group
     * @param $userid
     * @param $newEmail
     * @return bool|void
     * @throws Exception
     */
	public function updateEmail($group, $userid, $newEmail) {
		if(!Validator::UnsignedNumber($userid)) return;
		if(!Validator::Email($newEmail)) return;
		$result = Connection::Database('Me_MuOnline',$group)->query("UPDATE "._TBL_MI_." SET "._CLMN_EMAIL_." = ? WHERE "._CLMN_MEMBID_." = ?", [$newEmail, $userid]);
		if($result) return true;
		return;
	}

    /**
     * EG验证二级密码
     * @param $group
     * @param $username
     * @param $level2Password
     * @return bool|void
     * @throws Exception
     */
    public function checkLevel2Password($group, $username, $level2Password)
    {
        if(!check_value($group)) return;
        if(!check_value($username)) return;
        if(!check_value($level2Password)) return;
        $regConfig = loadConfigurations('register');

        $result = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT "._CLMN_SNONUMBER_." FROM "._TBL_MI_." WHERE "._CLMN_USERNM_." = ?", [$username]);
        if(!$result) return;
        $check=$result[_CLMN_SNONUMBER_];
		$check = substr($check,-7);
		if($level2Password == $check) return true;
        return;
    }

    /**
     * 获取游戏大区从账号
     * @param $username
     * @param bool $userID 条件判断用账号查找还是userID查找
     * @return bool|void
     * @throws Exception
     */
	public function getGroupForUsername($username,$userID = false){
        if(!check_value($username)) throw new Exception('您必须填写表单中的所有字段。');
        if(!Validator::UsernameLength($username)) throw new Exception('用户名长度必须为4到10个字符。');
        if(!Validator::AlphaUsername($username)) throw new Exception('用户名只能是小写字母或数字。');
        $WHERE = ($userID) ? _CLMN_MEMBID_ : _CLMN_USERNM_;
        global $serverGrouping;
        foreach ($serverGrouping AS $key=>$item) {
            $query = "SELECT ["._CLMN_GROUP_."] FROM "._TBL_MI_." WHERE [".$WHERE."] = ?";
            $database = Connection::Database('Me_MuOnline', $key);
            $databaseData[$key] = $database->query_fetch_single($query,[$username]);
            if($databaseData[$key]) return $databaseData[$key][_CLMN_GROUP_];
        }
        return;
    }
    /**
     * 统计所有大区的账号数量
     * @return float|int
     * @throws Exception
     */
    public function getServerAccountCount(){
        global $serverGrouping;
        $totalAccounts = [];
        $i = 0;
        foreach ($serverGrouping as $key=>$item){
            // 帐户总数
            $totalAccounts[] = Connection::Database('Me_MuOnline',$key)->query_fetch_single("SELECT COUNT(*) as result FROM "._TBL_MI_." where "._CLMN_GROUP_." = ?",[$item['SERVER_GROUP']]);
            $i++;
        }
        return array_sum(array_column($totalAccounts,'result'));
    }

    /**
     * 统计所有大区的在线人数
     * @return float|int
     * @throws Exception
     */
    public function getServerOnlineCount(){
        global $serverGrouping;
        $bannedAccounts = [0];
        foreach ($serverGrouping as $key=>$item) {
            $bannedAccounts[] = Connection::Database('Me_MuOnline', $key)->query_fetch_single("SELECT COUNT(*) as result FROM "._TBL_MS_." as OnlineSystem LEFT JOIN "._TBL_MI_." as AccountSystem ON AccountSystem."._CLMN_USERNM_." = OnlineSystem."._CLMN_MS_MEMBID_." WHERE OnlineSystem."._CLMN_CONNSTAT_." = 1 and AccountSystem."._CLMN_GROUP_." = ".$item['SERVER_GROUP']);
        }
        return array_sum(array_column($bannedAccounts,'result'));
    }

    /**
     * 统计所有大区的总角色数
     * @return float|int
     * @throws Exception
     */
    public function getServerCharacterCount(){
        global $serverGrouping;
        $totalCharacters = [0];
        foreach ($serverGrouping as $key=>$item) {
            $Db = ($item['SQL_USE_2_DB']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
            $totalCharacters[] = Connection::Database('MuOnline', $key)->query_fetch_single("SELECT COUNT(*) as result FROM "._TBL_CHR_." AS CharacterSystem LEFT JOIN [".$Db."].[dbo]."._TBL_MI_." AS AccountSystem ON AccountSystem."._CLMN_USERNM_." = CharacterSystem."._CLMN_CHR_ACCID_." where AccountSystem."._CLMN_GROUP_." = ".$item['SERVER_GROUP']);
        }
        return array_sum(array_column($totalCharacters,'result'));
    }

    /**
     * 获取账号从角色名
     * @param $group
     * @param $char_name
     * @return mixed|void
     * @throws Exception
     */
    public function getUsernameForCharacterName($group, $char_name)
    {
        if(!check_value($group)) return;
        if(!check_value($char_name)) return;
        $data = Connection::Database("MuOnline",$group)->query_fetch_single("SELECT [AccountID] from [Character] WHERE [Name] = ?",[$char_name]);
        if(!is_array($data)) return;
        return $data['AccountID'];
    }
}