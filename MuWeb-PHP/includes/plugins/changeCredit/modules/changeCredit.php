<?php
/**
 * [货币转换]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">货币转换</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $changeCredit = new \Plugin\changeCredit();
    $tConfig = $changeCredit->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    $creditSystem = new CreditSystem();
    $edu1 = $changeCredit->getAccountCreditPoint(1);
    $edu2 = $changeCredit->getAccountCreditPoint(2);
    if(check_value($_POST['submit'])){
        try{
            $changeCredit->changeCreditPoint();
        }catch (Exception $exception){
            message('error',$exception->getMessage());
        }
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">货币转换</div>
        <div class="card-body">
            <div class="mt-3">
                <ol style="word-break: break-all;">
                    <?if($tConfig['check_online']){?><li class="alert alert-info">使用该功能请确保您的账号游戏中处于离线状态。</li><?}?>
                    <li class="alert alert-info">使用该功能请先确保您的余额充足，<?=$tConfig['name_1']?>转<?=$tConfig['name_2']?>比例<?=$tConfig['price_1']?><?if($tConfig['orientation']){?>，<?=$tConfig['name_2']?>转<?=$tConfig['name_1']?>比例<?=$tConfig['price_2']?><?}?>。</li>
                    <li class="alert alert-info">每次使用该功能的金额必须在[<?=$tConfig['min_price']?>~<?=$tConfig['max_price']?>]之间。</li>
                    <li class="alert alert-info">当前账号：<strong><?=$_SESSION['username']?></strong>，当前余额：<?=$tConfig['name_2']?>[<strong><?=number_format($edu2)?></strong>]，<?=$tConfig['name_1']?>[<strong><?=number_format($edu1)?></strong>]。</li>
                </ol>
            </div>
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="old_id" class="col-sm-4 col-form-label text-right">
                        选择货币
                    </label>
                    <div class="col-sm-4">
                        <?if($tConfig['orientation']){?>
                            <select name="credit_old" class="form-control">
                                <option value="1"><?=$tConfig['name_1']?></option>
                                <option value="2"><?=$tConfig['name_2']?></option>
                            </select>
                        <?}else{?>
                            <select name="credit_old" class="form-control">
                                <option value="1"><?=$tConfig['name_1']?></option>
                            </select>
                        <?}?>
                    </div>
                    <div class="col-sm-4 form-text text-muted"><small>*要转换的</small></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="new_id" class="col-sm-4 col-form-label text-right">
                        转换为
                    </label>
                    <div class="col-sm-4">
                        <?if($tConfig['orientation']){?>
                            <select name="credit_new" class="form-control">
                                <option value="1"><?=$tConfig['name_1']?></option>
                                <option value="2" selected><?=$tConfig['name_2']?></option>
                            </select>
                        <?}else{?>
                            <select name="credit_new" class="form-control">
                                <option value="2"><?=$tConfig['name_2']?></option>
                            </select>
                        <?}?>
                    </div>
                    <div class="col-sm-4 form-text text-muted"><small>*转换后的</small></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="new_id" class="col-sm-4 col-form-label text-right">
                        转换金额
                    </label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="number" />
                    </div>
                    <div class="col-sm-4 form-text text-danger"><small>*<?=$tConfig['name_2']?>数量</small></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('changeCredit')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3">
                        确定
                    </button>
                </div>
            </form>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">货币转换</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}