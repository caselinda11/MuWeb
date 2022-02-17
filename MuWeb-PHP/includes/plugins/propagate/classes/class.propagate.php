<?php
/**
 * 每日任务类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Connection;
use Plugin\Market\warehouse;
use Validator;
use Token;
use Vote;

class propagate {

	private $_modulesPath = 'modules';
    private $serverFiles;
    private $mconfig,$Web;
    private $remainingTime;
    /**
     * 构造函数
     * propagate constructor.
     * @throws Exception
     */
	public function __construct()
    {
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        $this->Web = Connection::Database('Web');
        $this->mconfig = $this->loadConfig();
    }

    /**
     * 添加推广网址
     * @param $title
     * @param $link
     * @param $reward
     * @param $time
     * @return bool
     * @throws Exception
     */
    public function addVoteSite($title, $link, $reward, $time) {
        if(!check_value($title)) throw new Exception('请正确输入任务标题!');
        if(!check_value($link)) throw new Exception('请正确输入任务链接地址!');
        if(!check_value($reward)) throw new Exception('请正确输入物品代码!');
        if(!Validator::Items($reward)) throw new Exception('请正确输入物品代码!');
        if(!check_value($time)) throw new Exception('请正确输入时间限制!');
        if(!Validator::Number($time)) throw new Exception('时间必须为存数字,请正确输入时间限制!');
        #给奖励的货币数设定一个0值
        $votesite_reward = 0;
        $result = $this->Web->query("INSERT INTO ".X_TEAM_VOTE_SITES." (votesite_title,votesite_link,votesite_reward,votesite_time,reward_item) VALUES (?,?,?,?,?)", [$title,$link,$votesite_reward,$time,$reward]);
        if($result) return true;
        return false;
    }

    /**
     * @param $group
     * @param $userID
     * @param $username
     * @param $IP
     * @param $ID
     * @param $MachineID
     * @throws Exception
     */
    public function setPropagate($group, $userID, $username, $IP, $ID, $MachineID)
    {
        if(!Token::checkToken('propagate__'.$ID)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('表单提交无效,请重新登陆尝试!');
        if(!check_value($userID)) throw new Exception('表单提交无效,请重新登陆尝试!');
        if(!check_value($username)) throw new Exception('表单提交无效,请重新登陆尝试!');
        if(!check_value($IP)) throw new Exception('表单提交无效,请重新登陆尝试!');
        if(!check_value($ID)) throw new Exception('表单提交无效,请重新登陆尝试!');
        if(!check_value($MachineID)) throw new Exception('表单提交无效,请重新登陆尝试!');
        #获取数据
        $vote = new Vote();
        $data = $vote->retrieveVoteSite($ID);
        if(!is_array($data)) throw new Exception('该任务已经停用,请尝试其他任务!');

        $voteLink = $data['votesite_link'];
        #状态
        if(!$this->mconfig['active']) throw new Exception('该功能暂时禁用,请稍后再试或联系在线客服!');

        #验证
        if(!$this->_checkUserIDRecord($userID,$ID)) throw new Exception('您的账号已在限定的时间内领取过该任务，请在['.date("Y-m-d h:i:s",$this->remainingTime).']后再来！');

        # 检查ip是否可以投票
        if(!$this->_canIPVote($IP,$ID)) throw new Exception('您的IP地址已经在过去时限内领取过该奖励，请过时间后再来！');

        # 检查机器码是否可以投票
        if(!$this->_canMachineId($ID,$MachineID)) throw new Exception('您的IP地址已经在过去时限内领取过该奖励，请过时间后再来！');
        #添加记录时间
        $timestamp = time() + $data['votesite_time']*60*60;
        $newWarehouse = $this->warehouseAddItem($group,$data['reward_item']);
        $muOnline = Connection::Database("MuOnline",$group);

        try {
            $muOnline->beginTransaction();
            $this->Web->beginTransaction();
            $muOnline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?", [$newWarehouse, $username]);
            $this->Web->query("INSERT INTO ".X_TEAM_VOTES." (user_id, user_ip, user_machine, vote_site_id, timestamp) VALUES (?, ?, ?, ?, ?)", [$userID,$IP,$MachineID,$ID,$timestamp]);
            # 添加推广日志
            $this->Web->query("INSERT INTO ".X_TEAM_VOTE_LOGS." (servercode,user_id,votesite_id,timestamp) VALUES (?,?,?,?)", [getServerCodeForGroupID($group),$userID,$ID,time()]);
            $muOnline->commit();
            $this->Web->commit();
        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->Web->rollBack();
            global $config;
            if(!$config['error_reporting'])
                throw new Exception('提交错误,请联系管理员验证数据的正确性!');
            else
                throw new Exception($exception->getMessage());
        }

        # 重定向
        redirect(3, $voteLink);
        message('success','您已经成功完成任务,奖励已发放至您的账户中!');
    }

    /**
     * 仓库添加物品
     * @param $group
     * @param $itemCode
     * @return string
     * @throws Exception
     */
    private function warehouseAddItem($group, $itemCode){
        $warehouse = new warehouse($group);
        #操作仓库
        $newWarehouse = $warehouse->setWarehouseAddNewItem($itemCode);

        if(!$newWarehouse) throw new Exception("[错误][1]仓库没有足够的位置存放该物品,请先整理一下您的仓库!");
        #拼接
        $newWarehouse = $newWarehouse.$warehouse->extendWarehouseData;
        #为了安全二次校验
//        if(!stripos($newWarehouse,$itemCode)) throw new Exception("[错误][2]操作失败,物品错误!");
        return $newWarehouse;
    }

    /**
     * 验证账号id是否有记录
     * @param $ID
     * @param $userID
     * @return bool|void
     * @throws Exception
     */
    private function _checkUserIDRecord($userID, $ID) {
        if(!check_value($userID)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
        if(!check_value($ID)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
        $query = "SELECT * FROM ".X_TEAM_VOTES." WHERE user_id = ? AND vote_site_id = ?";
        $check = $this->Web->query_fetch_single($query, [$userID, $ID]);
        if(!is_array($check)) return true;
        if($this->_timePassed($check['timestamp'])) {
            if($this->_removeRecord($check['id'])) return true;
        }
        return false;
    }

    /**
     * 验证IP是否有记录
     * @param $ID
     * @param $IP
     * @return bool|void
     * @throws Exception
     */
    private function _canIPVote($IP,$ID) {
        if(!check_value($IP)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
        if(!check_value($ID)) throw new Exception('出现意外错误，请与我们的在线客服联系。');
        
        $query = "SELECT * FROM ".X_TEAM_VOTES." WHERE user_ip = ? AND vote_site_id = ?";
        $check = $this->Web->query_fetch_single($query, [$IP, $ID]);

        if(!is_array($check)) return true;
        if($this->_timePassed($check['timestamp'])) {
            if($this->_removeRecord($check['id'])) return true;
        }
        return;
    }

    /**
     * 校验机器码是否有记录
     * @param $ID
     * @param $MachineID
     * @return bool|void
     * @throws Exception
     */
    private function _canMachineId($ID,$MachineID){
        if(!check_value($MachineID)) throw new Exception('机器码无效!');
        if(!check_value($ID)) throw new Exception('出现意外错误，请与我们的在线客服联系。');

        $query = "SELECT * FROM ".X_TEAM_VOTES." WHERE user_machine = ? AND vote_site_id = ?";
        $check = $this->Web->query_fetch_single($query, [$MachineID, $ID]);

        if(!is_array($check)) return true;
        if($this->_timePassed($check['timestamp'])) {
            if($this->_removeRecord($check['id'])) return true;
        }
        return;
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
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载系统任务插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载系统任务插件模块。');
		if(!@include_once(__PATH_PLUGIN_PROPAGATE_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载系统任务插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_PLUGIN_PROPAGATE_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_PLUGIN_PROPAGATE_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('系统任务配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载系统任务插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}