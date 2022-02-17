<?php
/**
 * 财富插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
	if(!class_exists('Plugin\Rankings\ban')) throw new Exception('该插件已禁用!');
	$ban = new Plugin\Rankings\ban();
    $ban->loadModule('ban');
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}