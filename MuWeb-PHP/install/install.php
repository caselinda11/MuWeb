<?php
/**
 * 安装程序
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'install');
if(!@include_once('loader.php')) die('无法加载奇迹网站系统安装程序。');
?>
<!doctype html>
<html lang="cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="<?php echo __PATH_PUBLIC_CSS__; ?>bootstrap.min.css" />
    <link rel="shortcut icon" href="<?php echo __PATH_TEMPLATE__; ?>favicon.ico"/>
    <title>程序安装</title>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron " style="margin-bottom:0">
                <div class="container">
                    <h1 class="display-4">程序安装</h1>
                    <p class="lead">现在开始安装 X-Team Framework 网站系统</p>
                </div>
            </div>
            <ul class="nav bg-dark">
                <li class="nav-item">
                    <a class="nav-link active" href="#">&nbsp;</a>
                </li>
            </ul>

            <div class="row mt-3 mb-3">
                <div class="col-md-9">
                    <?php
                    try {
                        global $install;
                        if(array_key_exists($_SESSION['install_cstep'], $install['step_list'])) {
                            $fileName = $install['step_list'][$_SESSION['install_cstep']][0];
                            if(file_exists($fileName)) {
                                if(!@include_once($fileName)) throw new Exception('步骤错误的文件。');
                            }
                        }
                    } catch (Exception $ex) {

                        echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';

                    }
                    ?>
                </div>

                <div class="col-md-3">
                    <?php stepListSidebar(); ?>
                </div>
            </div>

            <div class="jumbotron text-center" style="margin-bottom:0">
                <p> <a href="http://www.niudg.com/" target="_blank">&copy; X-TEAM 奇迹Mu网站系统管理 2013-<?php echo date("Y"); ?></a> </p>
            </div>
	    </div>

	</div>
</div> <!-- /container -->
    <script src="<?= __PATH_PUBLIC_JS__?>jquery.min.js"></script><!--2.2.4-->
    <script src="<?=__PATH_PUBLIC_JS__?>bootstrap.bundle.min.js"></script>
</body>
</html>