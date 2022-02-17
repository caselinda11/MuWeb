<?php
/**
 * 在线夺宝
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\lottery;

# 插件目录
define('__PATH_LOTTERY_ROOT__', __PATH_INCLUDES_PLUGINS__.'lottery/');

# 插件链接
define('__LOTTERY_HOME__', __BASE_URL__.'usercp/lotteryExtract/');

# 后台导航
$extra_admincp_sidebar[] = [
    '在线夺宝', 'Plugin/lottery', 'fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '在线夺宝' => 'usercp/lottery',
//    '购买点数' => 'usercp/creditExchange',
//    '仓库重置' => 'usercp/ResetWarehouse',
];

// 加载插件类函数
if(!@include_once(__PATH_LOTTERY_ROOT__ . 'classes/class.lottery.php')) throw new Exception('无法加载在线抽奖插件类库。');