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
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'login.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['setting_1'];
	$xml->enable_session_timeout = $_POST['setting_2'];
	$xml->enable_redirect = $_POST['enable_redirect'];
	$xml->session_timeout = $_POST['setting_3'];
	$xml->max_login_attempts = $_POST['setting_4'];
	$xml->failed_login_timeout = $_POST['setting_5'];
	
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

loadModuleConfigs('login');
?>
<div class="card">
    <div class="card-header">登陆页面设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用或禁用登录模块。</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>登录超时</strong><span class="ml-3 text-muted">如果启用则在一定的时间内账号不活跃将被注销登陆。</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_2',mconfig('enable_session_timeout'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>登录超时限制</strong><span class="ml-3 text-muted">如果启用了登陆超时，请定义一个时间（秒），在该时间后将自动注销不活动的已登录的账号。</span></th>
			<td>
				<input class="input-mini" type="text" name="setting_3" value="<?=mconfig('session_timeout')?>"/> 秒
			</td>
		</tr>
        <tr>
            <th><strong>登录跳转</strong><span class="ml-3 text-muted">如果启用则在登陆后跳转至上一次访问页面，禁用则跳转至个人面板。</span></th>
            <td>
                <?=enableDisableCheckboxes('enable_redirect',mconfig('enable_redirect'),'启用','禁用'); ?>
            </td>
        </tr>
		<tr>
			<th><strong>登录失败次数</strong><span class="ml-3 text-muted">定义最大登录失败次数。</span></th>
			<td>
				<input class="input-mini" type="text" name="setting_4" value="<?=mconfig('max_login_attempts')?>"/>
			</td>
		</tr>
		<tr>
			<th><strong>登陆限制时间</strong><span class="ml-3 text-muted">如果该账号在同一时间内登陆次数达到上线，则该IP将限制一定时间禁止登陆。单位：分钟</span></th>
			<td>
				<input class="input-mini" type="text" name="setting_5" value="<?=mconfig('failed_login_timeout')?>"/> 分
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
		</tr>
	</table>
</form>
    </div>
</div>