<?php
/**
 * 排名页面模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!check_value($_REQUEST['subpage'])) {
    redirect(1,$_REQUEST['page'].'/'.mconfig('show_default'));
}