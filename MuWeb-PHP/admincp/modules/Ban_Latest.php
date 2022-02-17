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
                    <li class="breadcrumb-item active">最近封停</li>
                </ol>
            </div>
            <h4 class="page-title">最近封停 - [最近50次账号锁定记录]</h4>
        </div>
    </div>
</div>
<?php
	$database = Connection::Database("Web");
	
	if(check_value($_GET['liftban'])) {
		try {
			if(!Validator::UnsignedNumber($_GET['liftban'])) throw new Exception("无效的禁用ID!");
			
			// 取回禁令信息
			$banInfo = $database->query_fetch_single("SELECT * FROM ".X_TEAM_BAN_LOG." WHERE id = ?", [$_GET['liftban']]);

			if(!is_array($banInfo)) throw new Exception("该封停不存在或已经解除限制!");
			
			// 检测用户是否在线
			//if($common->accountOnline($banInfo['account_id'])) throw new Exception("该账号目前在线!");
			
			// 解封账号
            $query = "UPDATE "._TBL_MI_." SET "._CLMN_BLOCCODE_." = 0 WHERE "._CLMN_USERNM_." = ?";

            $group = getGroupIDForServerCode($banInfo['servercode']);
			$unBan = Connection::Database("Me_MuOnline",$group)->query($query, [$banInfo['account_id']]);
			if(!$unBan) throw new Exception("无法更新帐户信息[解锁]！");
			
			// 移除封停日志
			$database->query("DELETE FROM ".X_TEAM_BAN_LOG." WHERE account_id = ?", array($banInfo['account_id']));
			$database->query("DELETE FROM ".X_TEAM_BANS." WHERE account_id = ?", array($banInfo['account_id']));
			
			message('success', '账号解锁成功！');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
?>
<div class="card">
	<div class="col-md-12">
		<div class="card-body">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#temp" data-toggle="tab" aria-expanded="true">临时封停</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="#perm" data-toggle="tab" aria-expanded="false">永久封停</a>
                </li>
            </ul>
			<!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade active show" id="temp">
                    <?php
                    $tBans = $database->query_fetch("SELECT TOP 25 * FROM ".X_TEAM_BAN_LOG." WHERE ban_type = ? ORDER BY id DESC", array("temporal"));
                    if(is_array($tBans)) {
                        echo '<table class="table table-condensed text-center">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>账号</th>';
                        echo '<th>锁定</th>';
                        echo '<th>日期</th>';
                        echo '<th>天</th>';
                        echo '<th>原因</th>';
                        echo '<th>操作</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        foreach($tBans as $temporalBan) {
                            echo '<tr>';
                            echo '<td>'.$temporalBan['account_id'].'</td>';
                            echo '<td>'.$temporalBan['banned_by'].'</td>';
                            echo '<td>'.date("Y-m-d H:i", $temporalBan['ban_date']).'</td>';
                            echo '<td>'.$temporalBan['ban_days'].'</td>';
                            echo '<td>'.$temporalBan['ban_reason'].'</td>';
                            echo '<td><a href="index.php?module='.$_REQUEST['module'].'&liftban='.$temporalBan['id'].'" class="btn btn-danger btn-xs">解锁</a></td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        message('warning', '没有临时封停记录!');
                    }
                    ?>
                </div>
                <div class="tab-pane fade" id="perm">
                    <?php
                    $pBans = $database->query_fetch("SELECT TOP 25 * FROM ".X_TEAM_BAN_LOG." WHERE ban_type = ? ORDER BY id DESC", array("permanent"));
                    if(is_array($pBans)) {
                        echo '<table  id="datatable" class="table table-condensed">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th>账号</th>';
                        echo '<th>锁定</th>';
                        echo '<th>日期</th>';
                        echo '<th>原因</th>';
                        echo '<th></th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';
                        foreach($pBans as $permanentBan) {
                            echo '<tr>';
                            echo '<td>'.$permanentBan['account_id'].'</td>';
                            echo '<td>'.$permanentBan['banned_by'].'</td>';
                            echo '<td>'.date("Y-m-d H:i", $permanentBan['ban_date']).'</td>';
                            echo '<td>'.$permanentBan['ban_reason'].'</td>';
                            echo '<td style="text-align:right;"><a href="index.php?module='.$_REQUEST['module'].'&liftban='.$permanentBan['id'].'" class="btn btn-danger btn-xs">解锁</a></td>';
                            echo '</tr>';
                        }
                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        message('warning', '没有永久性封停记录!');
                    }
                    ?>
                </div>
			</div>
		</div>
	</div>
</div>