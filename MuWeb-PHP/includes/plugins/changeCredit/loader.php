<?php
/**
 * 货币转换
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\changeCredit;

# 插件目录
define('__PATH_CHANGE_CREDIT_ROOT__', __PATH_INCLUDES_PLUGINS__.'changeCredit/');

# 插件链接
define('__CHANGE_CREDIT_HOME__', __BASE_URL__.'changeCredit/');

# 后台导航
$extra_admincp_sidebar[] = [
    '货币转换', 'Plugin/changeCredit', 'fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '货币转换' => 'usercp/changeCredit',
];

// 加载插件类函数
if(!@include_once(__PATH_CHANGE_CREDIT_ROOT__ . 'classes/class.changeCredit.php')) throw new Exception('[积分转换]无法加载类函数库。');