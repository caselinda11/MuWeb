<?php
/**
 * MonthlyCard
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\MonthlyCard;

# 插件目录
define('__PATH_MonthlyCard_ROOT__', __PATH_INCLUDES_PLUGINS__.'MonthlyCard/');

# 插件链接
define('__MonthlyCard_HOME__', __BASE_URL__.'MonthlyCard/');

# 后台导航
$extra_admincp_sidebar[] = [
    '月卡套餐','Plugin/MonthlyCard','fab fa-joget'
];

#个人账号按钮
$extra_MyAccount_link[] = [
    '月卡套餐' => 'usercp/MonthlyCard',
];

// 加载插件类函数
if(!@include_once(__PATH_MonthlyCard_ROOT__ . 'classes/class.MonthlyCard.php')) throw new Exception('[月卡套餐]无法加载类函数库。');