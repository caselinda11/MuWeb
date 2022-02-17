<?php
/**
 * MentoringSystem
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\MentoringSystem')) throw new Exception('该插件已禁用!');
    $WCoinStatus = new \Plugin\MentoringSystem();

    $WCoinStatus->loadModule('MentoringSystem');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}