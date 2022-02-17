<?php
/**
 * 交易市场
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {

    ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">物品寄售</li>
        </ol>
    </nav>
    <?php
    if(!isLoggedIn()) redirect(1,'login');
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('item');
    $market->MarketMenu();
    $market->marketSellMenu();
    if(!$moduleConfig['active']) throw new Exception('暂时未开放物品交易市场！');
    $common = new common();
    if ($common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) throw new Exception("您的账号当前游戏在线，请先断开连接。");
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您的账号没有角色可以出售!');

    try{
        if(check_value($_REQUEST['request'])){
            if (!Validator::Number($_REQUEST['request'])) throw new Exception('数据错误，禁止站外提交！');
            $market->setCharSellOff($_SESSION['group'], $_SESSION['username'],$_REQUEST['request'],true);
        }
        if(check_value($_POST['submit'])){
            $market->setCharSell($_SESSION['group'],$_SESSION['username'],$_POST['name'],$_POST['price'],$_POST['price_type'],$_POST['code'],true);
        }
    }catch (Exception $ex){
        $_POST = [];
        message('error', $ex->getMessage());
    }
    ?>

    <p class="alert alert-info">寄售说明：物品寄售其实就是把整个角色包含背包出售出去，且寄售中角色处于封停状态。</p>

    <div class="card mb-3">
        <div class="card-header">我的角色</div>
        <div class="">
            <table class="table table-striped table-bordered text-center">

                <thead>
                <tr>
                    <th>角色</th>
                    <th>名称</th>
                    <th width="20%">价格</th>
                    <th width="15%">价格类型</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($AccountCharacters as $code=>$thisCharacter) {
                    $characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                    if($characterData['CtlCode']) continue;
                    $characterIMG = $character->GenerateCharacterClassAvatar($characterData[_CLMN_CHR_CLASS_],1,1,30);
                    $countLevel = $characterData['cLevel']+$characterData['mLevel'];
                    if($moduleConfig['min_level'] > $countLevel || $moduleConfig['max_level'] < $countLevel) continue;
                    ?>
                    <form class="form-horizontal" action="" method="post">
                        <tr>
                            <td><?=$characterIMG?></td>
                            <td><?=$characterData['Name']?></td>
                            <td><input type="text" name="price" id="" class="form-control" onkeyup="this.value=this.value.replace(/\D/g,'')" maxlength="8"/></td>
                            <td><?$creditSystem = new CreditSystem();?>
                                <?=$creditSystem->buildSelectInput("price_type", 1, "form-control"); ?>
                            </td>
                            <td>
                                <input type="hidden" name="name" value="<?=$characterData['Name']?>"/>
                                <input type="hidden" name="code" value="<?=$code?>"/>
                                <input type="hidden" name="key" value="<?=Token::generateToken('market_char'.$code)?>"/>
                                <div class="btn-group">
                                    <button name="submit" value="submit" class="btn btn-success">售卖背包</button>
                                </div>
                            </td>
                        </tr>
                    </form>
                <?}?>
                </tbody>
            </table>
        </div>
    </div>
    <?#寄售中的角色
    $MyList = $market->getMyMarketCharList($_SESSION['username'],true);
    if(is_array($MyList)){
        ?>
        <hr>
        <div class="card mb-3">
            <div class="card-header">正在寄售中的背包</div>
            <table class="table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th>单号</th>
                    <th>角色</th>
                    <th>价格</th>
                    <th>价格类型</th>
                    <th width="20%">发布时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?foreach ($MyList as $data){?>
                    <tr>
                        <td><?=$data['ID']?></td>
                        <td><?=playerProfile($data['servercode'],$data['name'])?></td>
                        <td><?= $data['price'] ?></td>
                        <td><?=getPriceType($data['price_type'])?></td>
                        <td><?=date("m/d h:i:s",$data['date'])?></td>
                        <td><a href="<?=__BASE_URL__?>Market/ItemSell/<?=$data['ID']?>" class="btn btn-outline-success">角色下架</a></td>
                    </tr>
                <?}?>
                </tbody>
            </table>
        </div>
    <?}?>

    <div class="card">
        <div class="card-header">交易市场使用指南</div>
        <div class="card-body">
            <p class="alert alert-info">1、等级限制：寄售角色仅显示等级+大师等级<span class="text-danger"><?=$moduleConfig['min_level']?>~<?=$moduleConfig['max_level']?></span>级之间的角色！</p>
            <p class="alert alert-info">2、价格限制：售价不得低于<span class="text-danger"><?=$moduleConfig['min_price']?></span>，货币基于您选择的货币类型！</p>
            <?$rate = ($moduleConfig['price_type']) ? $moduleConfig['price_rate'].'%' : $moduleConfig['price'];?>
            <p class="alert alert-info">3、服务费率：收取<span class="text-danger"> <?=$rate?> </span>交易服务费，扣除基于您售卖的币种！</p>
            <p class="alert alert-info">4、扣费设置：成功售卖将自动从价格中扣除，未交易则不扣费！</p>
            <p class="alert alert-info">5、一旦售卖则该角色将被临时冻结，下架或成功售出解除封停！</p>
            <?if($moduleConfig['vip_items']){?><p class="alert alert-info text-danger">6、会员物品：背包内不可包含不可以含有会员道具，否则无法寄售成功。</p><?}?>
        </div>
    </div>

    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}