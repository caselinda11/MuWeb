<?php
/**
 * 地图掉落
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\mapDrop;

# 插件目录
define('__PATH_MAP_DROP_ROOT__', __PATH_INCLUDES_PLUGINS__.'mapDrop/');

# 插件链接
define('__MAP_DROP_HOME__', __BASE_URL__.'mapDrop');

#菜单导航链接钩子
$extra_menu_link[] = [
    '地图掉落', 'mapDrop',
	'tag' => 'drop'
];

// 加载插件类函数
if(!@include_once(__PATH_MAP_DROP_ROOT__ . 'classes/class.mapDrop.php')) throw new Exception('无法加载[地图掉落]插件类库。');