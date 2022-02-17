<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'api');

include('../includes/Init.php');
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
$mconfig = loadConfigurations('register');
$geeTest = new GeeTestLib($mconfig['register_appID'],$mconfig['register_appKey']);

$ip = IP();
$username = isset($_SESSION['userid']) ? $_SESSION['userid'] : 'admin';
echo $geeTest->StartCaptchaServlet($ip,$username);