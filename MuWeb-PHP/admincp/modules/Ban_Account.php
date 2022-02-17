<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="<?=admincp_base()?>">官方主页</a>
                    </li>
                    <li class="breadcrumb-item active">禁用系统</li>
                    <li class="breadcrumb-item active">封停账号</li>
                </ol>
            </div>
            <h4 class="page-title">封停账号</h4>
        </div>
    </div>
</div>
<?php
	$database = Connection::Database("Web");
	
	// 添加禁令系统cron（如果不存在）
	$banCron = "INSERT INTO ".X_TEAM_CRON." (cron_name, cron_description, cron_file_run, cron_run_time, cron_status, cron_protected, cron_file_md5) VALUES ('禁用系统', '计划定时任务解除临时锁定!', 'temporal_bans.php', '3600', 1, 1, '1a3787c5179afddd1bfb09befda3d1c7')";
	$checkBanCron = $database->query_fetch_single("SELECT * FROM ".X_TEAM_CRON." WHERE cron_file_run = ?", array("temporal_bans.php"));
	if(!is_array($checkBanCron)) $database->query($banCron);
	
	if(check_value($_POST['submit_ban'])) {
		try {
            $common = new common();
		    $group = getGroupIDForServerCode($_POST['group']);
			if(!check_value($_POST['ban_account'])) throw new Exception("请输入帐号!");
			if(!$common->_checkUsernameExists($group,$_POST['ban_account'])) throw new Exception("无效账号!");
			if(!check_value($_POST['ban_days'])) throw new Exception("请输入天数!");
			if(!Validator::UnsignedNumber($_POST['ban_days'])) throw new Exception("锁定时间无效.");
			if(check_value($_POST['ban_reason'])) {
				if(!Validator::Length($_POST['ban_reason'], 100, 1)) throw new Exception("无效的锁定原因.");
			}
			
			// 检查在线状态
			if($common->checkUserOnline($group,$_POST['ban_account'])) throw new Exception("该帐号当前在线.");
			
			// 账号信息
			$userID = $common->getUserGIDForUsername($group,$_POST['ban_account']);
			$accountData = $common->getUserInfoForUserGID($group,$userID);
			
			// 检查是否已被禁止
			if($accountData[_CLMN_BLOCCODE_] == 1) throw new Exception("该帐号已被锁定.");
			
			// 禁用类型
			$banType = ($_POST['ban_days'] >= 1 ? "temporal" : "permanent");
			
			// 禁用日志
			$banLogData = [
			    'servercode'  =>  $_POST['group'],
				'acc' => $_POST['ban_account'],
				'by' => $_SESSION['username'],
				'type' => $banType,
				'date' => time(),
				'days' => $_POST['ban_days'],
				'reason' => (check_value($_POST['ban_reason']) ? $_POST['ban_reason'] : "")
			];
			
			$logBan = $database->query("INSERT INTO ".X_TEAM_BAN_LOG." (servercode, account_id, banned_by, ban_type, ban_date, ban_days, ban_reason) VALUES (:servercode, :acc, :by, :type, :date, :days, :reason)", $banLogData);
			if(!$logBan) throw new Exception("无法创建锁定日志(检查数据表)[1].");
			
			// 添加临时禁令
			if($banType == "temporal") {
				$tempBanData = [
                    'servercode'  =>    $_POST['group'],
                    'acc' => $_POST['ban_account'],
					'by' => $_SESSION['username'],
					'date' => time(),
					'days' => $_POST['ban_days'],
					'reason' => (check_value($_POST['ban_reason']) ? $_POST['ban_reason'] : "")
				];
				$tempBan = $database->query("INSERT INTO ".X_TEAM_BANS." (servercode, account_id, banned_by, ban_date, ban_days, ban_reason) VALUES (:servercode, :acc, :by, :date, :days, :reason)", $tempBanData);
				if(!$tempBan) throw new Exception("无法添加临时锁定(检查数据表)[2]. - " . $database->error);
			}
			
			// 禁用账号
			$banAccount = Connection::Database("Me_MuOnline",$group)->query("UPDATE "._TBL_MI_." SET "._CLMN_BLOCCODE_." = ? WHERE "._CLMN_USERNM_." = ?", array(1, $_POST['ban_account']));
			if(!$banAccount) throw new Exception("无法锁定账号(检查数据表)[3]");
			
			message('success', '帐户已锁定');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
?>
<div class="card">
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <form action="" method="post" role="form">
                    <div class="form-group">
                        <label for="group">分区</label>
                        <select id="group" name="group" class="form-control">
                            <?foreach (getServerGroupList() as $key=> $value){?>
                            <option value="<?=$key?>"><?=$value?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="acc">账号</label>
                        <input type="text" name="ban_account" class="form-control" id="acc">
                    </div>
                    <div class="form-group">
                        <label for="days">天数 [0为永久锁定]</label>
                        <input type="text" name="ban_days" class="form-control" id="days" value="0">
                    </div>
                    <div class="form-group">
                        <label for="reason">原因 [选填]</label>
                        <input type="text" name="ban_reason" class="form-control" id="reason">
                    </div>
                    <div style="text-align:center">
                        <button type="submit" name="submit_ban" value="submit" class="btn btn-success waves-effect waves-light col-md-12">封停</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
