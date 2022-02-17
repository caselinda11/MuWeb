<?php
/**
 * 财富排名插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\propagate;

# 插件目录
define('__PATH_PLUGIN_PROPAGATE_ROOT__', __PATH_INCLUDES_PLUGINS__.'propagate/');

# 插件链接
define('__PLUGIN_PROPAGATE_HOME__', __BASE_URL__.'propagate/');

# 后台导航
$extra_admincp_sidebar[] = [
    '系统任务', 'Plugin/propagate', 'fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '系统任务' => 'usercp/propagate',
];

// 加载插件类函数
if(!@include_once(__PATH_PLUGIN_PROPAGATE_ROOT__ . 'classes/class.propagate.php')) throw new Exception('无法加载系统任务插件类库。');