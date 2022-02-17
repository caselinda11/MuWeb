<?php
/**
 * 邮箱验证页面模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
        <li class="breadcrumb-item active" aria-current="page">邮箱验证</li>
    </ol>
</nav>
<div class="card">
	<div class="card-header">邮箱验证</div>
	<div class="card-body">
<?
	if(check_value($_GET['op'])) {
		
		/* 邮箱验证操作：
		|	1. 密码更改请求
		|	2. 注册
		|	3. 电邮变更要求
		*/
		switch($_GET['op']) {
			case 1:
				if(!check_value($_GET['uid'])) redirect();
				if(!check_value($_GET['ac'])) redirect();
				try {
					$Account = new Account();
					$Account->setChangePasswordVerificationProcess($_GET['group'],$_GET['uid'],$_GET['ac']);
				} catch (Exception $ex) {
					message('error', $ex->getMessage());
				}
				break;
			case 2:
				# 注册：电子邮件验证
				if(!check_value($_GET['user'])) redirect();
				if(!check_value($_GET['key'])) redirect();
				try {
					$Account = new Account();
					$Account->verifyRegistrationProcess($_GET['group'],$_GET['user'],$_GET['key']);
				} catch (Exception $ex) {
					message('error', $ex->getMessage());
				}
				break;
			default:
				if(!check_value($_GET['uid'])) redirect();
				if(!check_value($_GET['email'])) redirect();
				if(!check_value($_GET['key'])) redirect();
				try {
					$Account = new Account();
					$Account->changeEmailVerificationProcess($_GET['group'],$_GET['uid'],$_GET['email'],$_GET['key']);
					message('success', '您账号的邮箱地址已成功更改。');
				} catch (Exception $ex) {
					message('error', $ex->getMessage());
				}
		}
		
	} else {
		redirect();
	}
        ?>
	</div>
</div>

