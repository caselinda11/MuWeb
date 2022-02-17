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
                    <li class="breadcrumb-item active">角色铸造</li>
                </ol>
            </div>
            <h4 class="page-title">角色铸造</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_ITEM_STATUS_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->min_level = $_POST['min_level'];
        $xml->points_1 = $_POST['points_1'];
        $xml->points_2 = $_POST['points_2'];
        $xml->points_3 = $_POST['points_3'];
        $xml->points_4 = $_POST['points_4'];
        $xml->points_5 = $_POST['points_5'];
        $xml->item_1 = $_POST['item_1'];
        $xml->item_2 = $_POST['item_2'];
        $xml->item_3 = $_POST['item_3'];
        $xml->item_4 = $_POST['item_4'];
        $xml->item_5 = $_POST['item_5'];
        $xml->item_name_1 = $_POST['item_name_1'];
        $xml->item_name_2 = $_POST['item_name_2'];
        $xml->item_name_3 = $_POST['item_name_3'];
        $xml->item_name_4 = $_POST['item_name_4'];
        $xml->item_name_5 = $_POST['item_name_5'];
        $xml->item_number_1 = $_POST['item_number_1'];
        $xml->item_number_2 = $_POST['item_number_2'];
        $xml->item_number_3 = $_POST['item_number_3'];
        $xml->item_number_4 = $_POST['item_number_4'];
        $xml->item_number_5 = $_POST['item_number_5'];
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
    $ItemStatus = new \Plugin\ItemStatus();
    $moduleConfig = $ItemStatus->loadConfig();
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
                        <td><strong>日志开关</strong>  <span class="text-muted">启用/禁用 铸造日志是否写入数据库 <strong>日志表:[X_TEAM_ItemStatus_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>等级要求</strong>  <span class="text-muted">使用角色铸造的最低等级要求</td>
                        <td><input type="text" name="min_level" value="<?=$moduleConfig['min_level']?>" class="form-control"></td>
                    </tr>

                    <tr class="bg-warning">
                        <td><strong>[1] - 铸造点数</strong>  <span class="text-muted">第一阶段最高可以兑换的点数</td>
                        <td><input type="text" name="points_1" value="<?=$moduleConfig['points_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[1] - 物品数量</strong>  <span class="text-muted">第一阶段每点所需要的物品数量</td>
                        <td><input type="text" name="item_number_1" value="<?=$moduleConfig['item_number_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[1] - 所需物品</strong>  <span class="text-muted">输入物品总编号(总编号=大编码*512+小编吗)</span></td>
                        <td><input type="text" name="item_1" value="<?=$moduleConfig['item_1']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[1] - 物品名称</strong>  <span class="text-muted">输入物品名称(前台显示作用)</span></td>
                        <td><input type="text" name="item_name_1" value="<?=$moduleConfig['item_name_1']?>" class="form-control"></td>
                    </tr>

                    <tr class="bg-warning">
                        <td><strong>[2] - 铸造点数</strong>  <span class="text-muted">第二阶段最高可以兑换的点数</td>
                        <td><input type="text" name="points_2" value="<?=$moduleConfig['points_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[2] - 物品数量</strong>  <span class="text-muted">第二阶段每点所需要的物品数量</td>
                        <td><input type="text" name="item_number_2" value="<?=$moduleConfig['item_number_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[2] - 所需物品</strong>  <span class="text-muted">输入物品总编号(总编号=大编码*512+小编吗)</span></td>
                        <td><input type="text" name="item_2" value="<?=$moduleConfig['item_2']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[2] - 物品名称</strong>  <span class="text-muted">输入物品名称(前台显示作用)</span></td>
                        <td><input type="text" name="item_name_2" value="<?=$moduleConfig['item_name_2']?>" class="form-control"></td>
                    </tr>

                    <tr class="bg-warning">
                        <td><strong>[3] - 铸造点数</strong>  <span class="text-muted">第三阶段最高可以兑换的点数</td>
                        <td><input type="text" name="points_3" value="<?=$moduleConfig['points_3']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[3] - 物品数量</strong>  <span class="text-muted">第三阶段每点所需要的物品数量</td>
                        <td><input type="text" name="item_number_3" value="<?=$moduleConfig['item_number_3']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[3] - 所需物品</strong>  <span class="text-muted">输入物品总编号(总编号=大编码*512+小编吗)</span></td>
                        <td><input type="text" name="item_3" value="<?=$moduleConfig['item_3']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[3] - 物品名称</strong>  <span class="text-muted">输入物品名称(前台显示作用)</span></td>
                        <td><input type="text" name="item_name_3" value="<?=$moduleConfig['item_name_3']?>" class="form-control"></td>
                    </tr>

                    <tr class="bg-warning">
                        <td><strong>[4] - 铸造点数</strong>  <span class="text-muted">第四阶段最高可以兑换的点数</td>
                        <td><input type="text" name="points_4" value="<?=$moduleConfig['points_4']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[4] - 物品数量</strong>  <span class="text-muted">第四阶段每点所需要的物品数量</td>
                        <td><input type="text" name="item_number_4" value="<?=$moduleConfig['item_number_4']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[4] - 所需物品</strong>  <span class="text-muted">输入物品总编号(总编号=大编码*512+小编吗)</span></td>
                        <td><input type="text" name="item_4" value="<?=$moduleConfig['item_4']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[4] - 物品名称</strong>  <span class="text-muted">输入物品名称(前台显示作用)</span></td>
                        <td><input type="text" name="item_name_4" value="<?=$moduleConfig['item_name_4']?>" class="form-control"></td>
                    </tr>

                    <tr class="bg-warning">
                        <td><strong>[5] - 铸造点数</strong>  <span class="text-muted">第五阶段最高可以兑换的点数</td>
                        <td><input type="text" name="points_5" value="<?=$moduleConfig['points_5']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[5] - 物品数量</strong>  <span class="text-muted">第五阶段每点所需要的物品数量</td>
                        <td><input type="text" name="item_number_5" value="<?=$moduleConfig['item_number_5']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[5] - 所需物品</strong>  <span class="text-muted">输入物品总编号(总编号=大编码*512+小编吗)</span></td>
                        <td><input type="text" name="item_5" value="<?=$moduleConfig['item_5']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><strong>[5] - 物品名称</strong>  <span class="text-muted">输入物品名称(前台显示作用)</span></td>
                        <td><input type="text" name="item_name_5" value="<?=$moduleConfig['item_name_5']?>" class="form-control"></td>
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



