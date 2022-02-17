<?php
/**
 * MonthlyCard
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\MonthlyCard')) throw new Exception('该插件已禁用!');
    $MonthlyCard = new \Plugin\MonthlyCard();

    $MonthlyCard->loadModule('MonthlyCard');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}