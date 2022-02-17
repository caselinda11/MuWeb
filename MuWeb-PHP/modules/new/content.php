<?php
/**
 * 新闻内容展示页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item" aria-current="page"><a href="<?=__BASE_URL__?>news">新闻资讯</a></li>
            <li class="breadcrumb-item active" aria-current="page">新闻内容</li>
        </ol>
    </nav>
    <div class="card">
    <div class="card-header">新闻资讯</div>
<?php
try {
    // 新闻对象
    $News = new News();
    $requestedNewsId = $_GET['request'];
    if(!check_value($requestedNewsId)) throw new Exception('请提交有效的新闻ID！');
    if(!$News->newsIdExists($requestedNewsId)) throw new Exception('该新闻不存在或未知错误，请稍后再试！');
    if(!Validator::Number($requestedNewsId)) throw new Exception('操作失败，请稍后再试！');
    #加载新闻缓存
    $cachedNews = loadCache('news.cache');
    if(!is_array($cachedNews)) throw new Exception('暂无新闻讯息。');
    #传新闻ID
    $News->setId($requestedNewsId);
    // 新闻列表
    foreach($cachedNews as $newsContent) {
        if($_GET['request'] == $newsContent['news_id']){?>
            <div class="">
                <table class="table table-bordered" style="margin-bottom: 0;width: 100%;">
                    <thead>
                    <tr>
                        <th class="text-center"><h4><?=$newsContent['news_title'];?></h4></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3"><?=$News->LoadCachedNews();?></td>
                    </tr>
                    </tbody>
                </table>
                <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                    由 <strong><?=$newsContent['news_author']?></strong> 发布于<?=$newsContent['news_date']?>
                </footer>
            </div>
        <?}?>
    <?}?>
    </div>
<?
} catch(Exception $ex) {
    message('warning', $ex->getMessage());
}