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
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>Market">交易市场</a></li>
        <li class="breadcrumb-item active" aria-current="page">物品寄售</li>
    </ol>
</nav>
<?
try{
    if(!isLoggedIn()) redirect(1,'login');
    $market = new \Plugin\Market\Market();
    $market->MarketMenu();

    $modeConfig = $market->loadConfig('item');
    if(!$modeConfig['active']) throw new Exception('未开放物品交易市场！');
    #验证在线情况
    $common = new common();
    if ($common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) throw new Exception("您的账号当前游戏在线，请先断开连接。");
    $warehouse = new \Plugin\Market\warehouse($_SESSION['group']);
    $itemList = $warehouse->getWarehouseList();

    $creditSystem = new CreditSystem();
    try {
        if(check_value($_REQUEST['request'])){
            if (!Validator::Number($_REQUEST['request'])) throw new Exception('数据错误，禁止站外提交！');
            $market->setItemSellOff($_SESSION['group'], $_SESSION['username'],$_REQUEST['request'],0);
        }
        if(check_value($_POST['submit'])){
            if (!check_value($_POST['item'])) throw new Exception("没有检测到您的物品信息!");
            if (!$_POST['price_type']) alert('Market/SellItem','货币类型错误！');
            $market->setItemSell($_SESSION['group'], $_SESSION['username'], $_POST['char_name'], $itemList[$_POST['item']],$_POST['item_type'],$_POST['price'],$_POST['price_type']);
        }
    }catch (Exception $exception){
        message('error', $exception->getMessage());
    }
    $market->marketSellMenu();
    if($modeConfig['jewel']) message('info',"市场目前仅可寄售宝石类。(玛雅、祝福、灵魂、生命、创造、守护、再生、进化宝石、宝石组合等。)");
    if(!$modeConfig['vip_items']) message('info',"会员道具禁止出售，且不会识别显示出来。");
    ?>
<div class="card mb-3">
    <div class="card-header">寄售物品</div>
    <div class="card-body">
        <form class="form-horizontal" action="" method="post">
            <div class="form-group row justify-content-md-center">
                <label for="item" class="col-sm-4 col-form-label text-right">选择物品</label>
                <div class="col-sm-4">
                    <select name="item" id="item" class="form-control">
                        <?if(is_array($itemList)){
                            $equipment = new \Plugin\equipment();
                            foreach ($itemList as $id=>$data){
                                if(!$modeConfig['vip_items']) if (substr($data,6,2) == "FF") continue;
                                $AData = str_ireplace(str_pad("F",ITEM_SIZE,"F"),'',$data);
                                if(!$AData) continue;
                                $item = $equipment->convertItem($AData);
                                $name = $equipment->getItemName($item['section'],$item['index'],$item['level']);
                                ?>
                                <option value="<?=$id?>"><?=$name?></option>
                            <?}?>
                        <?}else{?>
                            <option value="">暂无数据</option>
                        <?}?>
                    </select>
                </div>
                <div class="col-sm-4"></div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="char_name" class="col-sm-4 col-form-label text-right">卖家选择</label>
                <div class="col-sm-4">
                    <select name="char_name" id="char_name" class="form-control">
                            <option value="">选择一个角色作为卖家</option>
                        <?    # 角色信息
                        $character = new Character();
                        $AccountCharacters = $character->getAccountCharacterNameForAccount($_SESSION['group'],$_SESSION['username']);
                        if(is_array($AccountCharacters)) {
                            foreach($AccountCharacters as $characterName) {
                                if(empty($characterName)) continue;
                        ?>
                            <option value="<?=$characterName?>"><?=$characterName?></option>
                            <?}?>
                        <?}?>
                    </select>
                </div>
                <div class="col-sm-4 col-form-label">*显示作用</div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="item_type" class="col-sm-4 col-form-label text-right">物品类型</label>
                <div class="col-sm-4">
                    <select name="item_type" id="item_type" class="form-control">
                        <?foreach ($market->itemType as $id=>$name){?>
                            <?if($modeConfig['jewel']) if ($id !== 6) continue;?>
                            <option value="<?=$id?>"><?=$name?></option>
                        <?}?>
                    </select>
                </div>
                <div class="col-sm-4 col-form-label">*</div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="price" class="col-sm-4 col-form-label text-right">物品售价</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="price" id="price"  maxlength="10" required>
                </div>
                <div class="col-sm-4"></div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="price_type" class="col-sm-4 col-form-label text-right">货币类型</label>
                <div class="col-sm-4">
                    <?$creditSystem = new CreditSystem();
                    if($modeConfig['limit']){?>
                        <select name="price_type" id="price_type" class="form-control">
                            <option value="<?=$modeConfig['limit_price_type']?>"><?=getPriceType($modeConfig['limit_price_type'])?></option>
                        </select>
                    <?}else{?>
                        <?=$creditSystem->buildSelectInput("price_type", $modeConfig['limit_price_type'], "form-control"); ?>
                    <?}?>
                </div>
                <div class="col-sm-4 col-form-label">*</div>
            </div>
            <div class="form-group row justify-content-md-center">
                <input type="hidden" name="key" value="<?=Token::generateToken('market_item')?>"/>
                <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3">
                    寄售物品
                </button>
            </div>
        </form>
        <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
            <cite>
                如有问题请联系我们的在线客服
            </cite>
        </footer>
    </div>
</div>
    <?$MyList = $market->getMyMarketItemList($_SESSION['username']);
    if(is_array($MyList)){
    $equipment = new \Plugin\equipment();?>
    <div class="card mb-3">
        <div class="card-header">寄售中的物品</div>
        <div class="">
        <table class="table table-bordered text-center">
            <thead>
            <tr>
                <td>物品</td>
                <td>价格</td>
                <td>发布日期</td>
                <td>操作</td>
            </tr>
            </thead>
            <?foreach($MyList as $data){?>
                <tr class="data-info" data-info="<?=$data['item_code'];?>">
                    <td><img src="<?=$equipment->ItemsUrl($data['item_code'])?>"  alt="<?=$data['item_code']?>" width="35" height="35"/></td>
                    <td><?=$data['price']?><sup><?=getPriceType($data['price_type'])?></sup></td>
                    <td><?=$data['date']?></td>
                    <td><a href="<?=__BASE_URL__?>Market/ItemSell/<?=$data['ID']?>" class="btn col-md-10 btn-success btn-sm">下架</a></td>
                </tr>
            <?}?>
            </table>
        </div>
    </div>
    <?}?>
            <div class="card">
                <div class="card-body">物品市场交易使用说明</div>
                <div class="card-body">
                    <p class="alert alert-info">1、操作指南：物品是从默认仓库由上至下显示，选择即可出售。</p>
                    <p class="alert alert-info">2、禁止出售：如果提示禁止出售，即代表该物品禁止出售。</p>
                    <p class="alert alert-info">3、价格限制：售价不得低于<span class="text-danger"><?=$modeConfig['min_price']?></span>，货币基于您选择的货币类型！</p>
                    <?$rate = ($modeConfig['price_type']) ? '%' : '';?>
                    <p class="alert alert-info">4、服务费率：收取<span class="text-danger"> <?=$modeConfig['price_rate']?><?=$rate?> </span>交易服务费，扣除基于您售卖的币种！</p>
                    <p class="alert alert-info">5、扣费设置：成功卖出将自动从价格中扣除，未交易则不扣费！</p>
                    <p class="alert alert-info">6、一旦售卖物品将会从仓库中取出，下架可取出物品至仓库中！</p>
                </div>
            </div>

<?} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
