<?php
/**
 * 充值排名插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\Rankings\buy')) throw new Exception('该插件已禁用!');
    $Plugin = new Plugin\Rankings\buy();
    $Plugin->loadModule('buy');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}