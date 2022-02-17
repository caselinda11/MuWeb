<?php
/**
 * 交易市场插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

#默认跳转至物品市场
if(!check_value($_REQUEST['subpage'])) {
    redirect(1,$_REQUEST['page'].'/Character');
}