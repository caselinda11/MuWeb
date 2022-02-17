<?php
/**
 * 底部
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<div class="col-sm-12 col-lg-9">
    <ul class="list-unstyled mt-3">
        <li class="media">
            <img src="<?=__PATH_TEMPLATE_IMG__?>img_logo01.png" class="align-self-center ml-3 mr-4" alt="">
            <div class="media-body">
                <h5 class="mt-2 mb-1">健康游戏公告</h5>
                <div class="text-muted">抵制不良游戏 拒绝盗版游戏 注意自我保护 谨防受骗上当 适度游戏益脑 沉迷游戏伤身 合理安排时间 享受健康生活</div>
                <div class="text-muted">本游戏适合18周岁以上的成年人用户，未满18周岁未成年用户请在家长的监督下进行游戏</div>
            </div>
        </li>
    </ul>
</div>
<div class="col-lg-3 text-right d-none d-lg-block visible-lg-block">
    <div class="row justify-content-md-center">
        <div style="color: #FF0000;font-weight: 400;font-size: 50px;margin-right: 20px"><span id="tLocalTime"></span><span style="display:none" id="tLocalDate"></span></div>
        <div style="color: #FF0000;font-weight: 400;font-size: 50px;"><span id="tServerTime"></span><span style="display:none" id="tServerDate"></span></div>
    </div>
</div>
<div class="col-md-12">
    <div class="text-center mt-2 mb-2 " style="border-top: 1px solid #d3d3d3;">
        Copyright © <?=config('server_name')?><?=$handler->powered()?> <?=date("Y")?><br>
        <p >Webzen Inc. Global Digital Entertainment Leader COPYRIGHT&copy; Webzen Inc. ALL RIGHTS RESERVED.</p>
    </div>
</div>



