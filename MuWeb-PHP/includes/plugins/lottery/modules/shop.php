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
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp/lotteryExtract">在线夺宝</a></li>
        <li class="breadcrumb-item active" aria-current="page">稀有商店</li>
    </ol>
</nav>
<?php
try{
    if (!isLoggedIn()) redirect(1, 'login');
    $lottery = new \Plugin\lottery();
    $tConfig = $lottery->loadConfig('config');
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');

    $sList = $lottery->getLotteryShop();
    if(!$sList) throw new Exception('暂无稀有物品信息，请稍后再试或联系在线客服！');
    try{
        if(check_value($_POST['submit'])){
            if(!check_value($_POST['id'])) throw new Exception('出错了，请您重新输入！');
            if(!Validator::Number($_POST['id'])) throw new  Exception('出错了，请您重新输入！');
            if(!Token::checkToken('ReceiveShop'.$_POST['id'])) throw new Exception('出错了，请您重新输入！');
            $lottery->setLotteryReceiveShop($_SESSION['group'],$_SESSION['username'],$_POST['id']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
?>
    <div class="card mt-3 mb-3" style="min-width: 730px">
        <div class="card-header">稀有商店</div>
        <div class="card-body">

            <div class="row">
                <?if(is_array($sList)){?>
                    <?foreach ($sList as $sData){
                        if(!$sData['status']) continue;
                        ?>
                        <div class="col-xl-3 p-2">
                            <div class="card text-center">
                                <h6 title="<?=$sData['reward_item_name']?>" class="card-header font-weight-bold text-truncate">
                                    <?=$sData['reward_item_name']?>
                                </h6>
                                <div class="card-body bg-dark p-0" style="height: 150px; line-height: 150px;">
                                    <?$item = new \Plugin\equipment()?>
                                    <img src="<?=$item->ItemsUrl($sData['reward_item_code'])?>" alt="暂无图片" class="data-info" data-info="<?=$sData['reward_item_code']?>" />
                                </div>
                                <div class="card-footer p-2">
                                    <form action="" method="post">
                                        <small class="font-weight-bold"><?=$sData['reward_item_price']?> <?=$tConfig['Crystal_Name']?></small>
                                        <input type="hidden" name="id" value="<?=$sData['ID']?>" />
                                        <input type="hidden" name="key" value="<?=Token::generateToken('ReceiveShop'.$sData['ID'])?>"/>
                                        <button type="submit" name="submit" value="submit" class="btn btn-outline-dark btn-sm pull-right">兑换</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?}?>
                <?}?>
            </div>
        </div>
    </div>
<?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
