<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

function saveChanges() {
	global $_POST;
	foreach($_POST as $setting) {
		if(!check_value($setting)) {
			message('error','缺少数据（请填写所有字段）');
			return;
		}
	}
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'usercp.mypassword.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['active'];
	$xml->change_password_email_verification = $_POST['change_password_email_verification'];
	$xml->change_password_request_timeout = $_POST['change_password_request_timeout'];
	
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

loadModuleConfigs('usercp.mypassword');
?>
<div class="card">
    <div class="card-header">更改密码设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 更改密码模块</span></th>
			<td>
				<?=enableDisableCheckboxes('active',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>邮箱验证</strong><span class="ml-3 text-muted">如果启用，在用户单击通过邮箱发送的验证链接之前，不会更改帐户的密码。</span></th>
			<td>
				<?=enableDisableCheckboxes('change_password_email_verification',mconfig('change_password_email_verification'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>更改密码时间限制，单位：秒</strong><span class="ml-3 text-muted">如果启用了邮箱验证，请设置验证链接保持有效的时间。</span></th>
			<td>
				<input class="form-control" type="text" name="change_password_request_timeout" value="<?=mconfig('change_password_request_timeout')?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
		</tr>
	</table>
</form>
    </div></div>