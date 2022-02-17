<?php
/**
 * 在线改名模块页面
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
            <li class="breadcrumb-item active" aria-current="page">在线改名</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $changeName = new \Plugin\changeName();
    $tConfig = $changeName->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色才能使用该功能！');
    try{
        if(check_value($_POST['submit'])) {
            $changeName->setNewCharName($_SESSION['group'],$_SESSION['username'],$_POST['character_name'],$_POST['new_name']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">在线改名</div>
        <div class="card-body mt-3 mb-3">
            <form class="form-horizontal mt-3" action="" method="post">
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
                    <label for="new_name" class="col-sm-4 col-form-label text-right">
                        新角色名
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="new_name" name="new_name" maxlength="10" required>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="user" class="col-sm-4 col-form-label text-right">
                        改名费用
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="user" disabled value="<?=$tConfig['credit_price']?> <?=$changeName->getPriceType($tConfig['credit_type'])?>">
                    </div>
                    <div class="col-sm-3 col-form-label"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('changeName')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                        确定
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>改名要求</li>
                    <p class="alert alert-info">角色必须是离线状态且无战盟状态，己加入战盟请先退出战盟或解散战盟。</p>
                    <li>改名限制</li>
                    <p class="alert alert-info">新角色名仅限是中文、字母、数字、以及部分特殊符号可用。</p>
                    <li>改名费用</li>
                    <p class="alert alert-info">每次使用改名功能将收取<kbd><?=$tConfig['credit_price']?><?=$changeName->getPriceType($tConfig['credit_type'])?></kbd>作为手续费，请谨慎操作。</p>
                </ol>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">改名功能</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}