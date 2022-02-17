<?php
/**
 * MemberReward
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\MemberReward;

# 插件目录
define('__PATH_MEMBER_REWARD_ROOT__', __PATH_INCLUDES_PLUGINS__.'MemberReward/');

# 插件链接
define('__MEMBER_REWARD_HOME__', __BASE_URL__.'MemberReward/');

#个人账号按钮
$extra_MyAccount_link[] = [
    '会员领奖' => 'usercp/MemberReward',
];

# 后台导航
$extra_admincp_sidebar[] = [
    '会员领奖','Plugin/MemberReward','fab fa-joget'
];

// 加载插件类函数
if(!@include_once(__PATH_MEMBER_REWARD_ROOT__ . 'classes/class.MemberReward.php')) throw new Exception('[会员领奖]无法加载类函数库。');
