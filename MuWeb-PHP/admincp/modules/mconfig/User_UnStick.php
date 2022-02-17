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
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'usercp.unstick.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['active'];
	$xml->unstick_price_zen = $_POST['unstick_price_zen'];
	
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

loadModuleConfigs('usercp.unstick');
?>
<div class="card">
    <div class="card-header">角色自救设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 角色自救模块.</span></th>
			<td>
				<?=enableDisableCheckboxes('active',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>金币(Zen)</strong><span class="ml-3 text-muted">使用此功能的金币(Zen)费用，0为不需要。</span></th>
			<td>
				<input class="form-control" type="text" name="unstick_price_zen" value="<?=mconfig('unstick_price_zen')?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2" class=" text-center">
                <button type="submit" name="submit_changes" value="submit" class="btn btn-success col-md-3">保存</button>
            </td>
		</tr>
	</table>
</form>
    </div></div>