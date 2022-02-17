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
//    'rankings',
    'market',
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
    <!--顶部-->
    <header id="top">
        <?include(__PATH_TEMPLATE_ROOT__ .'inc/modules/header.php')?>
    </header>
    <!--顶部END-->
    <!--主要部分-->
    <main class="container">
        <div class="row">
            <?if(in_array($_REQUEST['page'], $disabledSidebar)) { ?>
                <div class="col-sm-12" style="margin-top: 15px;">
                    <?$handler->loadModule($_REQUEST['page'],$_REQUEST['subpage'])?>
                </div>
            <?}else{?>
                <div class="col-sm-8" style="margin-top: 15px;">
                    <?$handler->loadModule($_REQUEST['page'],$_REQUEST['subpage'])?>
                    <div class="card mt-3 mb-3 d-none d-lg-block visible-lg-block">
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

    <!--置顶按钮-->
    <div class="row justify-content-md-center"><a class="buttonTop"></a></div>
    <!--底部-->
    <footer >
        <div class="container">
            <div class="row footer">
                <?include(__PATH_TEMPLATE_ROOT__ . 'inc/modules/footer.php')?>
            </div>
        </div>
    </footer>
    <!--底部END-->
    <script>
        function ScrolClass() {
            if($(this).scrollTop() >= 50) {
                $('.menu').addClass('menu-fixed');
            } else {
                $('.menu').removeClass('menu-fixed');
            }
        }
        $(window).scroll(function() {
            ScrolClass();
        });
        $(document).ready(function() {
            ScrolClass();
        });
        $('.buttonTop').click(function(){
            $('body, html').animate({scrollTop:0},800);
        });
    </script>
    <script src="<?=__PATH_PUBLIC_JS__?>main.js"></script>
	<script src="<?=__PATH_PUBLIC_JS__?>alt.js"></script>
</body>
</html>
