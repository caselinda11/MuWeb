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

#在这里指定哪个页面不显示右侧栏
$disabledSidebar = [
    'rankings',
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
    <meta name="generator" content="XTEAMCMS <?=__X_TEAM_VERSION__?>"/>
    <meta name="author" content="XTEAMCMS"/>
    <meta name="description" content="<?=config('website_meta_keywords')?>"/>
    <meta name="keywords" content="<?=config('website_meta_keywords')?>"/>
    <link rel="shortcut icon" href="<?=__PATH_TEMPLATE__?>favicon.ico"/>
    <!-- DataTables -->
    <link href="<?=__PATH_PUBLIC__; ?>plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />

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
    <!-- Required datatable js -->
    <script src="<?=__PATH_PUBLIC__; ?>plugins/datatables/js/jquery.dataTables.min.js"></script>
</head>
<body>

    <!--导航-->
    <header>
            <?include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/header.php')?>
    </header>
    <!--轮播-->
    <div class="container" style="padding-right: 0;padding-left: 0">
        <div style="background: url('<?=__PATH_TEMPLATE_IMG__?>bg_main01.png') 0 100% repeat-x">
            <div class="row" style="margin-top: 10rem!important;">
                <div class="col-sm-12 col-lg-8">
                    <div id="Captions" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators carousel-indicators-menu">
                            <li data-target="#Captions" data-slide-to="0" class="active"></li>
                            <li data-target="#Captions" data-slide-to="1"></li>
                            <li data-target="#Captions" data-slide-to="2"></li>
                        </ol>
                        <div class="carousel-inner">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="<?=__PATH_TEMPLATE_IMG__?>slide/1.png" class="d-block w-100" alt="">
                                    <div class="carousel-caption carousel-caption-menu d-none d-md-block">
                                        <h3>轻体验 大改变</h3>
                                        <p>我们一直在努力,只为给您带来更好的体验!</p>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <img src="<?=__PATH_TEMPLATE_IMG__?>slide/2.png" class="d-block w-100" alt="">
                                    <div class="carousel-caption carousel-caption-menu d-none d-md-block">
                                        <h3>精彩 一下就有</h3>
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
    <main class="container">
        <div class="row main">
            <?if(in_array($_REQUEST['page'], $disabledSidebar)) { ?>
                <div class="col-sm-12" style="margin-top: 15px;">
                    <?$handler->loadModule($_REQUEST['page'],$_REQUEST['subpage'])?>
                </div>
            <?}else{?>
                <div class="col-sm-9" style="margin-top: 15px;">
                    <?$handler->loadModule($_REQUEST['page'],$_REQUEST['subpage'])?>
                </div>
                <div class="col-sm-3" style="margin-top: 15px;">
                    <?include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/side.php')?>
                </div>
            <?}?>
            <hr>
        </div>
    </main>
    <footer class="container mb-5">
        <div class="row footer  align-self-center">
            <?include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/footer.php')?>
        </div>
    </footer>

    <script src="<?=__PATH_PUBLIC_JS__?>main.js"></script>
	<script src="<?=__PATH_PUBLIC_JS__?>alt.js"></script>
</body>
</html>
