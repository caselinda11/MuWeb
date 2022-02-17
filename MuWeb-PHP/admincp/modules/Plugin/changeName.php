<?php
/**
 * 在线改名插件后台模块
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
                    <li class="breadcrumb-item active">在线改名</li>
                </ol>
            </div>
            <h4 class="page-title">在线改名</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_CHANGE_NAME_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->credit_type = $_POST['credit_type'];
        $xml->credit_price = $_POST['credit_price'];
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
    $changeName = new \Plugin\changeName();
    $moduleConfig = $changeName->loadConfig();
    $creditSystem = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header">
            在线改名
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td width="60%"><strong>模块状态</strong>  <span
                                class="text-muted">启用/禁用 此扩展。</td>
                        <td><?=enableDisableCheckboxes('active', $moduleConfig['active'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>日志开关</strong>  <span
                                class="text-muted">启用/禁用 改名日志是否写入数据库 <strong>日志表:[X_TEAM_CHANGE_NAME_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>货币类型</strong>  <span class="text-muted">选择一种使用该功能的费用货币类型</span></td>
                        <td>
                            <?=$creditSystem->buildSelectInput("credit_type", $moduleConfig['credit_type'], "form-control"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>改名价格</strong>  <span class="text-muted">每次使用改名功能的价格</td>
                        <td><input type="text" name="credit_price" value="<?=$moduleConfig['credit_price']?>" class="form-control"></td>
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



