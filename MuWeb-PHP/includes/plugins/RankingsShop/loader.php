<?php
/**
 * 商城消费排名插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\Rankings\Shop;

# 插件目录
define('__PATH_PLUGIN_RANKING_SHOPS_ROOT__', __PATH_INCLUDES_PLUGINS__.'RankingsShop/');

# 插件链接
define('__PLUGIN_RANKING_SHOPS_HOME__', __BASE_URL__.'RankingsShop/');

# 前台排名导航
$rankingMenuLinks[] = ['消费排名', 'shop', 1];

// 加载插件类函数
if(!@include_once(__PATH_PLUGIN_RANKING_SHOPS_ROOT__ . 'classes/class.shops.php')) throw new Exception('无法加载消费排名类库。');