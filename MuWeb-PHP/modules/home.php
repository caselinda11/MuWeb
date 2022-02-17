<?php
/**
 * 主页主要模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    ?>

    <!-- QQ群链接 -->
    <? if(config('QQUN') && config('qq_enable')) { ?>
        <iframe src="<?=config('QQUN')?>" style="display:none"></iframe>
    <?}?>
    <div class="row mb-1">
        <div class="col-md-7" style="padding-right: unset;">
            <!--轮播图-->
            <?if($config['website_template'] !== 'SkeletonLord'){?>
                <?if($config['website_template'] !== 'default'){?>
                    <?if($config['website_template'] !== 'simple'){?>
                        <div class="card mb-3">
                            <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                                    <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                                    <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
                                </ol>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="<?=__PATH_TEMPLATE_IMG__; ?>slide/1.jpg" class="d-block w-100"
                                             alt="...">
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5>轻体验 大改变</h5>
                                            <p>我们一直在努力,只为给您带来更好的娱乐!</p>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <img src="<?=__PATH_TEMPLATE_IMG__; ?>slide/2.jpg" class="d-block w-100"
                                             alt="...">
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5>精彩 一下就有</h5>
                                            <p>缘来游戏还可以这么玩</p>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <img src="<?=__PATH_TEMPLATE_IMG__; ?>slide/3.jpg" class="d-block w-100"
                                             alt="...">
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5>精彩视界 乐无止境</h5>
                                            <p>高清的视觉引擎给您带来穿越时空的感觉</p>
                                        </div>
                                    </div>
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    <?}?>
                <?}?>
            <?}?>

        </div>
        <div class="col-md-5" style="padding-left: unset;">
            <!--新闻-->
            <?
            try {
                // 新闻对象
                $News = new News();
                $cachedNews = loadCache('news.cache');
                if (!is_array($cachedNews)) throw new Exception("暂无新闻讯息。");
                ?>
                <div class="card mb-3" style="max-height: 241px;">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        新闻资讯
                        <a href="<?=__BASE_URL__?>news" class="more">&nbsp;＋&nbsp;</a>
                    </div>
                    <div class="overflow-auto">
                        <div class="list-group list-group-flush">
                            <table class="table table-striped table-bordered text-center table-hover" style="margin-bottom: 0;table-layout:fixed;">
                                <?php
                                // 新闻列表
                                foreach ($cachedNews as $newsArticle) {
                                    $News->setId($newsArticle['news_id']);
                                    if(!$newsArticle['status']) continue; //检测新闻是否使用中
                                    $newType = newsType();
                                    ?>
                                    <tr>
                                        <td width="90">
                                            <font color="<?=$newsArticle['type_color']?>">[<?= $newType[$newsArticle['news_type']]?>]</font>
                                        </td>
                                        <td>
                                            <div style="overflow:hidden;white-space:nowrap;text-overflow:ellipsis;">
                                            <a href="<?=__BASE_URL__.'new/content/'.$newsArticle['news_id']?>">
                                                <font color="<?= $newsArticle['title_color']?>">
                                                    <?=$newsArticle['news_title']?>
                                                </font>
                                            </a>
                                            </div>
                                        </td>
                                        <td width="100">
                                            <?=date("Y-m-d", strtotime($newsArticle['news_date']))?>
                                        </td>
                                    </tr>
                                    <?
                                } ?>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
            }catch (Exception $exception){
                message('warning',$exception->getMessage());
            }
            ?>
        </div>
    </div>

    <!-- 游戏设置 -->
    <div class="card mb-3">
        <div class="card-header">设置介绍</div>
        <div class="">
            <html>
                <body>
                    <?=getTextContent(__PATH_INCLUDES_CONFIGS__."settings_home.txt"); ?>
                </body>
            </html>
        </div>
    </div>

<?
}catch (Exception $exception){
    message('error',$exception->getMessage());
} ?>

