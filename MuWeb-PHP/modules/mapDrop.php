<?php
/**
 * 地图掉落
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\mapDrop')) throw new Exception('该插件已禁用!');
    $mapDrop = new \Plugin\mapDrop();
    $mapDrop->loadModule('mapDrop');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}