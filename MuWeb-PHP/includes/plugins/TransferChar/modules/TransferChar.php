<?php
/**
 * [CharTransfer]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">角色转移</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $CharTransfer = new \Plugin\TransferChar();
    $tConfig = $CharTransfer->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色才能使用该功能！');
    try{
        if(check_value($_POST['submit'])) {
            $CharTransfer->setCharacterTransfer($_SESSION['group'],$_SESSION['username'],$_POST['character_name'],$_POST['user']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">角色转移</div>
        <div class="card-body">
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="character" class="col-sm-4 col-form-label text-right">
                        选择角色
                    </label>
                    <div class="col-sm-5">
                        <select id="character" name="character_name" class="form-control">
                            <?foreach($AccountCharacters as $code=>$thisCharacter) {?>
                                <?$characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                                if($characterData['CtlCode']) continue;
                                ?>
                                <option value="<?=$thisCharacter?>"><?=$thisCharacter?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
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
                        转移费用
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="price" disabled value="<?=$tConfig['credit_price']?> <?=getPriceType($tConfig['credit_type'])?>">
                    </div>
                    <div class="col-sm-3 col-form-label"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('CharTransfer')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                        确定
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>转移要求</li>
                    <p class="alert alert-info">转移角色双方账号必须为离线状态，且角色属于正常状态才可使用。</p>
                    <li>转移要求</li>
                    <p class="alert alert-info">角色等级必须是[<?=$tConfig['min_level']?>~<?=$tConfig['max_level']?>]之间才可使用该功能。</p>
                    <li>转移须知</li>
                    <p class="alert alert-info">请认真输入账号核对谨慎操作，一旦转移成功将无法撤回操作。</p>
                </ol>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">角色转移</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}