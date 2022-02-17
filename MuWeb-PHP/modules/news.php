<?php
/**
 * 新闻列表页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item active" aria-current="page">新闻资讯</li>
        </ol>
    </nav>
<?php
try {
	# 模块状态
	if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    # 新闻对象
    $News = new News();
    $cachedNews = loadCache('news.cache');

    if(!is_array($cachedNews)) throw new Exception('暂无新闻讯息。');
?>

<div class="card mb-3">
    <div class="card-header">新闻资讯</div>
    <div class="">
        <table class="table table-striped table-hover table-bordered text-center" style="margin-bottom: 0">

    <?foreach($cachedNews as $data) {
        if(!$data['status']) continue; //检测新闻是否使用中
		$newType = newsType();?>
    <tr>
        <td width="20%">
            <font color="<?=$data['type_color']?>">[<?=$newType[$data['news_type']]?>]</font>
        </td>
        <td width="60%">
            <a href="<?=__BASE_URL__.'new/content/'.$data['news_id']?>">
                <font color="<?=$data['title_color']?>"><?=$data['news_title']?></font>
            </a>
        </td>
        <td width="20%">
            <?=date("Y-m-d",strtotime($data['news_date']))?>
        </td>
    </tr>
    <?}?>

        </table>
    </div>
</div>
<?php
} catch(Exception $ex) {
	message('warning', $ex->getMessage());
}