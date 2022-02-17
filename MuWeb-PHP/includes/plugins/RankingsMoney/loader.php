<?php
/**
 * 财富排名插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\Rankings\Money;

# 插件目录
define('__PATH_PLUGIN_RANKING_MONEY_ROOT__', __PATH_INCLUDES_PLUGINS__.'RankingsMoney/');

# 插件链接
define('__PLUGIN_RANKING_MONEY_HOME__', __BASE_URL__.'RankingsMoney/');

# 前台导航
$rankingMenuLinks[] = ['财富排名', 'money', 1];

# 后台导航
$extra_admincp_sidebar[] = [
    '财富排名', 'Plugin/Rankings_Money', 'fab fa-joget'
];

// 加载插件类函数
if(!@include_once(__PATH_PLUGIN_RANKING_MONEY_ROOT__ . 'classes/class.money.php')) throw new Exception('无法加载财富插件类库。');