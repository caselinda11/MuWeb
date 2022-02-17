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
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'castlesiege.xml';
	$xml = simplexml_load_file($xmlPath);

	$xml->active = $_POST['setting_5'];
	$xml->enable_banner = $_POST['setting_1'];
	$xml->cs_battle_day = $_POST['setting_2'];
	$xml->cs_battle_time = $_POST['setting_3'];
	$xml->cs_battle_duration = $_POST['setting_4'];
	
	$save = $xml->asXML($xmlPath);
	if($save) {
		message('success','已成功保存!');
	} else {
		message('error','保存时发生错误!');
	}
}

if(check_value($_POST['submit_changes'])) {
	saveChanges();
}

loadModuleConfigs('castlesiege');

message('','仅适用于每周一次（每7天），参照自己游戏设置的《罗兰攻城》时间表！','提示:');
?>
<div class="card">
    <div class="card-header">罗兰攻城设置</div>
    <div class="card-body">
    <form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 罗兰攻城模块.</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_5',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>横幅状态</strong><span class="ml-3 text-muted">启用/禁用 罗兰攻城倒计时横幅。</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',mconfig('enable_banner'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
            <th><strong>罗兰攻城日</strong><span class="ml-3 text-muted">规定哪一天是攻城，选项:1~7</span></th>
			<td>
                <label>
                    <select name="setting_2" style="width:180px">
                        <?for ($i=1;$i<=7;$i++){?>
                            <option value="<?=$i?>" <?if($i==mconfig('cs_battle_day')) echo 'selected'?>>星期<?=$i?></option>
                        <?}?>
                    </select>
                </label>
			</td>
		</tr>
		<tr>
			<th><strong>罗兰攻城时间</strong><span class="ml-3 text-muted">攻城开始的时间（24小时制！）格式:[时:分:秒]</span></th>
			<td>
				<input class="input-mini" type="text" name="setting_3" value="<?=mconfig('cs_battle_time')?>"/>
			</td>
		</tr>
		<tr>
			<th><strong>罗兰攻城持续时间</strong><span class="ml-3 text-muted">攻城时长（分钟）</span></th>
			<td>
				<input class="input-mini" type="text" name="setting_4" value="<?=mconfig('cs_battle_duration')?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2"<div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success col-md-4">保存</button></div></td>
		</tr>
	</table>
</form>
    </div>
</div>