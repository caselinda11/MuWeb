<?php
/**
 * 会员升级
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\MemberBuy;

# 插件目录
define('__PATH_MEMBER_BUY_ROOT__', __PATH_INCLUDES_PLUGINS__.'MemberBuy/');

# 插件链接
define('__MEMBER_BUY_HOME__', __BASE_URL__.'MemberBuy/');

# 后台导航
$extra_admincp_sidebar[] = [
    '会员升级', 'Plugin/MemberBuy', 'fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '会员升级' => 'usercp/Member',
];

// 加载插件类函数
if(!@include_once(__PATH_MEMBER_BUY_ROOT__ . 'classes/class.MemberBuy.php')) throw new Exception('无法加载[会员升级]插件类库。');