<?php
/**
 * 大师铸造
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\ExchangeMaster;

# 插件目录
define('__PATH_Exchange_Master_ROOT__', __PATH_INCLUDES_PLUGINS__.'ExchangeMaster/');

# 插件链接
define('__Exchange_Master_HOME__', __BASE_URL__.'ExchangeMaster/');

# 后台导航
$extra_admincp_sidebar[] = [
    '大师铸造','Plugin/ExchangeMaster','fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '大师铸造' => 'usercp/ExchangeMaster',
];


// 加载插件类函数
if(!@include_once(__PATH_Exchange_Master_ROOT__ . 'classes/class.ExchangeMaster.php')) throw new Exception('无法加载[大师铸造]插件类库。');