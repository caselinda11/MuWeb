<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

// Load Vote Class
$vote = new Vote();

function saveChanges() {
	global $_POST;
	foreach($_POST as $setting) {
		if(!check_value($setting)) {
			message('error','缺少数据（请填写所有字段）');
			return;
		}
	}
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'usercp.vote.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['setting_1'];
	$xml->vote_save_logs = $_POST['setting_2'];
	$xml->credit_config = $_POST['setting_3'];
	
	$save = $xml->asXML($xmlPath);
	if($save) {
		message('success','已成功保存.');
	} else {
		message('error','保存时发生错误.');
	}
}


if(check_value($_POST['submit_changes'])) {
	saveChanges();
}

if(check_value($_POST['votesite_add_submit'])) {
	$add = $vote->addVoteSite($_POST['votesite_add_title'],$_POST['votesite_add_link'],$_POST['votesite_add_reward'],$_POST['votesite_add_time']);
	if($add) {
		message('success','推广已成功添加!');
	} else {
		message('error','添加推广时出错!');
	}
}

if(check_value($_REQUEST['delete'])) {
	$delete = $vote->deleteVoteSite($_REQUEST['delete']);
	if($delete) {
		message('success','推广网站已成功删除!.');
	} else {
		message('error','删除网站时出现错误!');
	}
}

loadModuleConfigs('usercp.vote');

$creditSystem = new CreditSystem();
?>
<div class="card">
    <div class="card-header">推广系统设置</div>
    <div class="card-body">
        <form action="index.php?module=Settings_Model&config=User_Vote" method="post">
            <table class="table table-striped table-bordered table-hover module_config_tables">
                <tr>
                    <th style="width: 60%"><strong>状态</strong><span class="ml-3 text-muted">启用/禁用 推广模块</span></th>
                    <td>
                        <?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>日志</strong><span class="ml-3 text-muted">如果启用，则每次推广的记录都将记录在数据库表中。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('setting_2',mconfig('vote_save_logs'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>货币类型</strong></th>
                    <td>
                        <?php echo $creditSystem->buildSelectInput("setting_3", mconfig('credit_config'), "form-control"); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php $votesiteList = $vote->retrieveVoteSite();
if(is_array($votesiteList)) {?>

    <div class="card">
        <div class="card-header">管理推广网站</div>
        <div class="">
            <table class="table table-bordered text-center">
                <tr>
                    <th>标题</th>
                    <th>链接（完整的网址，包括http）</th>
                    <th>奖励 [货币数量]</th>
                    <th>限制 [单位:小时]</th>
                    <th></th>
                </tr>
                <?foreach($votesiteList as $thisVoteSite) {?>
                    <tr>
                        <td><?=$thisVoteSite['votesite_title']?></td>
                        <td><?=$thisVoteSite['votesite_link']?></td>
                        <td><?=$thisVoteSite['votesite_reward']?></td>
                        <td><?=$thisVoteSite['votesite_time']?></td>
                        <td>
                            <a href="index.php?module=Settings_Model&config=User_Vote&delete=<?=$thisVoteSite['votesite_id']?>" class="btn btn-danger col-md-12"><i class="ion-trash-b"></i></a>
                        </td>
                    </tr>
                <?}?>
            </table>
        </div>
    </div>
<?} ?>

<div class="card">
    <div class="card-header">添加推广网站</div>
    <div class="">
        <table class="table table-bordered table-hover text-center">
            <tr>
                <th>标题</th>
                <th>推广链接 [完整的网址，包括http]</th>
                <th>奖励 [货币数量]</th>
                <th>限制 [单位:小时]</th>
                <th></th>
            </tr>
            <form action="index.php?module=Settings_Model&config=User_Vote" method="post">
                <tr>
                    <td><input name="votesite_add_title" class="form-control" type="text"/></td>
                    <td><input name="votesite_add_link" class="form-control" type="text"/></td>
                    <td><input name="votesite_add_reward" class="form-control" type="text"/></td>
                    <td><input name="votesite_add_time" class="form-control" type="text"/></td>
                    <td><input type="submit" name="votesite_add_submit" class="btn btn-success col-md-12" value="添加"/></td>
                </tr>
            </form>
        </table>
    </div>
</div>


