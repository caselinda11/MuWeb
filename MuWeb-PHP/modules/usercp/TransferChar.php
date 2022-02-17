<?php
/**
 * [CharTransfer]插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\TransferChar')) throw new Exception('该插件已禁用!');
    $CharTransfer = new \Plugin\TransferChar();
    $CharTransfer->loadModule('TransferChar');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
