<?php
/**
 * 在线改名
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\changeName;

# 插件目录
define('__PATH_CHANGE_NAME_ROOT__', __PATH_INCLUDES_PLUGINS__.'changeName/');

# 插件链接
define('__PLUGIN_CHANGE_NAME_HOME__', __BASE_URL__.'changeName/');

# 后台导航
$extra_admincp_sidebar[] = [
    '在线改名', 'Plugin/changeName', 'fab fa-joget'
];
#个人账号按钮
$extra_MyAccount_link[] = [
    '在线改名' => 'usercp/changeName',
];

// 加载插件类函数
if(!@include_once(__PATH_CHANGE_NAME_ROOT__ . 'classes/class.changeName.php')) throw new Exception('无法加载在线改名插件类库');