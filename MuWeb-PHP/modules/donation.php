<?php
/**
 * 赞助页面模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item active" aria-current="page">赞助我们</li>
        </ol>
    </nav>
<?php
	if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	?>
<div class="card mb-3">
    <div class="card-header">赞助我们</div>
    <div class="">
        <html>
            <body>
                <?php echo getTextContent(__PATH_INCLUDES_CONFIGS__ . "settings_buys.txt"); ?>
            </body>
        </html>
    </div>
</div>

<?php
} catch(Exception $ex) {

	message('error', $ex->getMessage());

}