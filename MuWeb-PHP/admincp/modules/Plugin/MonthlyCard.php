<?php
/**
 * [MonthlyCard]后台配置文件
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
                    <li class="breadcrumb-item active">购买月卡</li>
                </ol>
            </div>
            <h4 class="page-title">购买月卡</h4>
        </div>
    </div>
</div>
<?php
try {
    $web = Connection::Database("Web");
    function submit()
    {
        $xmlPath = __PATH_MonthlyCard_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];

        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '保存时发生错误！');
        }
    }
    if (check_value($_POST['submit'])) {
        switch ($_POST['submit']){
            case "submit": #配置文件保存
                try{
                    submit();
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_add": #添加月卡配置
                try{
                    if(!check_value($_POST['project_name'])) throw new Exception("请输入月卡名称。");
                    if(!check_value($_POST['status'])) throw new Exception("请正确选择状态。");
                    if(!check_value($_POST['day'])) throw new Exception("请正确输入月卡天数，可以是1~365天。");
                    if(!check_value($_POST['price'])) throw new Exception("请正确输入月卡价格。");
                    if(!check_value($_POST['credit_type'])) throw new Exception("请正确输入月卡价格。");
                    if(!check_value($_POST['daily_salary'])) throw new Exception("请正确输入每日领取的工资额度。");
                    if(!check_value($_POST['salary_type'])) throw new Exception("请正确选择工资类型。");

                    $query = [
                        $_POST['project_name'],
                        $_POST['day'],
                        $_POST['price'],
                        $_POST['credit_type'],
                        $_POST['daily_salary'],
                        $_POST['salary_type'],
                        $_POST['status'],
                    ];

                    $sql = $web->query("INSERT INTO [X_TEAM_MONTHLY_CARD_CONFIG] ([project_name],[day],[price],[credit_type],[daily_salary],[salary_type],[status]) VALUES (?, ?, ?, ?, ?, ?, ?)",$query);
                    if(!$sql) throw new  Exception("操作失败，请确保数据的正确性！");
                    message('success', "恭喜您，月卡配置添加成功！");
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_edit": #编辑月卡配置
                try{
                    if(!check_value($_POST['ID'])) throw new Exception("操作失败，请确保数据的正确性！");
                    if(!check_value($_POST['project_name'])) throw new Exception("请输入月卡名称。");
                    if(!check_value($_POST['status'])) throw new Exception("请正确选择状态。");
                    if(!check_value($_POST['day'])) throw new Exception("请正确输入月卡天数，可以是1~365天。");
                    if(!check_value($_POST['price'])) throw new Exception("请正确输入月卡价格。");
                    if(!check_value($_POST['credit_type'])) throw new Exception("请正确输入月卡价格。");
                    if(!check_value($_POST['daily_salary'])) throw new Exception("请正确输入每日领取的工资额度。");
                    if(!check_value($_POST['salary_type'])) throw new Exception("请正确选择每日领取的工资类型。");

                    $update = [
                        'project_name' => $_POST['project_name'],
                        'day' => $_POST['day'],
                        'price' => $_POST['price'],
                        'credit_type' => $_POST['credit_type'],
                        'daily_salary' => $_POST['daily_salary'],
                        'salary_type' => $_POST['salary_type'],
                        'status' => $_POST['status'],
                        'ID' => $_POST['ID']
                    ];

                    $sql = $web->query("UPDATE [X_TEAM_MONTHLY_CARD_CONFIG] SET [project_name] = :project_name,[day] = :day,[price] = :price,[credit_type] = :credit_type,[daily_salary] = :daily_salary,[salary_type] = :salary_type,[status] = :status WHERE [ID] = :ID",$update);
                    if(!$sql) throw new  Exception("操作失败，请确保数据的正确性！");
                    message('success', '恭喜您，月卡配置编辑成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_delete": #删除月卡配置
                try{
                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    $delete = $web->query("DELETE FROM [X_TEAM_MONTHLY_CARD_CONFIG] WHERE [ID] = ?",[$_POST['ID']]);
                    if(!$delete) throw new  Exception("操作失败，请确保数据的正确性！");
                    message('success', '月卡配置删除成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
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
    $MonthlyCard = new \Plugin\MonthlyCard();
    $moduleConfig = $MonthlyCard->loadConfig();
    $creditSystem = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header">
            月卡配置
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
                        <td><strong>日志开关</strong>  <span class="text-muted">启用/禁用 铸造日志是否写入数据库 <strong>日志表:[X_TEAM_MONTHLY_CARD_LOG]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
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
    <div class="card">
        <div class="card-header">月卡列表</div>
        <div class="">
            <table class="table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th>名称</th>
                    <th>状态</th>
                    <th>天数</th>
                    <th>售价</th>
                    <th>货币类型</th>
                    <th>每日可领</th>
                    <th>领取类型</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?$list = $MonthlyCard->getMonthlyCardList()?>
                <?IF(is_array($list)){?>
                    <?foreach ($list as $data){?>
                <form action="" method="post">

                    <tr>
                        <td>
                            <input type="hidden" class="form-control" name="ID" value="<?=$data['ID']?>"/>
                            <input type="text" class="form-control" name="project_name" value="<?=$data['project_name']?>"/>
                        </td>
                        <td>
                            <select class="form-control" name="status">
                                <option value="1" <?=selected($data['status'],1)?>>启用</option>
                                <option value="0" <?=selected($data['status'],0)?>>禁用</option>
                            </select>
                        </td>
                        <td><input type="number" class="form-control" name="day" value="<?=$data['day']?>"/></td>
                        <td><input type="number" class="form-control" name="price" value="<?=$data['price']?>"/></td>
                        <td><?=$creditSystem->buildSelectInput("credit_type", $data['credit_type'], "form-control"); ?></td>
                        <td><input type="number" class="form-control" name="daily_salary" value="<?=$data['daily_salary']?>"/></td>
                        <td><?=$creditSystem->buildSelectInput("salary_type", $data['salary_type'], "form-control"); ?></td>
                        <td>
                            <div class="btn-group col-md-12">
                                <button type="submit" name="submit" value="submit_edit" class="btn btn-primary">编辑</button>
                                <button type="submit" name="submit" value="submit_delete" class="btn btn-danger">删除</button>
                            </div>
                        </td>
                    </tr>

                </form>
                    <?}?>
                <?}else{?>
                    <tr><th  colspan="8" class="text-danger">暂无月卡配置</th></tr>
                <?}?>
                    <tr><th colspan="8"><strong>添加新的</strong></th></tr>
                <form action="" method="post">
                    <tr>
                        <td><input type="text" class="form-control" name="project_name"/></td>
                        <td>
                            <select class="form-control" name="status">
                                <option value="0">禁用</option>
                                <option value="1" selected>启用</option>
                            </select>
                        </td>
                        <td><input type="number" class="form-control" name="day"/></td>
                        <td><input type="number" class="form-control" name="price"/></td>
                        <td><?=$creditSystem->buildSelectInput("credit_type", $data['credit_type'], "form-control"); ?></td>
                        <td><input type="number" class="form-control" name="daily_salary"/></td>
                        <td><?=$creditSystem->buildSelectInput("salary_type", $data['salary_type'], "form-control"); ?></td>
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



