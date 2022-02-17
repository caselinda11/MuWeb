<?php
/**
 * 注册页面
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
            <li class="breadcrumb-item active" aria-current="page">注册账号</li>
        </ol>
    </nav>
<?php
    if(!mconfig('active')) throw new Exception('目前尚未启用注册功能，请稍后再试。');
    # 注册流程
    if(check_value($_POST['submit'])) {
        try {
            $Account = new Account();
            if(!isset($_POST['sno__numb'])) $_POST['sno__numb'] = '111111111111111111';
            $Account->setRegisterAccount($_POST['serverGroup'],$_POST['userName'],$_POST['passWord'],$_POST['repassWord'],$_POST['email'],$config);
        } catch (Exception $ex) {
            message('error', $ex->getMessage());
        }
    }

?>
<div class="card">
    <div class="card-header">注册账号</div>
    <div class="card-body">
        <form class="form-horizontal mt-3" action="" method="post">
            <div class="">
            <div class="form-group row justify-content-md-center">
                <label for="serverGroup" class="col-md-4 col-form-label text-right">游戏大区</label>
                <div class="col-md-5">
                    <select class="custom-select form-control" name="serverGroup" id="serverGroup">
                        <? foreach (getServerGroupList() as $group=> $item){ ?>
                        <option value="<?=$group;?>"><?=$item;?></option>
                        <?}?>
                    </select>
                </div>
            <small class="col-sm-3 text-left form-inline text-muted"></small>
            </div>
                <div class="form-group row justify-content-md-center">
                    <label for="userName" class="col-md-4 col-form-label text-right">游戏账号</label>
                    <div class="col-md-5">
                        <input class="form-control" type="text" name="userName" id="userName" maxlength="<?=config('username_max_len')?>" autoComplete="off" required>
                    </div>
                    <br>
                    <small class="col-sm-3 text-left form-inline text-muted"></small>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="passWord" class="col-md-4 col-form-label text-right"></label>
                    <div class="col-md-5">
                        <button type="button" class="btn btn-outline-secondary col-md-12" onclick="checkUsername()">检测账号是否存在</button>
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted"></small>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="passWord" class="col-md-4 col-form-label text-right">游戏密码</label>
                    <div class="col-md-5">
                        <input class="form-control" type="password" name="passWord" id="passWord" maxlength="<?=config('password_max_len')?>" required>
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted"></small>
                </div>

                <div class="form-group row justify-content-md-center">
                    <label for="passWord" class="col-md-4 col-form-label text-right">确认密码</label>
                    <div class="col-md-5">
                        <input class="form-control" type="password" name="repassWord" id="repassWord" placeholder="" required>
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted">请再输入一次</small>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="email" class="col-md-4 col-form-label text-right">电子邮箱</label>
                    <div class="col-md-5">
                        <input type="email" class="form-control" name="email" id="email" required placeholder="10000@qq.com">
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted"><?if(mconfig('register_enable_qq_email')){?>必须是真实QQ邮箱<?}?></small>
                </div>

                <?if(mconfig('register_enable_sno__numb')){?>
                    <div class="form-group row justify-content-md-center">
                        <label for="sno__numb" class="col-md-4 col-form-label text-right">身份证号</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" minlength="<?=mconfig('register_sno__numb_length')?>" placeholder="由<?=mconfig('register_sno__numb_length')?>位数字与字母X组成" maxlength="<?=mconfig('register_sno__numb_length')?>" name="sno__numb" id="sno__numb">
                        </div>
                        <small class="col-sm-3 text-left form-inline text-muted"></small>
                    </div>
                <?}?>

                <?$inviteConfig = loadConfigurations('usercp.myaccount');?>
                <?if($inviteConfig['invite']){?>
                    <div class="form-group row justify-content-md-center">
                        <label for="invite" class="col-md-4 col-form-label text-right">推荐人ID</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="invite" id="invite" value="<?=$_GET['inviteID']?>" placeholder="没有可留空">
                        </div>
                        <small class="col-sm-3 text-left form-inline text-muted"></small>
                    </div>
                <?}?>
                <?if(mconfig('register_enable_phone')){?>
                <div class="form-group row justify-content-md-center">
                    <label for="phon_numb" class="col-sm-4 col-form-label text-right">手机号码</label>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="phon_numb" id="phon_numb" maxlength="11" required>
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted">用于接收验证码</small>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="phon_code" class="col-sm-4 col-form-label text-right">短信验证</label>
                    <div class="col-md-5">
                        <div class="row" style="padding-right: 15px;padding-left: 15px;">
                            <input type="text" class="form-control col-sm-6 is-invalid" name="phon_code" id="phon_code" maxlength="4" required>
                            <input type="button" id="click" value="获取验证码" class="btn btn-default col-sm-6">
                        </div>
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted "></small>
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
                        $.post( baseUrl+'api/ext/register.php', {group:group,mobile:phone,models:'register'}, function (data) {
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
                        var myreg = /^[1][2,3,4,5,6,7,8,9][0-9]{9}$/;
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
                <?}?>
                <?if(mconfig('register_enable_captcha')) {?>
                    <script src="<?=__PATH_PUBLIC__?>js/gt.js" ></script>
                    <div class="form-group row justify-content-md-center">
                        <label for="invite" class="col-md-4 col-form-label text-right">人机验证</label>
                        <div class="col-md-5">
                            <div id="embed-captcha"></div>
                            <button id="captcha-wait" class="btn btn-outline-secondary col-md-12" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </button>
                            <script>
                                var handlerEmbed = function (captchaObj) {
                                    $("#embed-submit").click(function (e) {
                                        var validate = captchaObj.getValidate();
                                        if (!validate) return commonUtil.message('请先完成人机验证。','danger','body');
                                    });
                                    captchaObj.appendTo("#embed-captcha");
                                    captchaObj.onReady(function () {
                                        $("#captcha-wait").hide();
                                    });
                                };
                                $.ajax({
                                    url: baseUrl +"api/captcha.php?t=" + <?=time()?>, // 加随机数防止缓存
                                    type: "get",
                                    dataType: "json",
                                    success: function (data) {
                                        initGeetest({
                                            gt: data.gt,
                                            challenge: data.challenge,
                                            new_captcha: data.new_captcha,
                                            product: "popup", // 产品形式，包括：float(浮动式)，embed(嵌入式)，popup(弹出式)。注意只对PC版验证码有效
                                            offline: !data.success, // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                                            width:"100%"
                                        }, handlerEmbed);
                                    }
                                });
                            </script>
                        </div>
                        <small class="col-sm-3 text-left form-inline text-muted"></small>
                    </div>
                <?}?>

                <div class="form-group row justify-content-md-center">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="customSwitch1" checked>
                        <label class="custom-control-label" for="customSwitch1">
							注册即表示您同意我们的 <a href="<?=__BASE_URL__?>tos" target="_blank" style="color:#03a9f4">服务条款</a>
                        </label>
                    </div>
                </div>

                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('register')?>"/>
                    <button type="submit" name="submit" value="submit" id="embed-submit" class="btn btn-success col-sm-4 submit" >创建账号</button>
                </div>
                <div class="form-group row justify-content-md-center">
                    <a href="<?=__BASE_URL__;?>login" class="btn btn-primary col-sm-4">
                        前往登陆
                    </a>
                </div>
            </div>

        </form>
    </div>
</div>
<?}catch(Exception $ex) {
    message('error', $ex->getMessage());
}
