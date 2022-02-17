<?php
/**
 * 加载罗兰峡谷API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'api');

include('../includes/Init.php');
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
$cs = cs_CalculateTimeLeft();
$timeLeft = (check_value($cs) ? $cs : 0);

echo json_encode(
	['TimeLeft' => $timeLeft]
);
