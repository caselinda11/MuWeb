<?php
/**
 * 财富插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
	if(!class_exists('Plugin\Rankings\money')) throw new Exception('该插件已禁用!');
	$MyPlugin = new Plugin\Rankings\money();
	$MyPlugin->loadModule('money');
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}