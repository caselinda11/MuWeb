<?php
/**
 * 账号登陆模块
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
        <li class="breadcrumb-item active" aria-current="page">账号登录</li>
    </ol>
</nav>
<?php
	if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    $regConfig = loadConfigurations('register');
	// 登录表单请求
	if(check_value($_POST['submit'])) {
		try {
            $userLogin = new login();
            $userLogin->validateLogin($_POST['group'], $_POST['user'], $_POST['pwd'], $_POST['levelPassword']);
		} catch (Exception $ex) {
			message('error', $ex->getMessage());
		}
	}

?>


<div class="card">
    <div class="card-header">账号登录</div>
    <div class="card-body">
        <form class="form-horizontal mt-3" action="" method="post">
            <div class="form-group row justify-content-md-center">
                <label for="group" class="col-sm-4 col-form-label text-right">游戏大区</label>
                <div class="col-sm-5">
                    <select class="custom-select form-control" name="group" id="group">
                        <? foreach (getServerGroupList() as $group=> $item){ ?>
                        <option value="<?=$group;?>"><?=$item;?></option>
                        <?}?>
                    </select>
                </div>
                <div class="col-sm-3"></div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="user" class="col-sm-4 col-form-label text-right">
                    游戏账号
                </label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="user" name="user" required>
                </div>
                <div class="col-sm-3"></div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="pwd" class="col-sm-4 col-form-label text-right">
                    游戏密码
                </label>
                <div class="col-sm-5">
                    <input type="password" class="form-control" id="pwd" name="pwd" required autocomplete="off">
                </div>
                <div class="col-sm-3"></div>
            </div>
            <?if($regConfig['register_enable_sno__numb']){?>
            <div class="form-group row justify-content-md-center">
                <label for="levelPassword" class="col-sm-4 col-form-label text-right">
                    二级密码
                </label>
                <div class="col-sm-5">
                    <input type="password" class="form-control" id="levelPassword" name="levelPassword" minlength="7" maxlength="7"  required>
                </div>
                <div class="col-sm-3">(*身份证后7位)</div>
            </div>
            <?}?>
                <script src="<?=__PATH_PUBLIC__?>js/gt.js" ></script>

            <div class="form-group row justify-content-md-center">
                <label class="col-sm-4 col-form-label text-right">人机验证</label>
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
                            // 将验证码加到id为captcha的元素里，同时会有三个input的值：geetest_challenge, geetest_validate, geetest_seccode
                            captchaObj.appendTo("#embed-captcha");
                            captchaObj.onReady(function () {
                                $("#captcha-wait").hide();
                            });
                        };
                        $.ajax({
                            // 获取id，challenge，success（是否启用failback）
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
                <p id="notice" class="col-form-label d-none">请先完成人机验证</p>
            </div>

            <div class="form-group row justify-content-md-center">
                <input type="hidden" name="key" value="<?=Token::generateToken('login')?>"/>
                <button type="submit" id="embed-submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                        登录
                </button>
				<!--
               <button type="button" id="embed-submit" name="submit" value="submit" class="btn btn-success col-sm-4" onclick="testAlipay();">
                        支付宝测试
                </button>
				
				<button type="button" id="embed-submit" name="submit" value="submit" class="btn btn-success col-sm-4" onclick="testDrop();">
				         测试drop
				 </button>
				-->
            </div>
            <div class="form-group row justify-content-md-center">
                <a href="<?=__BASE_URL__;?>register" class="btn btn-primary col-sm-4">
                    前往注册
                </a>
            </div>
            <div class="form-group row justify-content-md-center">
                <a href="<?=__BASE_URL__;?>forgotpassword" class="btn btn-primary col-sm-4">
                    密码找回
                </a>
            </div>
        </form>
    </div>
</div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}

