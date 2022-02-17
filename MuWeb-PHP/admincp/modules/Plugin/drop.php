<?php
/**
 * 角色铸造后台配置文件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="<?=admincp_base()?>">官方主页</a>
                    </li>
                    <li class="breadcrumb-item active">插件系统</li>
                    <li class="breadcrumb-item active">溯源系统</li>
                </ol>
            </div>
            <h4 class="page-title">溯源系统</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_DROP_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->redis_ip = $_POST['redis_ip'];
        $xml->redis_pass = $_POST['redis_pass'];
        $xml->redis_port = $_POST['redis_port'];
        $xml->log_path = $_POST['log_path'];

        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '保存时发生错误！');
        }
    }
    if (check_value($_POST['submit'])) {
        submit();
    }

}catch (Exception $exception){
    message('error',$exception->getMessage());
}
try {
    $drop = new \Plugin\drop();
    $moduleConfig = $drop->loadConfig();
    $creditSystem = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header">
            角色铸造
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td width="60%"><strong>模块状态</strong>  <span class="text-muted">启用/禁用 此扩展。</td>
                        <td><?=enableDisableCheckboxes('active', $moduleConfig['active'], '启用', '禁用'); ?></td>
                    </tr>

                    <tr>
                        <td><strong>[redis]IP</strong>  <span class="text-muted">redis所在服务器的IP，本地则写[127.0.0.1]</td>
                        <td><input type="text" name="redis_ip" value="<?=$moduleConfig['redis_ip']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[redis]密码</strong>  <span class="text-muted">默认为空，如果配置是在redis中配置密码。</td>
                        <td><input type="text" name="redis_pass" value="<?=$moduleConfig['redis_pass'] ? $moduleConfig['redis_pass'] : '' ?>" class="form-control"></td>
                    </tr>

                    <tr>
                        <td><strong>[redis]端口</strong>  <span class="text-muted">默认为[6379]，如需修改是在redis中配置。</td>
                        <td><input type="text" name="redis_port" value="<?=$moduleConfig['redis_port']?>" class="form-control"></td>
                    </tr>

                    <tr>
                        <td><strong>[LOG]路径</strong>  <span class="text-muted">填写[GameServer]的日志绝对路径</td>
                        <td><input type="text" name="log_path" value="<?=$moduleConfig['log_path']?>" class="form-control"></td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <div style="text-align:center">
                                <button type="submit" name="submit" value="submit" class="btn btn-success col-md-2">保存
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
} ?>



