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

	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'register.xml';
	$xml = simplexml_load_file($xmlPath);

	$xml->active = $_POST['setting_1'];
	$xml->register_enable_qq_email = $_POST['register_enable_qq_email'];
	$xml->register_enable_sno__numb = $_POST['enable_sno__numb'];
    $xml->register_sno__numb_length = $_POST['sno__numb_length'];
	$xml->register_enable_captcha = $_POST['register_enable_captcha'];
	$xml->register_enable_phone = $_POST['register_enable_phone'];
	$xml->register_appID = $_POST['register_appID'];
	$xml->register_appKey = $_POST['register_appKey'];
	$xml->send_welcome_email = $_POST['setting_6'];
	$xml->verify_email = $_POST['setting_5'];
	$xml->verification_timelimit = $_POST['setting_7'];
	$xml->money = $_POST['money'];
	$xml->email_status = $_POST['email_status'];

	$save = $xml->asXML($xmlPath);
	if($save) {
		message('success','已成功保存.');
	} else {
		message('error','保存时发生错误.');
	}
}

if(check_value($_POST['submit'])) {
	saveChanges();
}

loadModuleConfigs('register');
?>
<div class="card">
    <div class="card-header">注册设置</div>
    <div class="card-body">
    <form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">开启或关闭注册板块</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
        <tr>
            <th><strong>是否使用QQ邮箱</strong><span class="ml-3 text-muted">如果启用则注册时填写的邮箱必须是QQ邮箱才可注册。</span></th>
            <td>
                <?=enableDisableCheckboxes('register_enable_qq_email',mconfig('register_enable_qq_email'),'启用','禁用'); ?>
            </td>
        </tr>
        <tr>
            <th><strong>身份证号</strong><span class="ml-3 text-muted">如果启用注册页面将会多出身份证号输入选项，</span><span class="text-danger">登陆页面将会启动二级密码选项。</span></th>
            <td>
                <?=enableDisableCheckboxes('enable_sno__numb',mconfig('register_enable_sno__numb'),'启用','禁用'); ?>
            </td>
        </tr>
        <tr>
            <th><strong>身份证号长度</strong><span class="ml-3 text-muted">身份证号启用才有效，<span class="text-danger">登陆二级密码取身份证号后7位</span>。最小<span class="text-danger"><b>1</b></span>位，最大<span class="text-danger"><b>18</b></span>位</span><span class="text-danger">（*IGC端最长13位）</span></th>
            <td>
                <input type="text" name="sno__numb_length" maxlength="18" value="<?=mconfig('register_sno__numb_length')?>"/>
            </td>
        </tr>
        <tr>
			<th><strong>验证码</strong><span class="ml-3 text-muted">禁用/启用，验证码采用极验验证码。</th>
			<td>
				<?=enableDisableCheckboxes('register_enable_captcha',mconfig('register_enable_captcha'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>验证码appID</strong><span class="ml-3 text-muted">此ID用于您的网站和极验之间的通信。</span></th>
			<td>
				<input type="text" name="register_appID" class="form-control" value="<?=mconfig('register_appID')?>" />
			</td>
		</tr>
		<tr>
			<th><strong>验证码appKey</strong><span class="ml-3 text-muted">此密钥用于您的网站和极验之间的通信。</span></th>
			<td>
				<input type="text" name="register_appKey" class="form-control" value="<?=mconfig('register_appKey')?>" />
			</td>
		</tr>
        <tr>
            <th><strong>重复邮箱</strong><span class="ml-3 text-muted">默认启用，每个邮箱将只能用一次。如果禁用，同一个邮箱可以无限制使用。</span></th>
            <td>
                <?=enableDisableCheckboxes('email_status',mconfig('email_status'),'启用','禁用'); ?>
            </td>
        </tr>
		<tr>
			<th><strong>邮箱验证</strong><span class="ml-3 text-muted">如果启用，则用户将收到带有验证链接的邮箱。 如果未验证邮箱，则不会创建该帐户。</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_5',mconfig('verify_email'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>欢迎邮箱</strong><span class="ml-3 text-muted">注册新账号后是否发送欢迎邮箱。</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_6',mconfig('send_welcome_email'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
			<th><strong>邮箱时效</strong><span class="ml-3 text-muted">如果 <strong>邮箱验证</strong> 已启用。设置用户验证帐户的时间。超过验证时间限制后，用户将重新注册！</span></th>
			<td>
				<input class="input-mini" type="text" name="setting_7" value="<?=mconfig('verification_timelimit')?>"/> 小时制
			</td>
		</tr>
        <tr>
            <th><strong>注册送钱(Mu币)</strong><span class="ml-3 text-muted">最大值不要超过20亿,不送钱设置为0即可!</th>
            <td>
                <input class="input-mini" type="text" name="money" value="<?=mconfig('money')?>"/> Mu币
            </td>
        </tr>
		<tr>
            <th><strong>手机注册</strong><span class="ml-3 text-muted">注册账号采用手机号注册!</th>
            <td>
                <?=enableDisableCheckboxes('register_enable_phone',mconfig('register_enable_phone'),'启用','禁用'); ?>
            </td>
        </tr>
		
		

		<tr>
			<td colspan="2">
                <div style="text-align:center">
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-md-2">
                        保存
                    </button>
                </div>
            </td>
		</tr>
	</table>
    </form>
    </div>
</div>
