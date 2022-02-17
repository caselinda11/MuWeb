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
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>shops">在线商城</a></li>
            <li class="breadcrumb-item active" aria-current="page">购买物品</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $shop = new \Plugin\Shop();
    $mConfig = $shop->loadConfig();
    if (!$mConfig['active']) throw new  Exception('该功能暂已关闭，请稍后尝试访问。');
    if(!check_value($_GET['request'])) throw new Exception('[1] - '.'出错了，请您重新输入！');
    if(!Validator::Number($_GET['request'])) throw new  Exception('出错了，请您重新输入！');
    $data = $shop->getShopDataList($_GET['request']);
    if(!is_array($data)) throw new Exception("该商品已下架，请重新选择商品！");
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色用于接收物品！');
    try{
        if (check_value($_POST['submit'])){
            if(!check_value($_POST['id'])) throw new Exception('出错了，请您重新输入！');
            if(!Validator::Number($_POST['id'])) throw new  Exception('出错了，请您重新输入！');
            if(!Token::checkToken('shop'.$_POST['id'])) throw new Exception('出错了，请您重新输入！');
            $shop->setBuyShopItem($_SESSION['group'],$_SESSION['username'],$_POST['character_name'],$_POST['id']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    //积分 or 元宝
    $configCredits = 0;
    try{
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($data['price_type']);
        $configSettings = $creditSystem->showConfigs(true);
        switch ($configSettings['config_user_col_id']) {
            case 'username':
                $creditSystem->setIdentifier($_SESSION['username']);
                break;
            case 'userid':
                $creditSystem->setIdentifier($_SESSION['userid']);
                break;
            case 'character':
                $creditSystem->setIdentifier($_SESSION['character']);
                break;
            default:
                throw new Exception("[货币系统]无效的标识符。");
        }
        $configCredits = $creditSystem->getCredits($_SESSION['group']);
    }catch (Exception $exception){ }
    #计算剩余商品总数
    if($data['item_count'] ){
        $count = $shop->checkItemCount($data['id']);
        if(($data['item_count'] - $count) <= 0) message('error','该商品已经售馨，请下次活动再来。');
    }
    ?>
    <div class="card">
        <div class="card-header"><?=$data['item_name']?></div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-4">
                    <div class="card text-center">
                        <h6 title="<?=$data['item_name']?>" class="card-header font-weight-bold text-truncate"><?=$data['item_name']?></h6>
                        <div class="card-body bg-dark p-0" style="height: 150px; line-height: 150px;">
                            <?$item = new \Plugin\equipment();?>
                            <?=($data['item_type'] == 0) ? '<img src="'.__PATH_PUBLIC_IMG__.'shops/shop.gif" alt="" data-toggle="tooltip" data-placement="bottom" data-original-title="礼包类型暂时无法查看" />' : '<img src="'.$item->ItemsUrl($data['item_code']).'" alt="暂无图片"  class="data-info" data-info="'.$data['item_code'].'">'?>
                        </div>
                        <div class="card-footer p-2">
                            <small class="font-weight-bold">
                                <div>鼠标放置物品上可查看属性</div>
                            </small>
                        </div>
                        <?if($data['item_count']){?>
                            <div class="position-absolute" style="z-index:10">
                                <?$img = ($data['item_count'] - $count) <= 0 ? "Promotion_off.png" : "Promotion.png";?>
                                <img src="<?=__PATH_PUBLIC__?>img/shops/<?=$img?>" alt="" width="70"/>
                            </div>
                        <?}?>
                    </div>

                    <div class="mt-3 mb-3">
                        <div class=" input-group mb-3">
                            <div class="input-group-prepend text-center"><label for="shopType" class="input-group-text">当前余额</label></div>
                            <input type="text" id="shopType" class="form-control font-weight-bold text-right" disabled="disabled" value="<?=number_format($configCredits)?>" />
                            <div class="input-group-prepend text-center"><label class="input-group-text"><?=getPriceType($data['price_type'])?></label></div>
                        </div>
                    </div>
                    <?if($data['item_count']){?>
                        <div class="mt-1 mb-3">
                            <div class="alert alert-info text-center" role="alert">此商品限购 请抓紧抢购</div>
                        </div>
                        <div class="mt-3 mb-3">
                            <div class=" input-group mb-3">
                                <div class="input-group-prepend text-center"><label for="shopType" class="input-group-text">剩余件数</label></div>
                                <input type="text" id="shopType" class="form-control font-weight-bold text-right" disabled="disabled" value="<?=number_format($data['item_count']-$count)?>" />
                                <div class="input-group-prepend text-center"><label class="input-group-text">件</label></div>
                            </div>
                        </div>
                    <?}?>
                </div>
                <div class="col-xl-8">
                    <div class="form-row">
                        <div class="w-100">
                            <div class="alert alert-info" role="alert">购买后请在游戏中的 (保险箱) 中领取！</div>
                            <div class="alert alert-warning" role="alert">物品购买后请在7日内领取，否则将会失效！</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class=" input-group mb-3">
                            <div class="input-group-prepend text-center"><label for="shopType" class="input-group-text">所属类型</label></div>
                            <? $shopType = $shop->shopType();?>
                            <input type="text" id="shopType" class="form-control" disabled="disabled" value="<?=$shopType[$data['item_type']]?>" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class=" input-group mb-3">
                            <div class="input-group-prepend text-center"><label for="price" class="input-group-text">商品价格</label></div>
                            <input type="text" class="form-control font-weight-bold" id="price" disabled="disabled" value="<?=$data['item_price']?>" />
                            <div class="input-group-prepend text-center"><label class="input-group-text"><?=getPriceType($data['price_type'])?></label></div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="input-group mb-3">
                            <div class="alert" role="alert" style="background-color: #e9ecef;width: 100%;border: 1px solid #ced4da;border-radius: .25rem;">
                                <strong>物品描述</strong><br>
                                <?=$data['item_content']?>
                            </div>
                        </div>
                    </div>
                    <form class="form-horizontal" action="" method="post">
                        <div class="form-row">
                            <div class=" text-center"><span class="text-danger">*选择一个角色用于接收该物品</span></div>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend text-center"><label for="character" class="input-group-text">接收角色</label></div>
                                <select id="character" name="character_name" class="form-control">
                                    <?foreach($AccountCharacters as $code=>$thisCharacter) {?>
                                        <?$characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                                        if($characterData['CtlCode']) continue;?>
                                        <option value="<?=$thisCharacter?>"><?=$thisCharacter?></option>
                                    <?}?>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="col-lg-12 mb-4">
                    <input type="hidden" name="id" value="<?=$data['id']?>" />
                    <input type="hidden" name="key" value="<?=Token::generateToken('shop'.$data['id'])?>"/>
                    <button type="submit" value="submit" name="submit" class="btn btn-success pull-right j-submit col-md-3">购买</button>
                </div>
            </div>
        </div>
    </div>
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}