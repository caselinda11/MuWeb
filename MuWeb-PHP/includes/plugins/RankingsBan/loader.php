<?php
/**
 * 封停排名插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\Rankings\Ban;

# 插件目录
define('__PATH_PLUGIN_RANKING_BAN_ROOT__', __PATH_INCLUDES_PLUGINS__.'RankingsBan/');

# 插件链接
define('__PLUGIN_RANKING_BAN_HOME__', __BASE_URL__.'RankingsBan/');

# 前台导航
$rankingMenuLinks[] = ['封停排名', 'bans', 1];

// 加载插件类函数
if(!@include_once(__PATH_PLUGIN_RANKING_BAN_ROOT__ . 'classes/class.ban.php')) throw new Exception('无法加载封停插件类库。');