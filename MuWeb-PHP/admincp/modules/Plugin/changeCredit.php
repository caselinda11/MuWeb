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
                    <li class="breadcrumb-item active">货币转换</li>
                </ol>
            </div>
            <h4 class="page-title">货币转换</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_CHANGE_CREDIT_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);
        if(!strstr($_POST['price_1'],':')) throw new Exception("货币[1]比例中必须含有半角(:)冒号");
        if(!strstr($_POST['price_2'],':')) throw new Exception("货币[2]比例中必须含有半角(:)冒号");
        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->orientation = $_POST['orientation'];

        $xml->name_1 = $_POST['name_1'];
        $xml->table_1 = $_POST['table_1'];
        $xml->table_column_1 = $_POST['table_column_1'];
        $xml->table_id_1 = $_POST['table_id_1'];
        $xml->price_1 = $_POST['price_1'];

        $xml->name_2 = $_POST['name_2'];
        $xml->table_2 = $_POST['table_2'];
        $xml->table_column_2 = $_POST['table_column_2'];
        $xml->table_id_2 = $_POST['table_id_2'];
        $xml->price_2 = $_POST['price_2'];

        $xml->min_price = $_POST['min_price'];
        $xml->max_price = $_POST['max_price'];
        $xml->credit_price = $_POST['credit_price'];
        $xml->credit_type = $_POST['credit_type'];
        $xml->check_online = $_POST['check_online'];

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
    $changeCredit = new \Plugin\changeCredit();
    $moduleConfig = $changeCredit->loadConfig();
    $creditSystem = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header">
            货币转换
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
                        <td><strong>日志开关</strong>  <span class="text-muted">启用/禁用 货币转换日志是否写入数据库 <strong>日志表:[X_TEAM_CHANGE_CREDIT_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>检查在线</strong>  <span class="text-muted">启用/禁用 启用则需要账号离线状态，禁用则不需要。<span class="text-danger">[安全考虑,建议启用]</span></td>
                        <td><?=enableDisableCheckboxes('check_online', $moduleConfig['check_online'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>货币模式</strong>  <span class="text-muted">启用/禁用 启用是否支持双向互转，如果禁用则只支持货币[1]换货币[2]。</td>
                        <td><?=enableDisableCheckboxes('orientation', $moduleConfig['orientation'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>货币[1]名称</strong>  <span class="text-muted">货币[1]的名称，仅做描述作用</td>
                        <td><input type="text" name="name_1" value="<?=$moduleConfig['name_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[1]表名</strong>  <span class="text-muted">货币[1]所在的数据库表名</td>
                        <td><input type="text" name="table_1" value="<?=$moduleConfig['table_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[1]列名</strong>  <span class="text-muted">货币[1]所在的数据库表名</td>
                        <td><input type="text" name="table_column_1" value="<?=$moduleConfig['table_column_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[1]识别符</strong>  <span class="text-muted">货币[1]所在的数据库的识别符，必须是账号识别符。</td>
                        <td><input type="text" name="table_id_1" value="<?=$moduleConfig['table_id_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[1]比例</strong>  <span class="text-muted">兑换比例，货币[1]:货币[2]，格式：例如：10:1</td>
                        <td><input type="text" name="price_1" value="<?=$moduleConfig['price_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[2]名称</strong>  <span class="text-muted">货币[2]的名称，仅做描述作用</td>
                        <td><input type="text" name="name_2" value="<?=$moduleConfig['name_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[2]表名</strong>  <span class="text-muted">货币[2]所在的数据库表名</td>
                        <td><input type="text" name="table_2" value="<?=$moduleConfig['table_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[2]列名</strong>  <span class="text-muted">货币[2]所在的数据库表名</td>
                        <td><input type="text" name="table_column_2" value="<?=$moduleConfig['table_column_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[2]识别符</strong>  <span class="text-muted">货币[2]所在的数据库的识别符，必须是账号识别符。</td>
                        <td><input type="text" name="table_id_2" value="<?=$moduleConfig['table_id_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>货币[2]比例</strong>  <span class="text-muted">兑换比例，货币[2]:货币[1]，格式：例如：1:10 <span class="text-danger">[开启货币模式才管用]</span></td>
                        <td><input type="text" name="price_2" value="<?=$moduleConfig['price_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>最低限额</strong>  <span class="text-muted">使用此功能的最低额度限制，低于此限制无法使用该功能。</td>
                        <td><input type="text" name="min_price" value="<?=$moduleConfig['min_price']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>最高限额</strong>  <span class="text-muted">使用此功能的最高额度限制，高于此限制无法使用该功能。</td>
                        <td><input type="text" name="max_price" value="<?=$moduleConfig['max_price']?>" class="form-control"></td>
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



