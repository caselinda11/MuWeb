<?php
/**
 * 推广类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Vote {
	
	private $_userid;
	private $_username;
	private $_votesIdeId;
	private $_accountInfo;
	private $_ip;
	private $_MachineId;

	private $_configXml = 'usercp.vote.xml';
	private $_active = true;
	private $_saveLogs = true;
	private $_creditConfig;
    private $common,$xml,$Web;
    private $remainingTime; #剩余时间
    /**
     * 构造函数
     * Vote constructor.
     * @throws Exception
     */
	function __construct() {
		// 基类加载
		$this->common = new common();
		$this->Web = Connection::Database('Web');
		# 加载配置文件
		$this->xml = simplexml_load_file(__PATH_INCLUDES_CONFIGS_MODULE__ . $this->_configXml);
		if(!$this->xml) throw new Exception('无法加载任务系统配置。');
		
		$xmlConfig = convertXML($this->xml);
		$this->_active = $xmlConfig['active'];
		$this->_saveLogs = $xmlConfig['vote_save_logs'];
		$this->_creditConfig = $xmlConfig['credit_config'];
	}

    /**
     * 初始化传值Id
     * @param $group
     * @param $userid
     * @throws Exception
     */
	public function setUserId($group,$userid) {
		if(!check_value($userid)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!Validator::UnsignedNumber($userid)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$accountInfo = $this->common->getUserInfoForUserGID($group,$userid);
		if(!is_array($accountInfo)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$this->_accountInfo = $accountInfo;
		$this->_userid = $userid;
		$this->_username = $this->_accountInfo[_CLMN_USERNM_];
	}

    /**
     * @param $voteSiteId
     * @throws Exception
     */
	public function setVoteSiteId($voteSiteId) {
		if(!check_value($voteSiteId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!Validator::UnsignedNumber($voteSiteId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!$this->_siteExists($voteSiteId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$this->_votesIdeId = $voteSiteId;
	}

    /**
     * 设置IP地址
     * @param $ip
     * @throws Exception
     */
	public function setIp($ip) {
		if(!check_value($ip)) throw new Exception('IP地址无效。');
		if(!Validator::Ip($ip)) throw new Exception('IP地址无效。');
		
		$this->_ip = $ip;
	}

    /**
     * 设置机器码
     * @param $MachineId
     * @throws Exception
     */
    public function setMachineId($MachineId)
    {
        if(!check_value($MachineId)) throw new Exception('无法检测到您电脑的机器码!');
        $this->_MachineId = $MachineId;
	}

    /**
     * 推广过程
     * @param $group
     * @throws Exception
     */
	public function vote($group) {
		if(!check_value($this->_userid)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!check_value($this->_ip)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!check_value($this->_votesIdeId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		# 检查模块是否启用
		if(!$this->_active) throw new Exception('该功能暂已关闭，请稍后尝试访问。');

		# 检查货币配置
		if(!$this->_creditConfig) throw new Exception('货币尚未配置，请稍后再试或联系在线客服。');

		# 检查用户是否可以投票
		if(!$this->_canUserVote()) throw new Exception('您的账号已在限定的时间内领取过该任务，请在['.date("Y-m-d h:i:s",$this->remainingTime).']后再来！');

		# 检查ip是否可以投票
		if(!$this->_canIPVote()) throw new Exception('您的IP地址已经在过去时限内领取过该奖励，请过时间后再来！');

		# 检查机器码是否可以投票
        if(!$this->_canMachineId()) throw new Exception('您的IP地址已经在过去时限内领取过该奖励，请过时间后再来！');

		# 检索票务数据
		$voteSite = $this->retrieveVoteSite($this->_votesIdeId);
		if(!is_array($voteSite)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$voteLink = $voteSite['votesite_link'];
		$creditsReward = $voteSite['votesite_reward'];

		# reward user
		$creditSystem = new CreditSystem();

		$creditSystem->setConfigId($this->_creditConfig);
		$configSettings = $creditSystem->showConfigs(true);
		switch($configSettings['config_user_col_id']) {
			case 'userid':
				$creditSystem->setIdentifier($this->_userid);
				break;
			case 'username':
				$creditSystem->setIdentifier($this->_username);
				break;
			default:
				throw new Exception('[货币系统] 尚未设置用户标识符。');
		}
		# 奖励积分
		$creditSystem->addCredits($group,$creditsReward);
		# 添加记录
		$this->_addRecord();
		# 添加推广日志
		if($this->_saveLogs) {
			$this->_logVote($group);
		}
		
		# 重定向
		redirect(3, $voteLink);
        message('success','您已经成功完成任务,奖励已发放至您的账户中!');
	}

    /**
     * 验证账号id是否有记录
     * @return bool|void
     * @throws Exception
     */
	private function _canUserVote() {
		if(!check_value($this->_userid)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!check_value($this->_votesIdeId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$query = "SELECT * FROM ".X_TEAM_VOTES." WHERE user_id = ? AND vote_site_id = ?";
		$check = $this->Web->query_fetch_single($query, [$this->_userid, $this->_votesIdeId]);
		
		if(!is_array($check)) return true;
		if($this->_timePassed($check['timestamp'])) {
			if($this->_removeRecord($check['id'])) return true;
		}
        return;
	}

    /**
     * 验证IP是否有记录
     * @return bool|void
     * @throws Exception
     */
	private function _canIPVote() {
		if(!check_value($this->_ip)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!check_value($this->_votesIdeId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$query = "SELECT * FROM ".X_TEAM_VOTES." WHERE user_ip = ? AND vote_site_id = ?";
		$check = $this->Web->query_fetch_single($query, [$this->_ip, $this->_votesIdeId]);
		
		if(!is_array($check)) return true;
		if($this->_timePassed($check['timestamp'])) {
			if($this->_removeRecord($check['id'])) return true;
		}
        return;
	}

    /**
     * 校验机器码是否有记录
     * @return bool|void
     * @throws Exception
     */
	private function _canMachineId(){
        if(!check_value($this->_MachineId)) throw new Exception('机器码无效!');
        if(!check_value($this->_votesIdeId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');

        $query = "SELECT * FROM ".X_TEAM_VOTES." WHERE user_machine = ? AND vote_site_id = ?";
        $check = $this->Web->query_fetch_single($query, [$this->_MachineId, $this->_votesIdeId]);

        if(!is_array($check)) return true;
        if($this->_timePassed($check['timestamp'])) {
            if($this->_removeRecord($check['id'])) return true;
        }
        return;
    }

    /**
     * 添加推广记录
     * @throws Exception
     */
	private function _addRecord() {
		if(!check_value($this->_userid)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!check_value($this->_ip)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!check_value($this->_votesIdeId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$voteSiteInfo = $this->retrieveVoteSite($this->_votesIdeId);
		if(!is_array($voteSiteInfo)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		
		$timestamp = time() + $voteSiteInfo['votesite_time']*60*60;

		$data = [
			$this->_userid,
			$this->_ip,
            $this->_MachineId,
			$this->_votesIdeId,
			$timestamp
		];
		
		$add = $this->Web->query("INSERT INTO ".X_TEAM_VOTES." (user_id, user_ip, user_machine, vote_site_id, timestamp) VALUES (?, ?, ?, ?, ?)", $data);
		if(!$add) throw new Exception('出现意外错误，请与我们的在线客服联系。');
	}

    /**
     * 配置验证
     * @param $id
     * @return bool
     */
	private function _removeRecord($id) {
		$remove = $this->Web->query("DELETE FROM ".X_TEAM_VOTES." WHERE id = ?", [$id]);
		if($remove) return true;
		return false;
	}

    /**
     * 间隔时间验证
     * @param $timestamp
     * @return bool
     */
	private function _timePassed($timestamp) {
		if(time() > $timestamp) return true;
        $this->remainingTime = $timestamp;
		return false;
	}

    /**
     * 验证是否存在
     * @param $id
     * @return bool|void
     */
	private function _siteExists($id) {
		if(!check_value($id)) return;
		$check = $this->Web->query_fetch_single("SELECT * FROM ".X_TEAM_VOTE_SITES." WHERE votesite_id = ?", [$id]);
		if(is_array($check)) return true;
		return false;
	}

    /**
     * 记录推广日志
     * @param $group
     * @return bool
     * @throws Exception
     */
	private function _logVote($group) {
		if(!check_value($this->_userid)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
		if(!check_value($this->_votesIdeId)) throw new Exception('出现意外错误，请与我们的在线客服联系。');

        $servercode = getServerCodeForGroupID($group);
		$add_data = [
            $servercode,
			$this->_userid,
			$this->_votesIdeId,
			time()
		];
		
		$add_log = $this->Web->query("INSERT INTO ".X_TEAM_VOTE_LOGS." (servercode,user_id,votesite_id,timestamp) VALUES (?,?,?,?)", $add_data);
		if(!$add_log) return false;
		return true;
	}

    /**
     * 添加推广网址
     * @param $title
     * @param $link
     * @param $reward
     * @param $time
     * @return bool
     */
	public function addVoteSite($title, $link, $reward, $time) {
		$result = $this->Web->query("INSERT INTO ".X_TEAM_VOTE_SITES." (votesite_title,votesite_link,votesite_reward,votesite_time) VALUES (?,?,?,?)", [$title,$link,$reward,$time]);
		if($result) return true;
	}

    /**
     * 删除推广网站
     * @param $id
     * @return bool|void
     */
	public function deleteVoteSite($id) {
		if(!$this->_siteExists($id)) return;
		$result = $this->Web->query("DELETE FROM ".X_TEAM_VOTE_SITES." WHERE votesite_id = ?", [$id]);
		if($result) return $result;
	}

    /**
     * 检索投票站点
     * @param null $id
     * @return array|bool|mixed|null
     */
	public function retrieveVoteSite($id=null) {
		if(check_value($id)) return $this->Web->query_fetch_single("SELECT * FROM ".X_TEAM_VOTE_SITES." WHERE votesite_id = ?", [$id]);
		return $this->Web->query_fetch("SELECT * FROM ".X_TEAM_VOTE_SITES." ORDER BY votesite_id ASC");
	}

}