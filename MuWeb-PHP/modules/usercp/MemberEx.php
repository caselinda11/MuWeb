<?php
/**
 * $Member
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!class_exists('Plugin\MemberEx')) throw new Exception('该插件已禁用!');
    $MemberEx = new \Plugin\MemberEx();

    $MemberEx->loadModule('MemberEx');
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}