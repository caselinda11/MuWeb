<?php
/**
 * 在线改名插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\WingCasting')) throw new Exception('该插件已禁用!');
    $WingCasting = new \Plugin\WingCasting();
    $WingCasting->loadModule('WingCasting');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
