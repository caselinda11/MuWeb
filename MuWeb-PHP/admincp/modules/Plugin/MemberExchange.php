<?php
/**
 * MemberEx插件后台模块
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
                    <li class="breadcrumb-item active">兑换会员</li>
                </ol>
            </div>
            <h4 class="page-title">兑换会员</h4>
        </div>
    </div>
</div>
<?php
try {
    $web = Connection::Database("Web");
    function submit()
    {
        $xmlPath = __PATH_MEMBER_EXCHANGE_ROOT__ .'config.xml';
        $xml = simplexml_load_file($xmlPath);
        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->max_level = $_POST['max_level'];
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

                    if(!check_value($_POST['vip_name'])) throw new  Exception("添加失败，请先填写会员名称。");
                    if(!check_value($_POST['vip_code'])) throw new  Exception("添加失败，请先填写会员编号。");
                    if(!check_value($_POST['items_code'])) throw new  Exception("添加失败，请先填写兑换物品。");
                    if(!check_value($_POST['items_name'])) throw new  Exception("添加失败，请先填写物品名称。");
                    if(!check_value($_POST['items_number'])) throw new  Exception("添加失败，请先填写物品数量。");
                    if(!check_value($_POST['status'])) throw new  Exception("添加失败，请确保数据的正确性！");
                    if(!check_value($_POST['Description'])) throw new  Exception("添加失败，请先填写会员描述。");
                    $query = [
                        $_POST['vip_name'],
                        $_POST['vip_code'],
                        $_POST['Description'],
                        $_POST['items_code'],
                        $_POST['items_name'],
                        $_POST['items_number'],
                        $_POST['status'],
                    ];
                    $sql = $web->query("INSERT INTO [X_TEAM_MEMBER_EXCHANGE]([vip_name],[vip_code],[Description],[price],[price_type],[status]) VALUES (?,?,?,?,?,?)",$query);
                    if(!$sql) throw new  Exception("添加失败，请确保数据的正确性！");
                    message('success', '奖励配置添加成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_edit":
                try{
                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    if(!check_value($_POST['vip_name'])) throw new  Exception("添加失败，请先填写会员名称。");
                    if(!check_value($_POST['vip_code'])) throw new  Exception("添加失败，请先填写会员编号。");
                    if(!check_value($_POST['items_code'])) throw new  Exception("添加失败，请先填写兑换物品。");
                    if(!check_value($_POST['items_name'])) throw new  Exception("添加失败，请先填写物品名称。");
                    if(!check_value($_POST['items_number'])) throw new  Exception("添加失败，请先填写物品数量。");
                    if(!check_value($_POST['status'])) throw new  Exception("添加失败，请确保数据的正确性！");
                    if(!check_value($_POST['Description'])) throw new  Exception("添加失败，请先填写会员描述。");
                    $query = [
                            'vip_name' => $_POST['vip_name'],
                            'vip_code' => $_POST['vip_code'],
                            'Description' => $_POST['Description'],
                            'items_code' => $_POST['items_code'],
                            'items_name' => $_POST['items_name'],
                            'items_number' => $_POST['items_number'],
                            'status' => $_POST['status'],
                            'ID' => $_POST['ID'],
                    ];
                    $sql = $web->query("UPDATE [X_TEAM_MEMBER_EXCHANGE] SET [vip_name] = :vip_name,[vip_code] = :vip_code,[Description] = :Description,[items_code] = :items_code,[items_name] = :items_name,[items_number] = :items_number,[status] = :status WHERE [ID] = :ID",$query);
                    if(!$sql) throw new  Exception("编辑失败，请确保数据的正确性！");
                    message('success', '奖励配置编辑成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_delete":
                try{
                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    $delete = $web->query("DELETE FROM [X_TEAM_MEMBER_EXCHANGE] WHERE [ID] = ?",[$_POST['ID']]);
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
    $MemberList = new \Plugin\MemberEx();
    $creditSystem = new CreditSystem();
    $moduleConfig = $MemberList->loadConfig();
    ?>
    <div class="card">
        <div class="card-header">
            会员领奖
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
                                class="text-muted">启用/禁用 日志是否写入数据库 <strong>日志表:[X_TEAM_MEMBER_EXCHANGE_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>最高等级</strong>  <span
                                class="text-muted">会员最高等级(仅用于显示作用)</td>
                        <td><input type="text" class="" name="max_level" value="<?=$moduleConfig['max_level']?>"/></td>
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
                    <th width="14%">会员名称(显示作用)</th>
                    <th >会员等级</th>
                    <th >所需物品(总编号)</th>
                    <th >物品名称</th>
                    <th >物品数量</th>
                    <th>描述</th>
                    <th >模式</th>
                    <th>操作(无用)</th>
                </tr>
                </thead>
                <tbody>
                <?$list = $MemberList->getMemberExList()?>
                <?IF(is_array($list)){?>
                    <?foreach ($list as $data){?>
                <form action="" method="post">
                    <tr>
                        <td><input type="text" class="form-control" name="vip_name" value="<?=$data['vip_name']?>" /></td>
                        <td><input type="number" class="form-control" name="vip_code" value="<?=$data['vip_code']?>"/></td>
                        <td><input type="text" class="form-control" name="items_code" value="<?=$data['items_code']?>"/></td>
                        <td><input type="text" class="form-control" name="items_name" maxlength="50" value="<?=$data['items_name']?>"/></td>
                        <td><input type="number" class="form-control" name="items_number" maxlength="9" value="<?=$data['items_number']?>"/></td>

                        <td><input type="text" class="form-control" name="Description" value="<?=$data['Description']?>"/></td>
                        <td>
                            <select class="form-control" name="status">
                                <option value="1" <?=selected($data['status'],1)?>>启用</option>
                                <option value="0" <?=selected($data['status'],0)?>>禁用</option>
                            </select>
                        </td>
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
                    <tr><th  colspan="7" class="text-danger">暂无配置</th></tr>
                <?}?>
                <tr><th colspan="7"><strong>添加新的</strong></th></tr>
                <form action="" method="post">
                    <tr>
                        <td><input type="text" class="form-control" name="vip_name"/></td>
                        <td><input type="number" class="form-control" name="vip_code"/></td>
                        <td><input type="text" class="form-control" name="items_code" /></td>
                        <td><input type="text" class="form-control" name="items_name" maxlength="50" /></td>
                        <td><input type="number" class="form-control" name="items_number" maxlength="9" /></td>
                        <td><input type="text" class="form-control" name="Description"/></td>
                        <td>
                            <select class="form-control" name="status">
                                <option value="0">禁用</option>
                                <option value="1" selected>启用</option>
                            </select>
                        </td>
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



