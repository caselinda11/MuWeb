<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>/usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">仓库管理</li>
        </ol>
    </nav>
<?
try {
    if (!isLoggedIn()) redirect(1, 'login');
    $warehouse = new \Plugin\Market\warehouse($_SESSION['group']);
    $item = $warehouse->_getItemLength();

    ?>
    <div class="card mb-3">
        <div class="card-header">仓库列表</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center text-white" style="min-width: 360px">
                    <div style="background: url('<?=__PATH_PUBLIC_IMG__?>warehouse/warehouse.png') no-repeat;width: 300px;height:590px; margin: 0 auto;">
                        <div style="color:#FFA500;line-height: 90px;">默认仓库</div>
                        <div style="width: 224px;height: 420px;position: relative;top: 22px;left: 37px;">
                            <?if(!empty($item)){?>
                                <?for($i=0;$i<120;$i++){?>
                                    <?if (isset($item[$i]['url'])) {?>
                                        <div style="position: absolute;top:<?=($item[$i]['y'] * 28)?>px;left:<?=($item[$i]['x'] * 28)?>px;">
                                            <div style="position: absolute;background: url('<?= __PATH_PUBLIC_IMG__ ?>warehouse/VA.jpg') repeat;width:<?=($item[$i]['width'] * 28)?>px;height:<?=($item[$i]['height'] * 28)?>px;">
                                                <div class="data-info" data-info="<?=$item[$i]['code']?>" style="width:<?=($item[$i]['width'] * 28)?>px;height:<?=($item[$i]['height'] * 28)?>px;background: url('<?= $item[$i]['url']?>') center;background-size: cover;"></div>
                                            </div>
                                        </div>
                                    <?}else{?>
                                        <div id="item-null-<?=$i?>"></div>
                                    <?}?>
                                <?}?>
                            <?}?>
                        </div>
                    </div>
                </div>
                <?if($warehouse->useExtendWarehouse){?>
                <!--    扩展仓库-->
                <div class="col-md-6 text-center text-white" style="min-width: 360px">
                    <div style="background: url('<?=__PATH_PUBLIC_IMG__?>warehouse/warehouse.png') no-repeat;width: 300px;height:590px; margin: 0 auto;">
                        <div style="color:#FFA500;line-height: 90px;">扩展仓库</div>
                        <div style="width: 224px;height: 420px;position: relative;top: 22px;left: 37px;">
                            <!--                            <div style="display: flex;-ms-flex-wrap: wrap;flex-wrap: wrap;">-->
                            <?if(!empty($item)){?>
                                <?for($i=120;$i<240;$i++){?>
                                    <?if (isset($item[$i]['url'])) {?>
                                        <div style="margin-top:<?=($item[$i]['y'] * 28)?>px;margin-left:<?=($item[$i]['x'] * 28)?>px;">
                                            <div style="position: absolute;background: url('<?= __PATH_PUBLIC_IMG__ ?>warehouse/VA.jpg') repeat;width:<?=($item[$i]['width'] * 28)?>px;height:<?=($item[$i]['height'] * 28)?>px;">
                                                <div class="square data-info" data-info="<?=$item[$i]['code']?>" style="width:<?=($item[$i]['width'] * 28)?>px;height:<?=($item[$i]['height'] * 28)?>px;background: url('<?= $item[$i]['url']?>') center;"></div>
                                            </div>
                                        </div>
                                    <?}else{?>
                                        <div id="item-null-<?=$i?>"></div>
                                    <?}?>
                                <?}?>
                            <?}?>
                            <!--                            </div>-->
                        </div>
                    </div>
                </div>
                <?}?>
            </div>
        </div>
    </div>

    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}