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
                    <li class="breadcrumb-item active">角色创建</li>
                </ol>
            </div>
            <h4 class="page-title">角色创建</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_CREATE_CLASS_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->mg_active = $_POST['mg_active'];
        $xml->mg_level_req = $_POST['mg_level_req'];
        $xml->mg_credit_price = $_POST['mg_credit_price'];
        $xml->mg_credit_type = $_POST['mg_credit_type'];
        $xml->dl_active = $_POST['dl_active'];
        $xml->dl_level_req = $_POST['dl_level_req'];
        $xml->dl_credit_price = $_POST['dl_credit_price'];
        $xml->dl_credit_type = $_POST['dl_credit_type'];

        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '保存时发生错误！');
        }
    }
    if (check_value($_POST['submit'])) {
//        switch ($_POST['submit']){
//            case "submit_add":
//                try{
//
//                    message('success', '角色类型添加成功！');
//                } catch(Exception $ex) {
//                    message('error', $ex->getMessage());
//                }
//                break;
//            case "submit_edit":
//                try{
//                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
//
//                    $update = $web->query("DELETE FROM [X_TEAM_MEMBER_REWARD] WHERE [ID] = ?",[$_POST['ID']]);
//                    if(!$update) throw new  Exception("操作失败，请确保数据的正确性！");
//                    message('success', '角色类型编辑成功！');
//                } catch(Exception $ex) {
//                    message('error', $ex->getMessage());
//                }
//                break;
//            case "delete":
//                try{
//                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
//                    $delete = $web->query("DELETE FROM [X_TEAM_MEMBER_REWARD] WHERE [ID] = ?",[$_POST['ID']]);
//                    if(!$delete) throw new  Exception("操作失败，请确保数据的正确性！");
//                    message('success', '角色类型删除成功！');
//                } catch(Exception $ex) {
//                    message('error', $ex->getMessage());
//                }
//                break;
//            case "submit_save":
                submit();
//                break;
//            default:
//                message('error', '禁止非法提交,请确保数据的正确性!');
//                break;
//        }

    }

}catch (Exception $exception){
    message('error',$exception->getMessage());
}
try {
    $CreateClass = new \Plugin\CreateClass();
    $moduleConfig = $CreateClass->loadConfig();
    $creditSystem = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header">
            角色创建
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
                                class="text-muted">启用/禁用 日志是否写入数据库 <strong>日志表:[X_TEAM_CREATE_CLASS_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr style="background: rgba(0,255,0,0.2)">
                        <td><strong>魔剑创建</strong>  <span
                                class="text-muted">启用/禁用 魔剑士创建开关</td>
                        <td><?=enableDisableCheckboxes('mg_active', $moduleConfig['mg_active'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr style="background: rgba(0,255,0,0.2)">
                        <td><strong>等级要求</strong>  <span class="text-muted">验证现有角色中是否已有角色达到该等级水平.(等级+大师)</td>
                        <td><input type="text" name="mg_level_req" value="<?=$moduleConfig['mg_level_req']?>" class="form-control"></td>
                    </tr>
                    <tr style="background: rgba(0,255,0,0.2)">
                        <td><strong>费用金额</strong>  <span class="text-muted">每次使用该功能的金额，最大值不能过2亿</td>
                        <td><input type="text" name="mg_credit_price" value="<?=$moduleConfig['mg_credit_price']?>" class="form-control"></td>
                    </tr>
                    <tr style="background: rgba(0,255,0,0.2)">
                        <td><strong>货币类型</strong>  <span class="text-muted">选择一种使用该功能的费用货币类型。</span></td>
                        <td>
                            <?=$creditSystem->buildSelectInput("mg_credit_type", $moduleConfig['mg_credit_type'], "form-control"); ?>
                        </td>
                    </tr>

                    <tr style="background: rgba(0,255,255,0.2)">
                        <td><strong>圣导创建</strong>  <span
                                class="text-muted">启用/禁用 圣导士创建开关</td>
                        <td><?=enableDisableCheckboxes('dl_active', $moduleConfig['dl_active'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr style="background: rgba(0,255,255,0.2)">
                        <td><strong>等级要求</strong>  <span class="text-muted">验证现有角色中是否已有角色达到该等级水平.(等级+大师)</td>
                        <td><input type="text" name="dl_level_req" value="<?=$moduleConfig['dl_level_req']?>" class="form-control"></td>
                    </tr>
                    <tr style="background: rgba(0,255,255,0.2)">
                        <td><strong>费用金额</strong>  <span class="text-muted">每次使用该功能的金额，最大值不能过2亿</td>
                        <td><input type="text" name="dl_credit_price" value="<?=$moduleConfig['dl_credit_price']?>" class="form-control"></td>
                    </tr>
                    <tr style="background: rgba(0,255,255,0.2)">
                        <td><strong>货币类型</strong>  <span class="text-muted">选择一种使用该功能的费用货币类型。</span></td>
                        <td>
                            <?=$creditSystem->buildSelectInput("dl_credit_type", $moduleConfig['dl_credit_type'], "form-control"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="text-align:center">
                                <button type="submit" name="submit" value="submit_save" class="btn btn-success col-md-2">保存
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



