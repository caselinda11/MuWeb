<?php
/**
 * WCoinTransfer
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\WCoinTransfer;

# 插件目录
define('__PATH_WCOIN_TRANSFER_ROOT__', __PATH_INCLUDES_PLUGINS__.'TransferWCoin/');

# 插件链接
define('__WCOIN_TRANSFER_HOME__', __BASE_URL__.'TransferWCoin/');

#个人账号按钮
$extra_MyAccount_link[] = [
    '货币转移' => 'usercp/TransferWCoin',
];

// 加载插件类函数
if(!@include_once(__PATH_WCOIN_TRANSFER_ROOT__ . 'classes/class.WCoinTransfer.php')) throw new Exception('无法加载[货币转移]插件类库。');