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
                        <li class="breadcrumb-item active">在线商城</li>
                    </ol>
                </div>
                <h4 class="page-title">购买日志</h4>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="float-right mb-2">
                <a class="btn btn-success btn-lg text-white" href="<?=admincp_base('Plugin/Shop'); ?>">商城设置</a>
            </div>
        </div>
    </div>
<?php
try{
    $shop = new \Plugin\Shop();
    $logData = $shop->getShopBuyLog();

    ?>
    <div class="card">
        <div class="card-header">在线商城购买日志</div>
        <div class="card-body">
            <?if (!is_array($logData)) message('warning',"暂无购买日志!");?>
            <?if(is_array($logData)){?>
            <table id="datatable" class="table table-striped table-bordered table-hover text-center">
                <thead>
                <tr>
                    <th>礼包ID</th>
                    <th>购买账号</th>
                    <th>购买角色</th>
                    <th>所属大区</th>
                    <th>购买物品</th>
                    <th>购买价格</th>
                    <th>价格类型</th>
                    <th>购买日期</th>
                </tr>
                </thead>
                <tbody>
                <?foreach($logData as $data){?>
                    <tr>
                        <td><?=$data['buy_id']?></td>
                        <td><?=$data['buy_username']?></td>
                        <td><?=$data['buy_character_name']?></td>
                        <td><?=getGroupNameForServerCode($data['servercode'])?></td>
                        <td><?=$data['buy_item_name']?></td>
                        <td><?=$data['buy_price']?></td>
                        <td><?=getPriceType($data['buy_price_type'])?></td>
                        <td><?=$data['buy_date']?></td>
                    </tr>
                <?}?>
                </tbody>
            </table>
            <?}?>
        </div>
    </div>
<?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
