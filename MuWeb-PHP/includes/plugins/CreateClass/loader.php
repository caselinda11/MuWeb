<?php
/**
 * 角色创建
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\CreateClass;

# 插件目录
define('__PATH_CREATE_CLASS_ROOT__', __PATH_INCLUDES_PLUGINS__.'CreateClass/');

# 插件链接
define('__CREATE_CLASS_HOME__', __BASE_URL__.'CreateClass/');

#个人账号按钮
$extra_MyAccount_link[] = [
    '角色创建' => 'usercp/CreateClass',
];
# 后台导航
$extra_admincp_sidebar[] = [
    '角色创建','Plugin/CreateClass','fab fa-joget'
];

// 加载插件类函数
if(!@include_once(__PATH_CREATE_CLASS_ROOT__ . 'classes/class.CreateClass.php')) throw new Exception('无法加载[角色创建]插件类库。');