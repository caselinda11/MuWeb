<?php
/**
 * $MemberReward
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\MemberReward')) throw new Exception('该插件已禁用!');
    $MemberReward = new \Plugin\MemberReward();

    $MemberReward->loadModule('MemberReward');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}