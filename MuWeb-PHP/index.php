<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>

 * @version     2.0.0
 *
 **/
 
# 定义CMS访问
define('access', 'index');

#===============================
    #测试所用 时间计算开头
    $startTme= microtime(true); //开始时间，放在页面头部
#===============================
try {
    //通过error_reporting()函数设置，输出所有级别的错误报告
    error_reporting(E_ALL);
    #防御系统
    if(!@require('includes/anti_ddos/anti-ddos-lite.php')) throw new Exception('无法加载[Anti-DDOS系统]！');
    # 加载系统
	if(!@include('includes/Init.php')) throw new Exception('无法加载网站系统初始化文件！');

	#===============================
    #测试所用 时间计算结尾
    if($config['error_reporting']) {
        $mtimeTamp = sprintf("%.3f", microtime(true)); // 带毫秒的时间戳
        $timestamp = floor($mtimeTamp); // 时间戳
        $milliseconds = round(($mtimeTamp - $timestamp) * 1000); // 毫秒
        $datetime = date("H:i:s", $timestamp) . '.' . $milliseconds;
        $microTime = round((microtime(true)-$startTme)*1000,2).'ms';//访问时间戳(毫秒)
        print "<div style='position:fixed;background:rgba(0,0,0,0.6);right:0;color: #fff;padding: 10px;z-index:9;bottom:0;'>访问时间[<span style='color:#48f870'>$datetime</span>]，页面加载耗时:[<span style='color:#48f870'>$microTime</span>]！</div>";//打印加载时间
    }
    #===============================

} catch (Exception $ex) {
	ob_clean();
	$errorPage = file_get_contents('includes/error.html');
	echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
}