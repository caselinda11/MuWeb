<?php
/**
 * CreateClass插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\CreateClass')) throw new Exception('该插件已禁用!');
    $CreateClass = new \Plugin\CreateClass();
    $CreateClass->loadModule('CreateClass');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
