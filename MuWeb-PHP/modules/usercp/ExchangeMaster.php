<?php
/**
 * 大师铸造插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\ExchangeMaster')) throw new Exception('该插件已禁用!');
    $ExchangeMaster = new \Plugin\ExchangeMaster();
    $ExchangeMaster->loadModule('ExchangeMaster');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
