<?php
/**
 * $Member
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\MemberBuy')) throw new Exception('该插件已禁用!');
    $MemberBuy = new \Plugin\MemberBuy();

    $MemberBuy->loadModule('MemberBuy');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}