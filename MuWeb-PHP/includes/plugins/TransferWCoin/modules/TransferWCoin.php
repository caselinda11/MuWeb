<?php
/**
 * [WCoinTransfer]模块页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    if(!isLoggedIn()) redirect(1,'login');
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">货币转移</li>
        </ol>
    </nav>
    <?php
    $Transfer = new \Plugin\WCoinTransfer();
    $tConfig = $Transfer->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色才能使用该功能！');
    try{
        if(check_value($_POST['submit'])) {
            $Transfer->setWCoinTransfer($_SESSION['group'],$_SESSION['username'],$_POST['user'],$_POST['price'],$_POST['credit_type']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    $creditSystem = new CreditSystem();
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">货币转移</div>
        <div class="card-body">
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="user" class="col-sm-4 col-form-label text-right">
                        对方账号
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="user" name="user" maxlength="10" required>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="price" class="col-sm-4 col-form-label text-right">
                        转移金额
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="price" name="price" maxlength="8">
                    </div>
                    <div class="col-sm-3 col-form-label"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="credit_type" class="col-sm-4 col-form-label text-right">
                        转移类型
                    </label>
                    <div class="col-sm-5">
                        <?=$creditSystem->buildSelectInput("credit_type", 1, "form-control"); ?>
<!--                        <select name="price_type" id="price_type" class="form-control">-->
<!--                            <option value="1">--><?//=getPriceType(1)?><!--</option>-->
<!--                        </select>-->
                    </div>
                    <div class="col-sm-3 col-form-label"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('TransferWCoin')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                        确定
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>转移要求</li>
                    <p class="alert alert-info">转移双方账号必须为离线状态，且该账号属于正常状态才可使用。</p>
                    <li>转移须知</li>
                    <p class="alert alert-info">请认真输入账号核对谨慎操作，一旦转移成功将无法撤回操作。</p>
                </ol>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">货币转移</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}