<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try {
    if (!isLoggedIn()) redirect(1, 'login');
    if(!class_exists('Plugin\Market\warehouse')) throw new Exception('该插件已禁用!');
    ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">仓库重置</li>
        </ol>
    </nav>
    <?php
    try {
        $ware = new \Plugin\Market\warehouse($_SESSION['group']);
        if (check_value($_POST['submit'])){
            $ware->resetWarehouse($_SESSION['group'],$_SESSION['username'],$_POST['pwd']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mb-3">
        <div class="card-header">仓库重置</div>
        <div class="card-body">
            <?=message('warning','一旦重置仓库，则仓库中的所有物品将会清空无法恢复，请谨慎操作！','功能说明:')?>
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <?=message('warning','请输入您的密码以验证为您本人操作')?>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="pwd" class="col-sm-2 col-form-label text-right">游戏密码</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="pwd" name="pwd" required="" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('resetWarehouse')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-danger col-sm-4">
                        重置仓库
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
