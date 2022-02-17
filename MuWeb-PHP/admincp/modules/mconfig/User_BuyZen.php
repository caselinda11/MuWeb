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
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'usercp.buyzen.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['setting_1'];
	$xml->max_zen = $_POST['setting_2'];
	$xml->exchange_ratio = $_POST['setting_3'];
	$xml->increment_rate = $_POST['setting_5'];
	$xml->credit_config = $_POST['setting_4'];
	
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

loadModuleConfigs('usercp.buyzen');

$creditSystem = new CreditSystem();
?>
<div class="card">
    <div class="card-header">购买金币(Zen)模块设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%">状态<span class="ml-3 text-muted">启用/禁用 购买金币模块</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th>最大Zen币(Mu币)<span class="ml-3 text-muted">一个角色可以拥有的最大Zen数量，（一般不要超过20亿）</span></th>
			<td>
				<input class="form-control" type="text" name="setting_2" value="<?=mconfig('max_zen')?>"/>
			</td>
		</tr>
		<tr>
			<th>汇率<span class="ml-3 text-muted">1点数可以兑换多少Zen币(Mu币)</span></th>
			<td>
				<input class="form-control" type="text" name="setting_3" value="<?=mconfig('exchange_ratio')?>"/>
			</td>
		</tr>
		<tr>
			<th>增长率<span class="ml-3 text-muted">值越大，下拉菜单中的选项就越少。</span></th>
			<td>
				<input class="form-control" type="text" name="setting_5" value="<?=mconfig('increment_rate')?>"/>
			</td>
		</tr>
		<tr>
			<th>货币类型<span class="ml-3 text-muted"></span></th>
			<td>
				<?=$creditSystem->buildSelectInput("setting_4", mconfig('credit_config'), "form-control"); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
		</tr>
	</table>
</form>
    </div></div>
