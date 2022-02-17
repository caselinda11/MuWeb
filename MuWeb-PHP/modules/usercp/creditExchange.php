<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try {
    if(!class_exists('Plugin\lottery')) throw new Exception('该插件已禁用!');
    $lottery = new \Plugin\lottery();
    $lottery->loadModule('creditExchange');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
