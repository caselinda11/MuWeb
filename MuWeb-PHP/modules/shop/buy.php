<?php
/**
 * 在线商城
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\Shop')) throw new Exception('该插件已禁用!');
    $shop = new \Plugin\Shop();
    $shop->loadModule('buy');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
