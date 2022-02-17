<?php
/**
 * [身份证修改]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">修改身份证</li>
        </ol>
    </nav>
    <?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $changeIDCard = new \Plugin\changeIDCard();
    try{
        if(check_value($_POST['submit'])) {
            $changeIDCard->setChangeIDCard($_SESSION['group'],$_SESSION['username'],$_POST['old_id'],$_POST['new_id']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">修改身份证</div>
        <div class="card-body">
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="old_id" class="col-sm-4 col-form-label text-right">
                        旧身份证
                    </label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="old_id" name="old_id" minlength="7" maxlength="7" required>
                    </div>
                    <div class="col-sm-4 form-text text-muted"><small>*后七位</small></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="new_id" class="col-sm-4 col-form-label text-right">
                        新身份证
                    </label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="new_id" name="new_id" minlength="15" maxlength="18" required>
                    </div>
                    <div class="col-sm-4 form-text text-muted"><small>*必须真实有效</small></div>
                </div>
                <div class="form-group row justify-content-md-center"><small class="text-danger">二级密码将使用新的身份证号码后7位。</small></div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('changeIDCard')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3">
                        确定
                    </button>
                </div>
            </form>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>修改申明</li>
                    <p class="alert alert-info">应壮游官方合作区要求，身份证信息必须为真实修改身份证号码。</p>
                    <li>功能说明</li>
                    <p class="alert alert-info">请认真核对填写您的身份证信息，不可有误，后7位为二级密码。</p>
               </ol>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">修改身份证</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}