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
	
	// DONATION MODULE
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'donation.xml';
	$xml = simplexml_load_file($xmlPath);
	$xml->active = $_POST['setting_1'];
	$save1 = $xml->asXML($xmlPath);
	
	if($save1) {
		message('success','[Donation] 已成功保存.');
	} else {
		message('error','[Donation] 保存时发生错误.');
	}
}

if(check_value($_POST['submit_changes'])) {
	saveChanges();
}

loadModuleConfigs('donation');
?>
<div class="card">
    <div class="card-header">赞助设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 赞助模块.</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success col-md-2">保存</button></div></td>
		</tr>
	</table>	
</form>
    </div></div>