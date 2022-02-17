<?php
/**
 * FlagTransfer
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\FlagTransfer;

# 插件目录
define('__PATH_FLAG_TRANSFER_ROOT__', __PATH_INCLUDES_PLUGINS__.'TransferFlag/');

# 插件链接
define('__FLAG_TRANSFER_HOME__', __BASE_URL__.'TransferFlag/');

# 后台导航
$extra_admincp_sidebar[] = [
    '旗帜转移','Plugin/TransferFlag','fab fa-joget'
];
#个人账号按钮
$extra_MyAccount_link[] = [
    '旗帜转移' => 'usercp/TransferFlag',
];

// 加载插件类函数
if(!@include_once(__PATH_FLAG_TRANSFER_ROOT__ . 'classes/class.FlagTransfer.php')) throw new Exception('无法加载[旗帜转移]插件类库。');
