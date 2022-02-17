<?php
/**
 * 邮箱设置
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
	$xmlPath = __PATH_INCLUDES_CONFIGS__.'email.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['setting_1'];
	$xml->send_from = $_POST['setting_2'];
	$xml->send_name = $_POST['setting_3'];
	$xml->smtp_active = $_POST['setting_4'];
	$xml->smtp_host = $_POST['setting_5'];
	$xml->smtp_port = $_POST['setting_6'];
	$xml->smtp_user = $_POST['setting_7'];
	$xml->smtp_pass = $_POST['setting_8'];
	
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

// 加载 SMTP 配置
$emailConfigs = gconfig('email',true);
?>
<div class="card">
    <div class="card-header">邮箱设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%"><strong>邮箱系统</strong><span class="ml-3 text-muted">启用/禁用 邮箱设置</span class="ml-3 text-muted"></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',$emailConfigs['active'],'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>管理员邮箱</strong><span class="ml-3 text-muted">设置您的邮箱地址</span class="ml-3 text-muted"></th>
			<td>
				<input type="text" name="setting_2" value="<?php echo $emailConfigs['send_from']; ?>"/>
			</td>
		</tr>
		<tr>
			<th><strong>邮箱管理员名</strong><span class="ml-3 text-muted"></span class="ml-3 text-muted"></th>
			<td>
				<input type="text" name="setting_3" value="<?php echo $emailConfigs['send_name']; ?>"/>
			</td>
		</tr>
		<tr>
			<th><strong>SMTP</strong><span class="ml-3 text-muted">启用/禁用 SMTP 系统。</span class="ml-3 text-muted"></th>
			<td>
				<?=enableDisableCheckboxes('setting_4',$emailConfigs['smtp_active'],'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>SMTP 地址</strong><span class="ml-3 text-muted">例如:smtp.163.com</span class="ml-3 text-muted"></th>
			<td>
				<input type="text" name="setting_5" value="<?php echo $emailConfigs['smtp_host']; ?>"/>
			</td>
		</tr>
		<tr>
			<th><strong>SMTP 端口</strong><span class="ml-3 text-muted">默认使用587</span class="ml-3 text-muted"></th>
			<td>
				<input type="text" class="input-mini" name="setting_6" value="<?php echo $emailConfigs['smtp_port']; ?>"/>
			</td>
		</tr>
		<tr>
			<th><strong>SMTP 账号</strong><span class="ml-3 text-muted">你的账号</span class="ml-3 text-muted"></th>
			<td>
				<input type="text" name="setting_7" value="<?php echo $emailConfigs['smtp_user']; ?>"/>
			</td>
		</tr>
		<tr>
			<th><strong>SMTP 密码</strong><span class="ml-3 text-muted">你申请的SMTP密码</span class="ml-3 text-muted"></th>
			<td>
				<input type="text" name="setting_8" value="<?php echo $emailConfigs['smtp_pass']; ?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success col-md-2">保存</button></div></td>
		</tr>
	</table>
</form>
    </div></div>