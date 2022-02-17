<?php
/**
 * [角色创建]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">角色创建</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $CreateClass = new \Plugin\CreateClass();
    $tConfig = $CreateClass->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色才能使用该功能！');
    try{
        if(check_value($_POST['submit'])) {
            $CreateClass->setCreateNewClass($_SESSION['group'],$_SESSION['username'],$_POST['character_class'],$_POST['character_name']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">角色创建</div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8"></div>
                <div class="col-md-4">
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
                                $creditSystem->setIdentifier($_SESSION[$myCredits['config_user_col_id']]);
                                $configCredits = $creditSystem->getCredits($_SESSION['group']); ?>
                                <div class="form-control col-sm-12 text-right"><?=$myCredits['config_title'];?> <?=number_format($configCredits);?></div>
                            <?}
                        }
                    } catch(Exception $ex) {}
                    ?>
                </div>
            </div>
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="character_class" class="col-sm-4 col-form-label text-right">
                        创建角色
                    </label>
                    <div class="col-sm-5">
                        <select id="character_class" name="character_class" class="form-control">
                                <option value="48">魔剑士</option>
                                <option value="64">圣导士</option>
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="character_name" class="col-sm-4 col-form-label text-right">
                        新角色名
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="character_name" name="character_name" maxlength="10" required>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center text-danger"><div class="alert alert-warning" role="alert">角色名称不能包含特殊符号且长度为2~5个中文字符</div></div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('CreateClass')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                        确定
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>功能使用前提：</li>
                    <p class="alert alert-info">使用该功能前确保账号有足够位置储存新角色，且账号必须处于离线状态。</p>
                    <?if($tConfig['mg_active']){?>
                    <li>魔剑士创建条件：</li>
                        <p class="alert alert-info">创建魔剑士现有角色中必要有一个角色达到<kbd><?=$tConfig['mg_level_req']?></kbd>级，且收取<kbd><?=$tConfig['mg_credit_price']?></kbd><?=getPriceType($tConfig['mg_credit_type'])?> 手续费。</p>
                    <?}?>
                    <?if($tConfig['dl_active']){?>
                    <li>圣导士创建条件：</li>
                        <p class="alert alert-info">创建圣导士现有角色中必要有一个角色达到<kbd><?=$tConfig['dl_level_req']?></kbd>级，且收取<kbd><?=$tConfig['dl_credit_price']?></kbd><?=getPriceType($tConfig['dl_credit_type'])?> 手续费。</p>
                    <?}?>
                </ol>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">角色创建</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}