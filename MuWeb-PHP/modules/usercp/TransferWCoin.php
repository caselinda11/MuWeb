<?php
/**
 * [WCoinTransfer]插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\WCoinTransfer')) throw new Exception('该插件已禁用!');
    $WCoinTransfer = new \Plugin\WCoinTransfer();
    $WCoinTransfer->loadModule('TransferWCoin');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
