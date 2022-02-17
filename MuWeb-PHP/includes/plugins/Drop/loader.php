<?php
/**
 * 溯源系统
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\drop;

# 插件目录
define('__PATH_DROP_ROOT__', __PATH_INCLUDES_PLUGINS__.'drop/');

# 插件链接
define('__PLUGIN_DROP_HOME__', __BASE_URL__.'drop/');

# 后台导航
$extra_admincp_sidebar[] = [
    '溯源系统', 'Plugin/drop', 'fab fa-joget'
];

#菜单导航链接钩子
$extra_menu_link[] = [
    '溯源系统', 'drop',
    'tag' => 'drop'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '溯源系统' => 'drop',
];

// 加载插件类函数
if(!@include_once(__PATH_DROP_ROOT__ . 'classes/class.drop.php')) throw new Exception('[溯源系统]无法加载类函数库。');