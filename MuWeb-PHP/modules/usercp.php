<?php
/**
 * 用户登陆跳转中间键
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {

if(!isLoggedIn()) redirect(1,'login');

//------------IGCN的服务端自动创建积分表
    $muonline  = Connection::Database('MuOnline',$_SESSION['group']);
    $Me_muonline  = Connection::Database('MuOnline',$_SESSION['group']);
switch (strtolower(config('server_files'))){
    case "igcn":
        $muonline->query_fetch_single("IF NOT EXISTS (SELECT * FROM [T_InGameShop_Point] WHERE [AccountID] = ?) BEGIN INSERT INTO T_InGameShop_Point (AccountID, WCoin, GoblinPoint) VALUES (?, 0, 0) END", [$_SESSION['username'],$_SESSION['username']]);
        $Me_muonline->query_fetch_single("IF NOT EXISTS (SELECT * FROM [T_VIPList] WHERE [AccountID] = ?) BEGIN INSERT INTO [T_VIPList] ([AccountID], [Type]) VALUES (?, 0) END", [$_SESSION['username'],$_SESSION['username']]);
        break;
    default:
        break;
}
//----------------
# 重定向到我的帐户
redirect(1, 'usercp/myaccount');

} catch (Exception $ex) {
    message('error', $ex->getMessage());
}