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
                    <li class="breadcrumb-item active">大师铸造</li>
                </ol>
            </div>
            <h4 class="page-title">大师铸造</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_Exchange_Master_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->min_master = $_POST['min_master'];
        $xml->max_master = $_POST['max_master'];
        $xml->items = $_POST['items'];
        $xml->item_number = $_POST['item_number'];
        $xml->item_name = $_POST['item_name'];
        $xml->master_level = $_POST['master_level'];
        $xml->master_point = $_POST['master_point'];
        $xml->point = $_POST['point'];
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
    $ExchangeMaster = new \Plugin\ExchangeMaster();
    $moduleConfig = $ExchangeMaster->loadConfig();

    ?>
    <div class="card">
        <div class="card-header">
            大师铸造
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
                        <td><strong>日志开关</strong>  <span class="text-muted">启用/禁用 铸造日志是否写入数据库 <strong>日志表:[X_TEAM_EXCHANGE_MASTER_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>最低大师等级</strong>  <span class="text-muted">使用该功能大师等级最低要求。</td>
                        <td><input type="text" name="min_master" value="<?=$moduleConfig['min_master']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>最高大师等级</strong>  <span class="text-muted">使用该功能大师等级最高限制。</td>
                        <td><input type="text" name="max_master" value="<?=$moduleConfig['max_master']?>" class="form-control"></td>
                    </tr>

                    <tr>
                        <td><strong>物品要求</strong>  <span class="text-muted">物品的总编号，总编号=大编码*512+小编码。</td>
                        <td><input type="text" name="items" value="<?=$moduleConfig['items']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>物品数量</strong>  <span class="text-muted">每个单位所需要的物品数量。</td>
                        <td><input type="text" name="item_number" value="<?=$moduleConfig['item_number']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>物品名称</strong>  <span class="text-muted">输入物品的名称用于前端显示作用。</td>
                        <td><input type="text" name="item_name" value="<?=$moduleConfig['item_name']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>奖励等级</strong>  <span class="text-muted">每个物品兑换大师的等级。</td>
                        <td><input type="text" name="master_level" value="<?=$moduleConfig['master_level']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>奖励大师</strong>  <span class="text-muted">每个物品兑换大师的点数。</td>
                        <td><input type="text" name="master_point" value="<?=$moduleConfig['master_point']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>奖励点数</strong>  <span class="text-muted">每个物品兑换普通属性点数。</td>
                        <td><input type="text" name="point" value="<?=$moduleConfig['point']?>" class="form-control"></td>
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



