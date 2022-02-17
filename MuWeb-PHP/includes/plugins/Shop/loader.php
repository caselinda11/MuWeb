<?php
/**
 * 商城插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\Shop;

# 插件目录
define('__PATH_PLUGIN_SHOP_ROOT__', __PATH_INCLUDES_PLUGINS__.'Shop/');
# 插件链接
define('__PLUGIN_SHOP_HOME__', __BASE_URL__.'shop/');

# 后台导航
$extra_admincp_sidebar[] = [
    '在线商城','Plugin/Shop','fab fa-joget'
];
#菜单导航链接钩子
$extra_menu_link[] = [
    '在线商城', 'shops',
    'tag' => 'shops'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '在线商城' => 'shops',
];

# 加载插件类函数
if(!@include_once(__PATH_PLUGIN_SHOP_ROOT__ . 'classes/class.shop.php')) throw new Exception('无法加载在线商城插件类库。');