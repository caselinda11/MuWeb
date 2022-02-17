<?php
/**
 * 我的插件模块页面
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
            <li class="breadcrumb-item active" aria-current="page">角色铸造</li>
        </ol>
    </nav>
    <?php
    $WCoinStatus = new \Plugin\WCoinStatus();
    $tConfig = $WCoinStatus->loadConfig();
    $tConfig = $WCoinStatus->loadConfig('config');
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色才能使用该功能！');
    try{
        if(check_value($_POST['submit'])) {
            $WCoinStatus->setWCoinStatus($_SESSION['group'],$_SESSION['username'],$_POST['character_name'],$_POST['point']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">角色铸造</div>
        <div class="card-body mb-3">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                </div>
                <div class="col-md-3">
                <? //积分 or 元宝
                try {
                    $creditSystem = new CreditSystem();
                    $creditConfigList = $creditSystem->showConfigs();
                    if(is_array($creditConfigList)) {
                        ?>
                        当前余额:
                        <?
                        foreach($creditConfigList as $myCredits) {
                            if(!$myCredits['config_display']) continue;
                            $creditSystem->setConfigId($myCredits['config_id']);
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
                            $configCredits = $creditSystem->getCredits($_SESSION['group']); ?>
                            <div class="form-control col-sm-12 text-right"><?=$myCredits['config_title'];?> <?=number_format($configCredits);?></div>
                        <?}
                    }
                } catch(Exception $ex) {}
                ?>
                </div>
            </div>
            <form class="form-horizontal mb-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="character" class="col-sm-4 col-form-label text-right">
                        选择角色
                    </label>
                    <div class="col-sm-5">
                        <select id="character" name="character_name" class="form-control">
                            <?foreach($AccountCharacters as $code=>$thisCharacter) {?>
                                <?$characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                                if($characterData['CtlCode']) continue;?>
                                <option value="<?=$thisCharacter?>"><?=$thisCharacter?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="point" class="col-sm-4 col-form-label text-right">
                        铸造点数
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="point" name="point" maxlength="10" required>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('WCoinStatus')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4 mb-4">
                        确定
                    </button>
                </div>
            </form>
            <div class="col-md-12 mb-3">
                <table class="text-center table table-striped table-bordered">
                    <tr><th colspan="6">角色铸造要求</th></tr>
                    <tr>
                        <th>/</th>
                        <?for($i=1;$i<=5;$i++){?>
                            <td>[<b><?=$i?></b>]阶段</td>
                        <?}?>
                    </tr>
                    <tr>
                        <td>最高获得点数</td>
                        <?for($i=1;$i<=5;$i++){?>
                            <td><?=$tConfig['points_'.$i]?></td>
                        <?}?>
                    </tr>
                    <tr>
                        <td>每属性点价格</td>
                        <?for($i=1;$i<=5;$i++){?>
                            <td><?=$tConfig['credit_price_'.$i]?><?=getPriceType($tConfig['credit_type_'.$i])?></td>
                        <?}?>
                    </tr>
                    <tr><th colspan="6">角色铸造信息</th></tr>
                    <?
                    $dataChar = $WCoinStatus->getCharStatus($_SESSION['group'],$_SESSION['username']);
                    foreach ($dataChar as $data){
                        ?>
                        <tr>
                            <td><?=$data['Name']?></td>
                            <?for($i=1;$i<=5;$i++){?>
                            <td><?=$data['Point'.$i]?></td>
                            <?}?>
                        </tr>
                    <?} ?>
                </table>
            </div>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>铸造说明</li>
                    <p class="alert alert-info">铸造的含义是用货币兑换相应的属性点，属性点终身享用。</p>
                    <li>铸造要求</li>
                    <p class="alert alert-info">角色必须达到<kbd><?=$tConfig['min_level']?></kbd>级，且角色处于离线状态才可使用该功能。</p>
                    <li>铸造阶段</li>
                    <p class="alert alert-info">角色铸造分为五个阶段，每个阶段将花费不等的费用，具体如上表。</p>
                </ol>
            </div>

            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">角色铸造</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}