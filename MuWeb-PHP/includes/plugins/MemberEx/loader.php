<?php
/**
 * 兑换会员
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\MemberEx;

# 插件目录
define('__PATH_MEMBER_EXCHANGE_ROOT__', __PATH_INCLUDES_PLUGINS__.'MemberEx/');

# 插件链接
define('__MEMBER_EXCHANGE_HOME__', __BASE_URL__.'MemberEx/');

# 后台导航
$extra_admincp_sidebar[] = [
    '兑换会员', 'Plugin/MemberExchange', 'fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '兑换会员' => 'usercp/MemberEx',
];

// 加载插件类函数
if(!@include_once(__PATH_MEMBER_EXCHANGE_ROOT__ . 'classes/class.MemberEx.php')) throw new Exception('无法加载[兑换会员]插件类库。');