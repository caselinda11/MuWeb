<?php
/**
 * [我的插件]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">我的插件</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $MyPlugin = new \Plugin\myPlugin();
    $tConfig = $MyPlugin->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">我的插件</div>
        <div class="card-body">
            主要内容
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">我的插件</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}