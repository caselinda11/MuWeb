<?php
/**
 * 头部文件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <header class="d-none d-lg-block visible-lg-block">
        <div class="head container">
            <div class="row">
                <div class="col-3 col-md-6">
                    <h1><a class="w-logo"></a></h1>
                </div>
                <div class="col-9 col-md-6">
                    <div class="float-right link">
                        <?if(!isLoggedIn()){?>
                            <a href="<?=__BASE_URL__?>register">账号注册</a>
                                |
                            <a href="<?=__BASE_URL__?>login">账号登录</a>
                        <?}else{?>
                            <div class="dropdown">
                                <a class="dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    账号管理
                                </a>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?=templateBuildUsercp()?>
                                </div>
                                |
                                <a href="<?=__BASE_URL__?>logout">安全登出</a>
                            </div>
                        <?}?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!--导航-->
    <div class="container menu">
        <div class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="<?=__BASE_URL__?>"><img src="<?=__PATH_TEMPLATE_IMG__?>logo.png" width="150px" alt=""></a>
            <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars text-white" style="font-size: 2rem" aria-hidden="true"></i>
            </button>
            <div class="navbar-brand collapse navbar-collapse justify-content-end" id="navbarNav">
            <?=templateBuildNavbar()?>
            </div>
        </div>
    </div>
