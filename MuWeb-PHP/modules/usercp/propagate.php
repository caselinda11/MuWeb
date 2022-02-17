<?php
/**
 *  每日任务插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\propagate')) throw new Exception('该插件已禁用!');
    $Market = new \Plugin\propagate();

    $Market->loadModule('propagate');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
