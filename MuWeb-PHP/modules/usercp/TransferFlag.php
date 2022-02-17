<?php
/**
 * [FlagTransfer]插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\FlagTransfer')) throw new Exception('该插件已禁用!');
    $FlagTransfer = new \Plugin\FlagTransfer();
    $FlagTransfer->loadModule('TransferFlag');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
