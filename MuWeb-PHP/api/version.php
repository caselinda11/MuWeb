<?php
/**
 * 加载版本API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'api');

include('../includes/Init.php');

$apacheVersion = (function_exists('apache_get_version') ? apache_get_version() : '未知');
$phpVersion = phpversion();
$webVersion = __X_TEAM_VERSION__;
//debug(key_encrypt('','E','heilong'));
echo json_encode(['apache' => $apacheVersion, 'php' => $phpVersion, 'Version' => $webVersion, 'key' => key_encrypt(KEY,'D','heilong')], JSON_PRETTY_PRINT);