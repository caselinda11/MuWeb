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
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'usercp.myemail.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['setting_1'];
	$xml->require_verification = $_POST['setting_2'];
	
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

loadModuleConfigs('usercp.myemail');
?>
<div class="card">
    <div class="card-header">更改邮箱设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 更改邮箱模块.</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>邮箱验证</strong><span class="ml-3 text-muted">如果启用，则在用户单击发送到其当前邮箱的验证链接之前，不会更改帐户的邮箱。</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_2',mconfig('require_verification'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
		</tr>
	</table>
</form>
    </div></div>