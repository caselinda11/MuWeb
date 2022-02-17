<?php
/**
 * 溯源系统
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\drop')) throw new Exception('该插件已禁用!');
    $Market = new \Plugin\drop();
    $Market->loadModule('drop');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}