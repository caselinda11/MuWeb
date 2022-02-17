<?php
/**
 * 登出账号
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!isLoggedIn()) { redirect(); }

// 登出流程
logOutUser();

// 重定向到首页
redirect();