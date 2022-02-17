<?php
/**
 * 在线商城插件后台模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group float-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item">
                            <a href="<?=admincp_base()?>">官方主页</a>
                        </li>
                        <li class="breadcrumb-item active">插件系统</li>
                        <li class="breadcrumb-item active">在线夺宝</li>
                    </ol>
                </div>
                <h4 class="page-title">在线夺宝</h4>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="float-right mb-2">
                <a class="btn btn-warning btn-lg" href="<?=admincp_base('Plugin/lottery'); ?>">在线夺宝设置</a>
                <a class="btn btn-success btn-lg" href="<?=admincp_base('Plugin/lottery_shop'); ?>">稀有商城设置</a>
            </div>
        </div>
    </div>
<?php

try {
    $lottery = new \Plugin\lottery();
    $data = $lottery->getLotteryLog(false);
    if(!is_array($data)) throw new Exception("暂无夺宝记录!");
    ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            夺宝日志
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered table-hover text-center" id="datatable">
                <thead>
                    <tr>
                        <th>用户名</th>
                        <th>所属大区</th>
                        <th>中奖物品</th>
                        <th>中奖时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                <?if(is_array($data)){?>
                <?foreach ($data as $log){
                $status = ($log['status']) ? '<span class="text-danger">已领取</span>' : '<span class="text-success">未领取</span>';
                    ?>
                <tr>
                    <td><?=$log['username']?></td>
                    <td><?=getGroupNameForServerCode($log['servercode'])?></td>
                    <?
                    $name = $log['win_code'];
                    if(class_exists('Plugin\equipment')){
                        $itemClass = new \Plugin\equipment();
                        if(!preg_match('/[a-fA-F0-9]/',$log['win_code'])) $name = $log['win_code'];
                        $itemClass->convertItem($log['win_code']);
                        $name = $itemClass->itemOption['Name'];
                    }?>
                    <td><?=$name?></td>
                    <td><?=$log['date']?></td>
                    <td><?=$log['receive_date']?></td>
                    <td><?=$status?></td>
                </tr>
                    <?}?>
                    <?}?>
                </tbody>
            </table>
        </div>
    </div>

<?php

}catch (Exception $exception){
        message('error',$exception->getMessage());
} ?>



