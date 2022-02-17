<?php
/**
 * 我的插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\myPlugin;

# 插件目录
define('__PATH_PLUGIN_MY_ROOT__', __PATH_INCLUDES_PLUGINS__.'myPlugin/');

# 插件链接
define('__PLUGIN_MY_HOME__', __BASE_URL__.'myPlugin/');

# 后台导航
$extra_admincp_sidebar[] = [
    '我的插件', 'Plugin/myPlugin', 'fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '我的插件' => 'usercp/myPlugin',
];

// 加载插件类函数
if(!@include_once(__PATH_PLUGIN_MY_ROOT__ . 'classes/class.myPlugin.php')) throw new Exception('[我的插件]无法加载类函数库。');