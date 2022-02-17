<?php
/**
 * 角色铸造
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\WCoinStatus;

# 插件目录
define('__PATH_WCOIN_STATUS_ROOT__', __PATH_INCLUDES_PLUGINS__.'WCoinStatus/');

# 插件链接
define('__WCOIN_STATUS_HOME__', __BASE_URL__.'WCoinStatus/');

# 后台导航
$extra_admincp_sidebar[] = [
    '角色铸造','Plugin/WCoinStatus','fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '角色铸造' => 'usercp/WCoinStatus',
];

// 加载插件类函数
if(!@include_once(__PATH_WCOIN_STATUS_ROOT__ . 'classes/class.WCoinStatus.php')) throw new Exception('无法加载角色铸造插件类库。');