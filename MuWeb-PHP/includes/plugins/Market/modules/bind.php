<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try {
    if(!class_exists('Plugin\Market\Market')) throw new Exception('该插件已禁用!');
    if($_GET['auth_code']){
        if(!isLoggedIn()) throw new Exception('[1]支付宝绑定失败，请先登录账号。');
        $t = new trading();
        $alipayID = $t->token($_GET['auth_code']);
        if(!$alipayID) throw new Exception('[1]支付宝绑定失败，请联系在线客服。');
        if(!$t->bindAliPay($alipayID)) throw new Exception('[2]支付宝绑定失败，请联系在线客服。');
        $temp = '<script type="text/javascript">';
        $temp .= '$(function() {';
        $temp .= 'modal_url(baseUrl + "market","恭喜您，支付宝绑定成功！");';
        $temp .= '});';
        $temp .= '</script>';
        echo $temp;
    }else{
        redirect(1,$_REQUEST['page'].'/character');
    }

} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
