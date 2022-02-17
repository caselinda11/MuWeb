<?php
/**
 * [师徒系统]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">师徒系统</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $MyPlugin = new \Plugin\MentoringSystem();
    $tConfig = $MyPlugin->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    if(check_value($_POST['submit'])){
        try{
            if($_POST['submit'] == 'submit'){
                $MyPlugin->Bind($_POST['username']);
            }
            else{
                $MyPlugin->unBind();
            }
        } catch(Exception $exception) {
            message('error', $exception->getMessage());
        }
    }

    try{
        #解除绑定从师傅
        if(check_value($_GET['uid'])){
            $MyPlugin->unBindForMaster($_GET['uid']);
        }
        #同意收徒
        if(check_value($_GET['apply'])){
            $MyPlugin->applyBind($_GET['apply']);
        }
        #拒绝收徒
        if(check_value($_GET['unapply'])){
            $MyPlugin->applyUnBind($_GET['unapply']);
        }
    } catch(Exception $exception) {
        message('error', $exception->getMessage());
    }

    $check = $MyPlugin->checkUsernameBind($_SESSION['username']);
    if($check) {
        message('warning',"检测您当前已经绑定师傅，师傅账号[<strong>$check</strong>]，如需更换师傅请先解除与现有师傅关系。");
    }
    ?>
    <? $applyListData = $MyPlugin->getApplyList(); ?>
    <? $MentoringData = $MyPlugin->getMentoringInfo(); ?>
    <div class="card mb-3">
        <div class="card-header">徒弟信息</div>
        <?if(is_array($applyListData)){?>
        <table class="table table-bordered text-center">
            <tbody>
            <tr>
                <th>申请账号</th>
                <th>申请时间</th>
                <th>操作</th>
            </tr>
                <?foreach ($applyListData as $data){?>
                <tr>
                    <td width="40%"><?=$data['apprentice']?></td>
                    <td width="30%"><?=date("m-d H:i:s",strtotime($data['Date']))?></td>
                    <td>
                        <div class="btn-group">
                            <a href="<?=__BASE_URL__?>usercp/MentoringSystem?apply=<?=$data['apprentice']?>" class="btn btn-success">收Ta为徒</a>
                            <a href="<?=__BASE_URL__?>usercp/MentoringSystem?unapply=<?=$data['apprentice']?>" class="btn btn-danger">拒绝</a>
                        </div>
                    </td>
                </tr>
                <?}?>
            </tbody>
        </table>
        <?}?>
        <?if(is_array($MentoringData)){
            $character = new Character();
            ?>
            <?foreach ($MentoringData as $key => $data){?>
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        徒弟:[<?=$key?>]
                        <a href="<?=__BASE_URL__?>usercp/MentoringSystem?uid=<?=$key?>" class="more badge badge-danger">逐出师门</a>
                    </div>
                    <table class="table text-center">
                        <tbody>
                        <tr>
                            <?if(is_array($data)){?>
                                <?foreach ($data as $datum){
                                    $characterIMG = $character->GenerateCharacterClassAvatar($datum['Class'],1,1,30,'rounded-circle');
                                    ?>
                                    <td>
                                        <div class="character-img"><?=$characterIMG?></div>
                                        <div class="character-name"><?=playerProfile(getServerCodeForGroupID($_SESSION['group']),$datum['Name'],'');?></div>
                                    </td>
                                <?}?>
                            <?}else{?>
                                <td class="text-center"><strong>暂无角色信息</strong></td>
                            <?}?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            <?}?>
        <?}else{?>
            <p class="text-center"><strong>暂无徒弟信息</strong></p>
        <?}?>
    </div>

    <div class="card mt-3 mb-3">
        <div class="card-header">师徒系统</div>
        <div class="card-body">
            <div class="">
                <ol style="word-break: break-all;">
                    <li class="alert alert-info">即把自己的账号绑定指定师傅账号，师傅登陆网站可看到自己的角色信息，请谨慎操作。</li>
                    <li class="alert alert-info">每个账号可以绑定一个师傅，每个师傅最多可绑定[<strong><?=$tConfig['frequency']?></strong>]个徒弟。</li>
                    <li class="alert alert-info">绑定师傅账号需先与师傅联系好，自行避免发生解绑费用产生。</li>
                    <li class="alert alert-info">更换绑定关系请先解除师徒关系，并且解除关系将扣除[<strong><?=$tConfig['credit_price']?></strong>]<?=getPriceType($tConfig['credit_type'])?>作为手续费。</li>
                </ol>
            </div>
            <form class="form-horizontal mb-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="username" class="col-sm-4 col-form-label text-right">
                        师傅账号
                    </label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="username" name="username" placeholder="请输入师傅账号">
                    </div>
                    <div class="col-sm-4"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('MentoringSystem')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3">
                        绑定师傅
                    </button>
                </div>
                <div class="form-group row justify-content-md-center">
                    <button type="submit" name="submit" value="unBind" class="btn btn-danger col-sm-3 mb-4">
                        解除绑定
                    </button>
                </div>
            </form>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">师徒系统</cite>
            </footer>
        </div>
    </div>

<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}