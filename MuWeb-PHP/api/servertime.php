<?php
/**
 * 获取服务器时间API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'api');

include('../includes/Init.php');
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
echo json_encode(
	[
		'ServerTime' => date("Y-m-d H:i:s")
	]
);