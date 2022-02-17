<?php
/**
 * 交易市场插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\Market;

# 插件目录
    define('__PATH_PLUGIN_MARKET_ROOT__', __PATH_INCLUDES_PLUGINS__ . 'Market/');

# 插件链接
    define('__PLUGIN_MARKET_HOME__', __BASE_URL__ . 'market/');

# 后台导航
    $extra_admincp_sidebar[] = [
        '交易市场', 'Plugin/Market', 'fab fa-joget'
    ];

#菜单导航链接钩子
    $extra_menu_link[] = [
        '交易市场', 'market',
        'tag' => 'Market'
    ];

#个人账号按钮
    $extra_MyAccount_link[] = [
        "交易市场" => 'market',
        "我的仓库" => 'usercp/warehouse',
    ];

# 加载插件类函数
    if (!@include_once(__PATH_PLUGIN_MARKET_ROOT__ . 'classes/class.market.php')) throw new Exception('无法加载交易市场插件类库。');
    if (!@include_once(__PATH_PLUGIN_MARKET_ROOT__ . 'classes/class.trading.php')) throw new Exception('无法加载交易市场插件类库。');

