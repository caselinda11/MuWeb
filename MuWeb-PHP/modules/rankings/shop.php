<?php
/**
 * 消费插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\Rankings\Shop')) throw new Exception('该插件已禁用!');
    $Plugin = new Plugin\Rankings\Shop();
    $Plugin->loadModule('Shop');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}