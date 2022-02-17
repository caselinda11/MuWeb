<?php
/**
 * 交易市场插件-卖模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
    ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>market">交易市场</a></li>
            <li class="breadcrumb-item active" aria-current="page">角色市场</li>
        </ol>
    </nav>
    <?php
try {
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('char');
    $market->MarketMenu();
    if(!$moduleConfig['active']) throw new Exception('未开放角色交易市场！');
    if($moduleConfig['active_login']) if(!isLoggedIn()) throw new Exception('该模块仅限会员可见，请先<a href="'.__BASE_URL__.'login">登陆账号</a>！');
    ?>
    <div class="spinner-border text-warning" id="loading" role="status" style="position: fixed;z-index: 9999;right: 2rem;bottom: 2rem;display:none">
        <span class="sr-only">Loading...</span>
    </div>
    <div class="card mb-3">
        <div class="card-header mb-2">角色市场</div>
        <?if(isLoggedIn()){?>
        <div class="col-md-12 mt-2">
            <div class="form-group row justify-content-md-end">
                <div class="" style="padding-left: 5px;padding-right: 5px;">
                    <div class="dropdown">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                            我的
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="javascript:" onclick="trading(0,'my-trading')">在售角色</a>
                            <a class="dropdown-item" href="javascript:" onclick="trading(0,'my-sell-log')">售出记录</a>
                            <a class="dropdown-item" href="javascript:" onclick="trading(0,'my-buy-log')">购买记录</a>
                        </div>
                    </div>
                </div>
                <div class="" style="padding-left: 5px;padding-right: 5px;">
                    <button class="btn btn-danger" type="button" onclick="trading(0,'sell')">寄售角色</button>
                </div>
            </div>
        </div>
        <?}?>
        <table id="trading-character" class="mt-2 table table-hover table-bordered table-striped text-center"></table>
        <footer class=" text-right mt-3 mb-3 mr-3">
            默认跟随玩家发布时间从新至旧排序
        </footer>
    </div>

    <script type="text/javascript">
        var rankingIndex = [1,6];
        var rankingTitle = ['所有职业','所有大区'];
    </script>
    <script type="text/javascript" src="<?=__PATH_PUBLIC_JS__?>trading.js"></script>
    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}