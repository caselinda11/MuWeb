<?php
/**
 * [角色转移]插件后台模块
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
                    <li class="breadcrumb-item active">角色转移</li>
                </ol>
            </div>
            <h4 class="page-title">角色转移</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_CHAR_TRANSFER_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->credit_type = $_POST['credit_type'];
        $xml->credit_price = $_POST['credit_price'];
        $xml->min_level = $_POST['min_level'];
        $xml->max_level = $_POST['max_level'];
        $xml->transfer_cont = $_POST['transfer_cont'];

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
    $CharTransfer = new \Plugin\TransferChar();
    $moduleConfig = $CharTransfer->loadConfig();
    $creditSystem = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header">
            角色转移
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
                                class="text-muted">启用/禁用 日志是否写入数据库 <strong>日志表:[X_TEAM_CHAR_TRANSFER_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>货币类型</strong>  <span class="text-muted">选择一种使用该功能的费用货币类型。</span></td>
                        <td>
                            <?=$creditSystem->buildSelectInput("credit_type", $moduleConfig['credit_type'], "form-control"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>费用金额</strong>  <span class="text-muted">每次使用该功能的金额，最大值不能过2亿</td>
                        <td><input type="text" name="credit_price" value="<?=$moduleConfig['credit_price']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>最小等级</strong><br><span class="text-muted">限制转移的角色最低等级，必须小于最大等级</span></td>
                        <td><input class="form-control" type="number" name="min_level" value="<?= $moduleConfig['min_level'] ?>"/></td>
                    </tr>
                    <tr>
                        <td><strong>最大等级</strong><br><span class="text-muted">限制转移的角色最高等级，必须大于最小等级。[等级+大师]</span></td>
                        <td><input class="form-control" type="number" name="max_level" value="<?= $moduleConfig['max_level'] ?>"/></td>
                    </tr>
                    <tr>
                        <td><strong>转移次数</strong><br><span class="text-muted">限制转移的角色交易次数，<span class="text-danger">使用此功能，日志开关必须开启。</span></span></td>
                        <td><input class="form-control" type="number" name="transfer_cont" value="<?= $moduleConfig['transfer_cont'] ?>"/></td>
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



