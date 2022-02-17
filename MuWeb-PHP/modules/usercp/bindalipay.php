<?php
/**
 * 角色加点页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">在线加点</li>
        </ol>
    </nav>
<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    debug($_GET);
    if($_GET['auth_code']){
        $t = new trading();
        debug($t->token($_GET['auth_code']));
    }
	?>

<?
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}