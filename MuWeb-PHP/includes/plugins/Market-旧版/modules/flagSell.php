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
        <li class="breadcrumb-item active" aria-current="page">旗帜寄售</li>
    </ol>
</nav>
<?php
try{
    if(!isLoggedIn()) redirect(1,'login');
    $market = new \Plugin\Market\Market();
    $market->MarketMenu();
    $market->marketSellMenu();

    $modeConfig = $market->loadConfig('flag');
    if(!$modeConfig['active']) throw new Exception('暂未开放旗帜交易市场！');

    $common = new common();
    if ($common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) throw new Exception("您的账号当前游戏在线，请先断开连接。");
    $Characters = $market->getCharacterInventory($_SESSION['group'],$_SESSION['username']);

    $creditSystem = new CreditSystem();
    try {
        if(check_value($_REQUEST['request'])){
            if (!Validator::Number($_REQUEST['request'])) throw new Exception('数据错误，禁止站外提交！');

        }
        if(check_value($_POST['submit'])){
            if (!$_POST['price_type']) throw new Exception('货币类型不可为空，请联系在线客服添加货币类型！');
            $market->setCharacterFlagSell($_SESSION['group'], $_SESSION['username'],$_POST['id'],$_POST['price'],$_POST['price_type']);
        }
    }catch (Exception $exception){
        message('error', $exception->getMessage());
    }
?>


<div class="card mb-3">
    <div class="card-header">旗帜寄售</div>
    <div class="card-body">
        <form class="form-horizontal mt-5" action="" method="post">
            <div class="form-group row justify-content-md-center">
                <label for="id" class="col-sm-2 col-form-label text-right">选择角色</label>
                <div class="col-sm-5">
                    <select name="id" id="id" class="form-control">
                        <?if(is_array($Characters)){?>
                            <?foreach ($Characters as $id=>$data){?>
                                <option value="<?=$id?>"><?=$data['Name']?></option>
                            <?}?>
                        <?}else{?>
                            <option value="">暂无数据</option>
                        <?}?>
                    </select>
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="price" class="col-sm-2 col-form-label text-right">旗帜售价</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" name="price" id="price"  maxlength="10" required>
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="price_type" class="col-sm-2 col-form-label text-right">货币类型</label>
                <div class="col-sm-5">
                    <?= $creditSystem->buildSelectInput("price_type", 1, "form-control"); ?>
                </div>
                <div class="col-sm-1">*</div>
            </div>
            <div class="form-group row justify-content-md-center">
                <input type="hidden" name="key" value="<?=Token::generateToken('market_flag')?>"/>
                <button type="submit" name="submit" value="submit" class="col-sm-offset-4 btn btn-success col-sm-4">
                    寄售物品
                </button>
            </div>
        </form>

        <footer class="blockquote-footer text-right mt-3 mb-3 mr-3">
            关于寄售 -
            <cite title="Source Title">
                寄售说明
            </cite>
        </footer>
    </div>
</div>
    <div class="card">
        <div class="card-header">旗帜交易市场使用说明</div>
        <div class="card-body">
            <p class="alert alert-info">1、操作指南：选择拥有旗帜的角色，系统将自动识别出旗帜。</p>
            <p class="alert alert-info">2、交易限制：被封停或寄售中的角色无法使用旗帜寄售功能。</p>
            <p class="alert alert-info">3、价格限制：售价不得低于<span class="text-danger"><?=$modeConfig['min_price']?></span>，货币基于您选择的货币类型！</p>
            <?$rate = ($modeConfig['price_type']) ? $modeConfig['price_rate'].'%' : $modeConfig['price'];?>
            <p class="alert alert-info">4、服务费率：收取<span class="text-danger"> <?=$rate?> </span>交易服务费，扣除基于您售卖的币种！</p>
            <p class="alert alert-info">5、扣费设置：成功卖出将自动从价格中扣除，未交易则不扣费！</p>
            <p class="alert alert-info">6、一旦售卖旗帜将会从背包中取出，下架可取出旗帜至仓库中！</p>
        </div>
    </div>
<?} catch(Exception $ex) {
    message('error', $ex->getMessage());
}