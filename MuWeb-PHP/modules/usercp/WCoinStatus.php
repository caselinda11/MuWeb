<?php
/**
 * 角色铸造
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\WCoinStatus')) throw new Exception('该插件已禁用!');
    $WCoinStatus = new \Plugin\WCoinStatus();

    $WCoinStatus->loadModule('WCoinStatus');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}