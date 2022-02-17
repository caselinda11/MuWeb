<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access) die();
include('inc/template.functions.php');

#在这里指定哪个页面不显示右侧�?
$disabledSidebar = [
//    'rankings',
];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!--    移动优先-->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title><?=config('website_title')?></title>
    <meta name="generator" content="X TEAM CMS <?=__X_TEAM_VERSION__?>"/>
    <meta content="always" name="referrer" />
    <meta name="author" content="By mason X"/>
    <meta name="description" content="<?=config('website_meta_keywords')?>"/>
    <meta name="keywords" content="<?=config('website_meta_keywords')?>"/>
    <link rel="shortcut icon" href="<?=__PATH_TEMPLATE__?>favicon.ico"/>
    <link rel="stylesheet" href="<?=__PATH_PUBLIC_CSS__?>bootstrap.min.css" />
    <link rel="stylesheet" href="<?=__PATH_PUBLIC_CSS__?>font-awesome.min.css" />
    <link rel="stylesheet" href="<?=__PATH_TEMPLATE_CSS__?>style.css" />
    <link rel="stylesheet" href="<?=__PATH_PUBLIC_CSS__?>profiles.css" />
    <link rel="stylesheet" href="<?=__PATH_TEMPLATE_CSS__?>override.css" />
    <script>
        const baseUrl = '<?=__BASE_URL__?>';
    </script>
    <script src="<?=__PATH_PUBLIC_JS__?>jquery.min.js"></script><!--2.2.4-->
    <script src="<?=__PATH_PUBLIC_JS__?>bootstrap.bundle.js"></script>
</head>
<body>
    <!--顶部-->
        <?include(__PATH_TEMPLATE_ROOT__ .'inc/modules/header.php')?>
    <!--顶部END-->

    <!--轮播-->
    <div class="container">
        <div style="background: url('<?=__PATH_TEMPLATE_IMG__?>bg_main01.png') 0 100% repeat-x">
        <div class="row" style="margin-top: 8rem!important;">
            <div class="col-sm-12 col-lg-8">
                <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators carousel-indicators-menu">
                        <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                        <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                        <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="<?=__PATH_TEMPLATE_IMG__?>slide/1.png" class="d-block w-100" alt="">
                                <div class="carousel-caption carousel-caption-menu d-none d-md-block">
                                    <h3>轻体�?大改�?/h3>
                                    <p>我们一直在努力,只为给您带来更好的体�?</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="<?=__PATH_TEMPLATE_IMG__?>slide/2.png" class="d-block w-100" alt="">
                                <div class="carousel-caption carousel-caption-menu d-none d-md-block">
                                    <h3>精彩 一下就�?/h3>
                                    <p>缘来游戏还可以这么玩</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <img src="<?=__PATH_TEMPLATE_IMG__?>slide/3.png" class="d-block w-100" alt="">
                                <div class="carousel-caption carousel-caption-menu d-none d-md-block">
                                    <h3>精彩视界 乐无止境</h3>
                                    <p>高清的视觉引擎给您带来穿越时空的感觉</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-4 d-none d-lg-block visible-lg-block">
                <a href="<?=__BASE_URL__?>downloads">
                    <div class="btn_downLoad"></div>
                </a>
            </div>
        </div>
    </div>
    </div>
    <!--轮播END-->

    <!--主要部分-->
    <main class="container">
        <div class="row main">
            <?if(in_array($_REQUEST['page'], $disabledSidebar)) { ?>
                <div class="col-sm-12" style="margin-top: 15px;">
                    <?$handler->loadModule($_REQUEST['page'],$_REQUEST['subpage'])?>
                </div>
            <?}else{?>
                <div class="col-sm-8" style="margin-top: 15px;">
                    <?$handler->loadModule($_REQUEST['page'],$_REQUEST['subpage'])?>
                    <div class="card mt-3 mb-3">
                        <div class="row">
                            <img src="<?=__PATH_PUBLIC_IMG__?>g_1.jpg" alt="" class="col-sm-3">
                            <img src="<?=__PATH_PUBLIC_IMG__?>g_2.jpg" alt="" class="col-sm-3">
                            <img src="<?=__PATH_PUBLIC_IMG__?>g_3.jpg" alt="" class="col-sm-3">
                            <img src="<?=__PATH_PUBLIC_IMG__?>g_4.jpg" alt="" class="col-sm-3">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4" style="margin-top: 15px;">
                    <?include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/side.php')?>
                </div>
            <?}?>
        </div>
    </main>
    <!--主要部分END-->
    <!--底部-->
    <footer class="container">
        <div class="row footer  align-self-center">
            <?include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/footer.php')?>
        </div>
    </footer>
    <!--底部END-->

    <script src="<?=__PATH_PUBLIC_JS__?>main.js"></script>
    <script src="<?=__PATH_PUBLIC_JS__?>alt.js"></script>
</body>
</html>
