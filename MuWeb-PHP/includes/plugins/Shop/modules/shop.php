<?php
/**
 * 在线商城
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
        <li class="breadcrumb-item active" aria-current="page">在线商城</li>
    </ol>
</nav>
<?php
try {
    $shop = new \Plugin\Shop();
    $mConfig = $shop->loadConfig();
    if($mConfig['check_login']) if(!isLoggedIn()) throw new Exception('该模块仅限会员可见，请先<a href="'.__BASE_URL__.'login">登陆账号</a>！');
    if (!$mConfig['active']) throw new  Exception('该功能暂已关闭，请稍后尝试访问。');
    $list = $shop->getShopDataList();
    if(!is_array($list)) throw new  Exception('商城暂无可购买的商品。');

    #初始化
    if(!isset($_GET['class'])) $_GET['class'] = 0;
    if(!isset($_GET['itemCategory'])) $_GET['itemCategory'] = 'all';

    if (check_value($_GET['class']) || check_value($_GET['itemCategory']) || $_GET['search']) {
        try{
            $list = $shop->search($_GET['class'],$_GET['itemCategory']);
        }catch (Exception $exception){
            message('error',$exception->getMessage());
        }
    }
    ?>
    <div class="col-md-12 mb-2">
        <div class="row">
            <div class="input-group">
                    <div class="input-group col-md-6" style="padding-right:0;padding-left:0;">
                        <div class="input-group-prepend">
                            <div class="input-group-text">职业</div>
                        </div>
                        <select id="class" onchange="window.location='?class='+this.value+'&amp;itemCategory=<?=$_GET['itemCategory']?>'" class="form-control">
                            <?foreach ($shop->classType() as $key=>$item){?>
                                <option value="<?=$key?>" <?=selected($_GET['class'], (string)$key)?>><?=$item?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="input-group col-md-6" style="padding-right:0;padding-left:0;">
                        <div class="input-group-prepend">
                            <div class="input-group-text">类型</div>
                        </div>
                        <select id="item_category" onchange="window.location='?class=<?=$_GET['class']?>&amp;itemCategory='+this.value+''" class="form-control">
                            <option value="all">所有类型</option>
                            <?foreach ($shop->shopType() as $key=>$item){?>
                                <option value="<?=$key?>" <?=selected($_GET['itemCategory'], (string)$key)?>><?=$item?></option>
                            <?}?>
                        </select>
                    </div>
            </div>
        </div>
    </div>
    <div class="card">
        <?if(is_array($list)){?>
        <?if(empty($list)) message('error','暂无商品!');?>
        <div class="col-md-12">
            <div class="row">
                    <?foreach ($list as $data){
                        $count = $shop->checkItemCount($data['id']);
                        $item = new \Plugin\equipment();
                        $itemImg = ($data['item_type'] == 0) ? '<img src="'.__PATH_PUBLIC_IMG__.'shops/shop.gif" alt="" />' : '<img src="'.$item->ItemsUrl($data['item_code']).'" alt="暂无图片">';
                        ?>
                        <?if(!$data['status']) continue;?>
                <div class="col-xl-3 p-2">
                    <div class="card text-center">
                        <h6 title="<?=$data['item_name']?>" class="card-header font-weight-bold text-truncate"><?=$data['item_name']?> </h6>
                        <div class="card-body bg-dark p-0" style="height: 150px; line-height: 150px;">
                            <?=$itemImg?>
                        </div>
                        <div class="card-footer p-2">
                            <small class="font-weight-bold"> <?=$data['item_price']?> <?=getPriceType($data['price_type'])?></small>
                            <a href="<?=__BASE_URL__?>shop/buy/<?=$data['id']?>" class="btn btn-outline-danger btn-sm pull-right">购买</a>
                        </div>
                        <?if($data['item_count']){?>
                            <div class="position-absolute" style="z-index:10">
                                <?$img = ($data['item_count'] - $count) <= 0 ? "Promotion_off.png" : "Promotion.png";?>
                                <img src="<?=__PATH_PUBLIC__?>img/shops/<?=$img?>" alt="" width="70"/>
                            </div>
                        <?}?>
                    </div>
                </div>
                <?}?>
            </div>
        </div>
        <?}else{?>
            <div class="card-body">
                <div class="alert alert-danger text-center" role="alert">
                    暂无商品信息!
                </div>
            </div>
        <?}?>
    </div>
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}