<?php
/**
 * 透视装备插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\equipment;

# 插件目录
define('__PATH_PLUGIN_ITEM_ROOT__', __PATH_INCLUDES_PLUGINS__.'items/');
# xml目录
define('__PATH_ITEMS_FILE__',__PATH_PLUGIN_ITEM_ROOT__.'config/');

# 后台导航
$extra_admincp_sidebar[] = [
    '透视物品', 'Plugin/Items', 'fab fa-joget'
];

# 加载插件类函数
if(!@include_once(__PATH_PLUGIN_ITEM_ROOT__ . 'classes/class.equipment.php')) throw new Exception('无法加载透视物品插件类库。');