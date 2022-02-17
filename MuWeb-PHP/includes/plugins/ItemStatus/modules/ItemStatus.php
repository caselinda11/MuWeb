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
    $WCoinStatus = new \Plugin\ItemStatus();
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
            <div class="col-md-12 mb-3">
                <div class="">
                    <ol style="word-break: break-all;">
                        <li class="alert alert-info">铸造的含义是用物品兑换相应的属性点，属性点终身享用。</li>
                        <li class="alert alert-info">角色必须达到<kbd><?=$tConfig['min_level']?></kbd>级，且角色处于离线状态才可使用该功能。</li>
                        <li class="alert alert-info">角色铸造分为五个阶段，每个阶段将花费不等的费用，具体如上表。</li>
                        <li class="alert alert-info">使用该功能前请确保您的仓库中有足够的物品数量。</li>
                    </ol>
                </div>
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
                            <td><?=$tConfig['item_name_'.$i]?><strong><?=$tConfig['item_number_'.$i]?></strong>个</td>
                        <?}?>
                    </tr>

                    <?
                    $dataChar = $WCoinStatus->getCharStatus($_SESSION['group'],$_SESSION['username']);
                    if(is_array($dataChar)){
                    ?>
                    <tr><th colspan="6">角色铸造信息</th></tr>
                    <?
                    foreach ($dataChar as $data){
                        ?>
                        <tr>
                            <td><?=$data['Name']?></td>
                            <?for($i=1;$i<=5;$i++){?>
                                <td><?=$data['Point'.$i]?></td>
                            <?}?>
                        </tr>
                    <?} ?>
                    <?} ?>
                </table>
            </div>

            <form class="form-horizontal mb-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="character" class="col-sm-4 col-form-label text-right">
                        选择角色
                    </label>
                    <div class="col-sm-4">
                        <select id="character" name="character_name" class="form-control">
                            <?foreach($AccountCharacters as $code=>$thisCharacter) {?>
                                <?$characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                                if($characterData['CtlCode']) continue;?>
                                <option value="<?=$thisCharacter?>"><?=$thisCharacter?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="point" class="col-sm-4 col-form-label text-right">
                        铸造点数
                    </label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="point" name="point" maxlength="10" required>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('ItemStatus')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3 mb-4">
                        确定
                    </button>
                </div>
            </form>

            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">角色铸造</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}