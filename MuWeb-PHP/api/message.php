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
/*防止恶意查询*/
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
try {
    if(!$_POST['uid'] == 1) throw new Exception("[1]禁止非法提交！");
    if(!$_POST['message']) throw new Exception("[2]禁止非法提交！");
    if(!check_value($_SESSION['group']))  throw new Exception("[3]禁止非法提交！");
    if(!$_POST['title']) $_POST['title'] = '游戏公告';
    $GameGroup = localServerGroupConfigs($_SESSION['group']);
    if(!$GameGroup['SERVER_JS_POST']) $GameGroup['SERVER_JS_POST'] = 55970;
    sleep(5); #延时5秒执行游戏公告
    @sendMessageGames($_POST['title'],$_POST['message'],$GameGroup['SERVER_IP'],$GameGroup['SERVER_JS_POST']);
    exit(json_encode('1'));
}catch (Exception $exception){
    exit($exception->getMessage());
}