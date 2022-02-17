<?php
/**
 * changeIDCard
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

# 插件命名
namespace Plugin\changeIDCard;

# 插件目录
define('__PATH_CHANGE_ID_CARD_ROOT__', __PATH_INCLUDES_PLUGINS__.'changeIDCard/');

# 插件链接
define('__CHANGE_ID_CARD_HOME__', __BASE_URL__.'changeIDCard/');

#个人账号按钮
$extra_MyAccount_link[] = [
    '修改身份证' => 'usercp/changeIDCard',
];

// 加载插件类函数
if(!@include_once(__PATH_CHANGE_ID_CARD_ROOT__ . 'classes/class.changeIDCard.php')) throw new Exception('[身份证修改]无法加载插件类库。');