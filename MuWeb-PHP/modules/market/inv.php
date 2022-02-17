<?php
/**
 * 交易市场模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\Market\Market')) throw new Exception('该插件已禁用!');
    $Market = new \Plugin\Market\Market();
    $Market->loadModule('inv');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}