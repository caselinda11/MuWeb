<?php
/**
 * 密码修改
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">修改密码</li>
        </ol>
    </nav>
<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	// common class
	$common = new common();
	if(mconfig('change_password_email_verification') && $common->hasActivePasswordChangeRequest($_SESSION['userid'])) {
		throw new Exception('您有一个有效的密码更改请求，请检查您的邮箱。');
	}
    $regConfig = loadConfigurations('register');
	if(check_value($_POST['submit'])) {
		try {
			$Account = new Account();
			# 是否启用邮箱验证
			if(mconfig('change_password_email_verification')) {
				# 需要验证
				$Account->setChangePasswordProcess_verifyEmail($_SESSION['group'],$_SESSION['userid'], $_SESSION['username'], $_POST['current'], $_POST['new'], $_POST['newconfirm'], $_SERVER['REMOTE_ADDR']);
			} else {
				# 不需要验证
				$Account->setChangePasswordProcess($_SESSION['group'],$_SESSION['userid'], $_SESSION['username'], $_POST['current'], $_POST['new'], $_POST['newconfirm']);
			}
		} catch (Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	?>
    <div class="card">
        <div class="card-header">修改密码</div>
        <div class="card-body">
            <div class="col-xs-8 col-xs-offset-1" style="margin-top:30px;">
                <form class="form-horizontal" action="" method="post">
                    <div class="form-group row justify-content-md-center">
                        <label for="current" class="col-sm-4 col-form-label text-right">旧密码</label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="current" name="current">
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <label for="new" class="col-sm-4 col-form-label text-right">新密码</label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="new" name="new">
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <label for="newconfirm" class="col-sm-4 col-form-label text-right">新密码</label>
                        <div class="col-sm-4">
                            <input type="password" class="form-control" id="newconfirm" name="newconfirm" placeholder="请再次输入新密码">
                        </div>
                        <div class="col-sm-4"></div>
                    </div>
                <?
                if($regConfig['register_enable_phone']){
                    ?>
                    <div class="form-group row justify-content-md-center">
                        <label for="phon_numb" class="col-sm-4 col-form-label text-right">手机号码</label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="phon_numb" id="phon_numb" maxlength="11" required>
                        </div>
                        <small class="col-sm-4 text-left form-inline text-muted">用于接收验证码</small>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <label for="phon_code" class="col-sm-4 col-form-label text-right">短信验证</label>
                        <div class="col-md-4">
                            <div class="row" style="padding-right: 15px;padding-left: 15px;">
                                <input type="text" class="form-control col-sm-6 is-invalid" name="phon_code" id="phon_code" maxlength="4" required>
                                <input type="button" id="click" value="获取验证码" class="btn btn-default col-sm-6">
                            </div>
                        </div>
                        <small class="col-sm-4 text-left form-inline text-muted "></small>
                    </div>
                    <script>
                        var thiscode;
                        $('#click').click(function () {
                            var phone = $('#phon_numb').val();//获取输入的电话号
                            if(phone === '') return modal_msg('手机号不能为空!');
                            if(!phoneFun(phone)) return modal_msg('请正确输入手机号!');
                            var group = <?=getGroupIDForServerCode($_SESSION['group'])?>;
                            var n = parseInt(Math.random() * 10000) + 1000;//生成随机验证码，位数自己制定
                            $(this).attr('disabled', 'disabled');//点击获取验证码后，禁用该按钮，开始倒计时
                            $(this).removeClass("btn-default").addClass("btn-secondary");
                            var time = 60;//倒计时时间，自定义
                            var $this = $(this);//备份，定时器是异步的，此this非彼this
                            var timer = setInterval(function () {
                                time--;//开始倒计时
                                if (time === 0) {//当倒计时为0秒时，关闭定时器，更改按钮显示文本并设置为可以点击
                                    clearInterval(timer);
                                    $this.val('获取验证码').removeAttr("disabled");

                                    $this.removeClass("btn-secondary").addClass("btn-default");
                                    return;
                                }
                                $this.val('还剩' + time + "s");//显示剩余秒数

                            }, 1000);//定时器一秒走一次，每次减一，就是倒计时了
                            $.post(baseUrl+'api/ext/register.php', {group:group,mobile:phone,models:'mypassword'}, function (data) {
                                if(!data.code){
                                    modal_msg(data);
                                    clearInterval(timer);
                                    $this.val('获取验证码').removeAttr("disabled");
                                    $this.removeClass("btn-secondary").addClass("btn-default");
                                }
                                //成功
                                thiscode = data.code;
                                modal_msg("短信发送成功，请检查您的手机短信。");
                            }, 'json');
                        });
                        //判断是否为手机号的正则表达式
                        function phoneFun(phones){
                            var myreg = /^[1][3,4,5,7,8,9][0-9]{9}$/;
                            if(!myreg.test(phones)) {
                                return false;
                            } else {
                                return true;
                            }
                        }
                        $(function(){
                            var check = $('#phon_recaptcha').val();
                            check.on('keyup',function(){
                                numbLength = $(this).val();
                            });
                            //获取焦点时触发事件
                            check.on('blur',function(){
                                var invalid = $('.is-invalid');
                                if(numbLength === thiscode) {
                                    invalid.addClass("is-valid").removeClass("is-invalid");
                                }
                            });
                        });
                    </script>
                <?}?>
                    <div class="form-group row justify-content-md-center">
                            <input type="hidden" name="key" value="<?=Token::generateToken('mypassword')?>"/>
                            <button type="submit" name="submit" value="submit" class="btn btn-primary col-sm-3" style="width:100%">确定</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}