<?php
/**
 * 密码找回页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(isLoggedIn()) redirect();
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item active" aria-current="page">忘记密码</li>
        </ol>
    </nav>
<?php
	if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    $regConfig = loadConfigurations('register');
	if(check_value($_GET['ui']) && check_value($_GET['ue']) && check_value($_GET['key'])) {
		# 恢复过程
		try {
			$Account = new Account();
			$Account->setPasswordRecoveryVerificationProcess($_GET['id'],$_GET['ui'], $_GET['ue'], $_GET['key']);
		} catch (Exception $ex) {
			message('error', $ex->getMessage());
		}
	} else {
		# 表格提交
		if(check_value($_POST['submit'])) {
			try {
				$Account = new Account();
                if($regConfig['register_enable_phone']) {
				    $Account->setPhoneForGotPassword($_POST['serverGroup'], $_POST['username'], $_POST['phon_numb'], $_POST['phon_code']);
                }else {
                    $Account->setPasswordRecoveryProcess($_POST['serverGroup'], $_POST['username'], $_POST['current'], $_SERVER['REMOTE_ADDR']);
                }
			} catch (Exception $ex) {
				message('error', $ex->getMessage());
			}
		}

        if($regConfig['register_enable_phone']) {
            message('warning', '使用该功能您将收到一条短信，请检测您的手机信息。', '提示: ');
        }else{
            message('warning', '如果没有收到邮件请检查邮件是否被加入到垃圾邮件中。', '提示: ');
        }
?>
        <div class="card">
            <div class="card-header">忘记密码</div>
            <div class="card-body mt-3">
                    <form class="form-horizontal" action="" method="post">
                        <div class="form-group row justify-content-md-center">
                            <label for="serverGroup" class="col-sm-4 col-form-label text-right">所属大区</label>
                            <div class="col-sm-4">
                                <select id="serverGroup" name="serverGroup" id="serverGroup" class="form-control">
                                    <?foreach (getServerGroupList() as $group=> $item){?>
                                        <option value="<?=$group?>"><?=$item?></option>';
                                    <?}?>
                                </select>
                            </div>
                            <div class="col-sm-4"></div>
                        </div>
                        <div class="form-group row justify-content-md-center">
                            <label for="username" class="col-sm-4 col-form-label text-right">
                                游戏账号
                            </label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="username" id="username" required>
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
                                var group = $('#serverGroup').val();
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
                                $.post(baseUrl+'api/ext/register.php', {group:group,mobile:phone,models:'forgotpassword'}, function (data) {
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
                                    if(numbLength == thiscode) {
                                        invalid.addClass("is-valid").removeClass("is-invalid");
                                    }
                                });
                            });
                        </script>
                        <?}else{?>
                        <div class="form-group row justify-content-md-center">
                            <label for="current" class="col-sm-4 col-form-label text-right">
                                电子邮箱
                            </label>
                            <div class="col-sm-4">
                                <input type="email" class="form-control" name="current" id="current" required>
                            </div>
                            <div class="col-sm-4"></div>
                        </div>
                        <div class="form-group row justify-content-md-center">
                            <small class="text-danger">请填写您账号所属的邮箱,系统将发送一份新密码到您的邮箱!</small>
                        </div>
                            <?}?>
                        <div class="form-group row justify-content-md-center">
                            <input type="hidden" name="key" value="<?=Token::generateToken('forgotpassword')?>"/>
                            <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3">
                                确定找回
                            </button>
                        </div>
                        <div class="form-group row justify-content-md-center">
                            <a href="<?=__BASE_URL__?>login" class="btn btn-primary col-sm-3">
                                前往登陆
                            </a>
                        </div>
                    </form>
                <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                    请确保您的邮箱是真实有效的
                </footer>
            </div>
        </div>
<?php
	}
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}