<?php
/**
 * MemberReward插件后台模块
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
                    <li class="breadcrumb-item active">会员领奖</li>
                </ol>
            </div>
            <h4 class="page-title">会员领奖</h4>
        </div>
    </div>
</div>
<?php
try {
    $web = Connection::Database("Web");
    function submit()
    {
        $xmlPath = __PATH_MEMBER_REWARD_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];


        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '保存时发生错误！');
        }
    }
    if (check_value($_POST['submit'])) {
        switch ($_POST['submit']){
            case "submit_add":
                try{
                    if(!check_value($_POST['reward_name'])) throw new  Exception("添加失败，请先填写奖励名称。");
                    if(!check_value($_POST['requirement_vip'])) throw new  Exception("添加失败，请先填写需求等级。");
                    if(!check_value($_POST['reward_code'])) throw new  Exception("添加失败，请先填写物品代码。");
                    if(!Validator::Items($_POST['reward_code'])) throw new  Exception("请正确输入物品代码！");
                    if(!check_value($_POST['status'])) throw new  Exception("添加失败，请确保数据的正确性！");
                    if(!check_value($_POST['reward_Description'])) throw new  Exception("添加失败，请先填写物品描述。");
                    $query = [
                        $_POST['reward_name'],
                        $_POST['requirement_vip'],
                        $_POST['reward_code'],
                        $_POST['reward_Description'],
                        $_POST['status'],
                    ];
                    $sql = $web->query("INSERT INTO [X_TEAM_MEMBER_REWARD] ([reward_name],[requirement_vip],[reward_code],[reward_Description],[status]) VALUES (?, ?, ?, ?, ?)",$query);
                    if(!$sql) throw new  Exception("添加失败，请确保数据的正确性！");
                    message('success', '奖励配置添加成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_edit":
                try{
                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    if(!check_value($_POST['reward_name'])) throw new  Exception("编辑失败，奖励名称不能为空。");
                    if(!check_value($_POST['requirement_vip'])) throw new  Exception("编辑失败，请先填写需求等级。");
                    if(!check_value($_POST['reward_code'])) throw new  Exception("编辑失败，请先填写物品代码。");
                    if(!Validator::Items($_POST['reward_code'])) throw new  Exception("请正确输入物品代码！");
                    if(!check_value($_POST['status'])) throw new  Exception("编辑失败，请确保数据的正确性！");
                    if(!check_value($_POST['reward_Description'])) throw new  Exception("编辑失败，请先填写物品描述。");
                    $query = [
                        'reward_name' => $_POST['reward_name'],
                        'requirement_vip' => $_POST['requirement_vip'],
                        'reward_code' => $_POST['reward_code'],
                        'reward_Description' => $_POST['reward_Description'],
                        'status' => $_POST['status'],
                        'ID' => $_POST['ID']
                    ];
                    $sql = $web->query("UPDATE [X_TEAM_MEMBER_REWARD] SET [reward_name] = :reward_name,[requirement_vip] = :requirement_vip,[reward_code] = :reward_code,[reward_Description] = :reward_Description,[status] = :status WHERE [ID] = :ID",$query);
                    if(!$sql) throw new  Exception("编辑失败，请确保数据的正确性！");
                    message('success', '奖励配置编辑成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_delete":
                try{
                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    $delete = $web->query("DELETE FROM [X_TEAM_MEMBER_REWARD] WHERE [ID] = ?",[$_POST['ID']]);
                    if(!$delete) throw new  Exception("操作失败，请确保数据的正确性！");
                    message('success', '奖励配置删除成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_save":
                submit();
                break;
            default:
                message('error', '禁止非法提交,请确保数据的正确性!');
                break;
        }

    }

}catch (Exception $exception){
    message('error',$exception->getMessage());
}
try {
    $MemberReward = new \Plugin\MemberReward();
    $moduleConfig = $MemberReward->loadConfig();
    ?>
    <div class="card">
        <div class="card-header">
            领取物品
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td width="60%"><strong>模块状态</strong>  <span
                                class="text-muted">启用/禁用 此扩展。 <strong>日志表:[X_TEAM_MEMBER_REWARD_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('active', $moduleConfig['active'], '启用', '禁用'); ?></td>
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

    <div class="card">
        <div class="card-header">奖励列表</div>
        <div>
            <table class="table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th>奖励名称</th>
                    <th>VIP等级要求</th>
                    <th>奖励物品</th>
                    <th>状态</th>
                    <th>奖励说明</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?$list = $MemberReward->getMemberRewardList()?>
                <?IF(is_array($list)){?>
                    <?foreach ($list as $data){?>
                <form action="" method="post">
                    <tr>
                        <td><input type="text" class="form-control" name="reward_name" value="<?=$data['reward_name']?>" /></td>
                        <td><input type="number" class="form-control" name="requirement_vip" value="<?=$data['requirement_vip']?>"/></td>
                        <td><input type="text" class="form-control" name="reward_code" maxlength="64" value="<?=$data['reward_code']?>"/></td>
                        <td>
                            <select class="form-control" name="status">
                                <option value="1" <?=selected($data['status'],1)?>>启用</option>
                                <option value="0" <?=selected($data['status'],0)?>>禁用</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="reward_Description" value="<?=$data['reward_Description']?>"/></td>
                        <td>
                            <div class="btn-group col-md-12">
                                <input type="hidden" class="form-control" name="ID" value="<?=$data['ID']?>" />
                                <button type="submit" name="submit" value="submit_edit" class="btn btn-primary">编辑</button>
                                <button type="submit" name="submit" value="submit_delete" class="btn btn-danger">删除</button>
                            </div>
                        </td>
                    </tr>
                </form>
                <?}?>
                <?}else{?>
                    <tr><th  colspan="6" class="text-danger">暂无月卡配置</th></tr>
                <?}?>
                <tr><th colspan="6"><strong>添加新的</strong></th></tr>
                <form action="" method="post">
                    <tr>
                        <td><input type="text" class="form-control" name="reward_name"/></td>
                        <td><input type="number" class="form-control" name="requirement_vip"/></td>
                        <td><input type="text" class="form-control" maxlength="64" name="reward_code"/></td>
                        <td>
                            <select class="form-control" name="status">
                                <option value="0">禁用</option>
                                <option value="1" selected>启用</option>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="reward_Description"/></td>
                        <td><button type="submit" name="submit" value="submit_add" class="btn btn-success col-md-12">添加</button></td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
    </div>

    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
} ?>



