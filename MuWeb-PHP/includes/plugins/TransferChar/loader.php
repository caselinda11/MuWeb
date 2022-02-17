<?php
/**
 * CharTransfer
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\CharTransfer;

# 插件目录
define('__PATH_CHAR_TRANSFER_ROOT__', __PATH_INCLUDES_PLUGINS__.'TransferChar/');

# 插件链接
define('__CHAR_TRANSFER_HOME__', __BASE_URL__.'CharTransfer/');

# 后台导航
$extra_admincp_sidebar[] = [
    '角色转移','Plugin/TransferChar','fab fa-joget'
];
#个人账号按钮
$extra_MyAccount_link[] = [
    '角色转移' => 'usercp/TransferChar',
];

// 加载插件类函数
if(!@include_once(__PATH_CHAR_TRANSFER_ROOT__ . 'classes/class.TransferChar.php')) throw new Exception('无法加载[角色转移]插件类库。');