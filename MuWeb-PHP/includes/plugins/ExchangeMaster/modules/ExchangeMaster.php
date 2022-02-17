<?php
/**
 * 大师铸造模块页面
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
            <li class="breadcrumb-item active" aria-current="page">大师铸造</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $ExchangeMaster = new \Plugin\ExchangeMaster();
    $tConfig = $ExchangeMaster->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色才能使用该功能！');
    try{
        if(check_value($_POST['submit'])) {
            $ExchangeMaster->setItemsExchange($_SESSION['group'],$_SESSION['username'],$_POST['character_name'],$_POST['number']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">大师铸造</div>
        <div class="card-body mb-3">
            <div class="">
                <ol style="word-break: break-all;">
                    <li class="alert alert-info">大师等级必须达到<?=$tConfig['min_master']?>级，账号必须处于离线状态且已是大师级别。</li>
                    <li class="alert alert-info">每<?=$tConfig['item_number']?>个<?=$tConfig['item_name']?>可以兑换<?=$tConfig['master_level']?>级大师，<?=$tConfig['master_point']?>点大师点，<?=$tConfig['point']?>点普通属性点，兑换后大师最高<?=$tConfig['max_master']?>级。</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-5">
                </div>
                <div class="col-md-4">
                    <?try {?>
                        当前仓库:
                            <div class="form-control col-sm-12 text-right"><?=$tConfig['item_name']?> <?=$ExchangeMaster->count?>个</div>
                        <?
                    } catch(Exception $ex) {
                        message('error', $ex->getMessage());
                    } ?>
                </div>
            </div>
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="character" class="col-sm-4 col-form-label text-right">
                        选择角色
                    </label>
                    <div class="col-sm-4">
                        <select id="character" name="character_name" class="form-control">
                            <?foreach($AccountCharacters as $code=>$thisCharacter) {?>
                                <?$characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                                if($characterData['CtlCode']) continue;?>
                                <option value="<?=$thisCharacter?>"><?=$thisCharacter?> 【大师Lv：<?=$characterData['mLevel']?>】</option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="number" class="col-sm-4 col-form-label text-right">
                        兑换数量
                    </label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control" id="number" name="number">
                    </div>
                    <div class="col-sm-4 col-form-label"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('ExchangeMaster')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3">
                        确定
                    </button>
                </div>
            </form>

            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">铸造功能</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}