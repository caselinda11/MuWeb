<?php
/**
 * 头部文件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<!--导航-->
<div class="menu">
    <div class="container">
        <div class="navbar navbar-expand-lg navbar-light">
            <a class="" href="<?=__BASE_URL__?>"><img src="<?=__PATH_TEMPLATE_IMG__?>logo.png" width="200" alt=""></a>
            <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars text-white" style="font-size: 2rem" aria-hidden="true"></i>
            </button>
            <div class="navbar-brand collapse navbar-collapse justify-content-center" id="navbarNav">
                <?=templateBuildNavbar()?>
            </div>
        </div>
    </div>
</div>

<div class="container d-none d-lg-block visible-lg-block" style="height: 500px">
    <div class="sparks">
        <div class="spark_1"></div>
        <div class="spark_2"></div>
        <div class="spark_3"></div>
        <div class="spark_4 spark-big"></div>
    </div>
</div>

<div class="container d-none d-lg-block visible-lg-block">
    <div class="row justify-content-md-center">
        <a href="<?= __BASE_URL__ ?>downloads" class="btn col-md-3 down-btn">
            <div class="ml-5">
                <h3>游戏下载</h3>
                <p>下载游戏即可体验</p>
            </div>
        </a>
        <a href="<?= __BASE_URL__ ?>login" class="btn col-md-3 login-btn">
            <div class="ml-5">
                <h3>账号登陆</h3>
                <p>登录网站开始旅程</p>
            </div>
        </a>
        <a href="<?= __BASE_URL__ ?>register" class="btn col-md-3 reg-btn">
            <div class="ml-5">
                <h3>账号注册</h3>
                <p>精彩从这里开始</p>
            </div>
        </a>
    </div>
</div>