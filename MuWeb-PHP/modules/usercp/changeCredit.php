<?php
/**
 * changeWCoin插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\changeCredit')) throw new Exception('该插件已禁用!');
    $changeIDCard = new \Plugin\changeCredit();
    $changeIDCard->loadModule('changeCredit');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
