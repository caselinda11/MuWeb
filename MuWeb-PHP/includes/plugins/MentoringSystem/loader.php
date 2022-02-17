<?php
/**
 * 师徒系统
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\MentoringSystem;

# 插件目录
define('__PATH_MentoringSystem_ROOT__', __PATH_INCLUDES_PLUGINS__.'MentoringSystem/');

# 插件链接
define('__PLUGIN_MY_HOME__', __BASE_URL__.'MentoringSystem/');

# 后台导航
$extra_admincp_sidebar[] = [
    '师徒系统', 'Plugin/MentoringSystem', 'fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '师徒系统' => 'usercp/MentoringSystem',
];

// 加载插件类函数
if(!@include_once(__PATH_MentoringSystem_ROOT__ . 'classes/class.MentoringSystem.php')) throw new Exception('[MentoringSystem]无法加载类函数库。');