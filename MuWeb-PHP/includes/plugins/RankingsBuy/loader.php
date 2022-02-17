<?php
/**
 * 充值排名插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\Rankings\Shop;

# 插件目录
define('__PATH_PLUGIN_RANKING_BUY_ROOT__', __PATH_INCLUDES_PLUGINS__.'RankingsBuy/');

# 插件链接
define('__PLUGIN_RANKING_BUY_HOME__', __BASE_URL__.'RankingsBuy/');
# 后台导航
$extra_admincp_sidebar[] = [
    '充值排名', 'Plugin/Rankings_Buy', 'fab fa-joget'
];
# 前台排名导航
$rankingMenuLinks[] = ['充值排名', 'buy', 1];

// 加载插件类函数
if(!@include_once(__PATH_PLUGIN_RANKING_BUY_ROOT__ . 'classes/class.buy.php')) throw new Exception("无法加载[充值排名]插件类库。");