<?php
/**
 * 更改电子邮件页面
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
            <li class="breadcrumb-item active" aria-current="page">更换邮箱</li>
        </ol>
    </nav>
<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	if(check_value($_POST['submit'])) {
		try {
			$Account = new Account();
			$Account->changeEmailAddress($_SESSION['group'],$_SESSION['userid'], $_POST['newEmail'], $_SERVER['REMOTE_ADDR']);
			if(mconfig('require_verification')) {
				message('success', '请检查您当前的邮箱地址再继续。');
			} else {
				message('success', '您账号的邮箱地址已成功更改。');
			}
		} catch (Exception $ex) {
            $_POST = [];
			message('error', $ex->getMessage());
		}
	}
	?>
	<div class="card">
        <div class="card-header">更换邮箱</div>
	<div class="card-body">
		<form class="form-horizontal" action="" method="post">
            <div class="form-group row justify-content-md-center">
                <label for="newEmail" class="col-sm-4 col-form-label text-right">新邮箱</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="newEmail" name="newEmail">
                    <input type="hidden" name="key" value="<?=Token::generateToken('changeEmail')?>"/>
                </div>
                <div class="col-sm-4"></div>
            </div>
            <div class="form-group row justify-content-md-center">
                <button type="submit" name="submit" value="submit" class="btn btn-success col-md-3" style="width:100%">更改</button>
            </div>
        </form>
    </div>
</div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}