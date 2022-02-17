<?php
/**
 * 在线商城插件后台模块
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
                        <li class="breadcrumb-item active">在线夺宝</li>
                    </ol>
                </div>
                <h4 class="page-title">在线夺宝</h4>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="float-right mb-2">
                <a class="btn btn-success btn-lg" href="<?=admincp_base('/Plugin/lottery'); ?>">夺宝设置</a>
                <a class="btn btn-warning btn-lg" href="<?=admincp_base('Plugin/lottery_log'); ?>">夺宝日志</a>
            </div>
        </div>
    </div>
<?php
    $lottery = new \Plugin\lottery();
    try {
        if (check_value($_POST['submit'])) {
            switch ($_POST['submit']){
                case "add":
                    if(!$_POST['reward_item_name']) throw new Exception("请输入物品名称!");
                    if(!$_POST['reward_item_code']) throw new Exception("请输入物品代码!");
                    if(!Validator::Items($_POST['reward_item_code'])) throw new Exception("请正确输入物品代码!");
                    if(!$_POST['reward_item_price']) throw new Exception("请输入物品兑换价格!");
                    if(!check_value($_POST['status'])) throw new Exception("禁止非法提交!");
                    $INSERT = [
                            'reward_item_name' => $_POST['reward_item_name'],
                            'reward_item_code' => $_POST['reward_item_code'],
                            'reward_item_price' => $_POST['reward_item_price'],
                            'status' => $_POST['status'],
                    ];
                    $sql = Connection::Database("Web")->query("INSERT INTO [X_TEAM_LOTTERY_SHOP] ([reward_item_name],[reward_item_code],[reward_item_price],[status]) VALUES (:reward_item_name, :reward_item_code, :reward_item_price, :status)",$INSERT);
                    if(!$sql) throw new Exception("操作失败,请确保数据的正确性!");
                    break;
                case "delete":
                    if(!$_POST['id']) throw new Exception("禁止非法提交!");
                    $sql = Connection::Database("Web")->query("DELETE FROM [X_TEAM_LOTTERY_SHOP] WHERE [ID] = ?",[$_POST['id']]);
                    if(!$sql) throw new Exception("操作失败,请确保数据的正确性!");
                    break;
                case "edit":
                    if(!$_POST['id']) throw new Exception("禁止非法提交!");
                    if(!$_POST['reward_item_name']) throw new Exception("请输入物品名称!");
                    if(!$_POST['reward_item_code']) throw new Exception("请输入物品代码!");
                    if(!$_POST['reward_item_price']) throw new Exception("请输入物品兑换价格!");
                    if(!check_value($_POST['status'])) throw new Exception("禁止非法提交!");
                    $sql = Connection::Database("Web")->query("UPDATE [X_TEAM_LOTTERY_SHOP] SET [reward_item_name] = ?,[reward_item_code] = ?,[reward_item_price] = ?,[status] = ? WHERE [ID] = ?",[$_POST['reward_item_name'],$_POST['reward_item_code'],$_POST['reward_item_price'],$_POST['status'],$_POST['id']]);
                    if(!$sql) throw new Exception("操作失败,请确保数据的正确性!");
                    break;
                default:
                    throw new Exception("禁止非法提交!");
            }
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
try {
    $sList = $lottery->getLotteryShop();
    ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            稀有商店列表
        </div>
        <div class="card-body">

                <table class="table table-striped table-bordered table-hover text-center">
                    <thead>
                    <tr>
                        <th style="width: 15%">物品名称</th>
                        <th style="width: 40%">物品代码</th>
                        <th style="width: 10%">兑换水晶(颗)</th>
                        <th style="width: 15%">状态</th>
                        <th style="width: 20%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?if(is_array($sList)){?>
                        <?foreach ($sList as $data){?>
                    <form class="form-horizontal" action="" method="post">
                            <tr>
                                <td>
                                    <input type="text" hidden="hidden" name="id" value="<?=$data['ID']?>" />
                                    <input type="text" class="form-control" name="reward_item_name" value="<?=$data['reward_item_name']?>" />
                                </td>
                                <td><input type="text" class="form-control" name="reward_item_code" value="<?=$data['reward_item_code']?>" /></td>
                                <td><input type="text" class="form-control" name="reward_item_price" value="<?=$data['reward_item_price']?>" /></td>
                                <td>
                                    <select class="form-control" name="status">
                                        <option value="1" <?if($data['status']) echo 'selected'?>>启用</option>
                                        <option value="0" <?if(!$data['status']) echo 'selected'?>>停用</option>
                                    </select>
                                </td>
                                <td>
                                    <button type="submit" name="submit" value="edit" class="btn btn-primary mr-3">保存</button>
                                    <button type="submit" name="submit" value="delete" class="btn btn-danger mr-3">删除</button>
                                </td>
                            </tr>
                    </form>
                        <?}?>
                    <?}else{?>
                        <tr>
                            <td colspan="5">稀有商店暂无物品</td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>

        </div>
    </div>
    <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            新增物品
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover text-center">
                    <thead>
                    <tr>
                        <th style="width: 15%">物品名称</th>
                        <th style="width: 40%">物品代码</th>
                        <th style="width: 10%">兑换水晶(颗)</th>
                        <th style="width: 15%">状态</th>
                        <th style="width: 20%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="text" class="form-control" name="reward_item_name" /></td>
                        <td><input type="text" class="form-control" name="reward_item_code" /></td>
                        <td><input type="text" class="form-control" name="reward_item_price" /></td>
                        <td>
                            <select class="form-control" name="status">
                                <option value="1" selected>启用</option>
                                <option value="0">停用</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" name="submit" value="add" class="btn btn-success mr-3 col-sm-12">添加</button>
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



