<?php
/**
 * 货币管理
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
                        <li class="breadcrumb-item active">货币管理</li>
                        <li class="breadcrumb-item active">货币配置</li>
                    </ol>
                </div>
                <h4 class="page-title">货币配置</h4>
            </div>
        </div>
    </div>
<?php
try {
    $creditSystem = new CreditSystem();
    if(check_value($_POST)) {
        try {
            $creditSystem->setConfigId($_POST['config']);
            $creditSystem->setIdentifier($_POST['identifier']);
            $group = getGroupIDForServerCode($_POST['group']);
            switch($_POST['transaction']) {
                case 1:
                    $creditSystem->addCredits($group,$_POST['credits']);
                    message('success', '交易完成!');
                    break;
                case 0:
                    $creditSystem->subtractCredits($group,$_POST['credits']);
                    message('success', '交易完成!');
                    break;
                default:
                    throw new Exception("交易无效!");
            }
        } catch (Exception $ex) {
            message('error', $ex->getMessage());
        }
    }
?>
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">货币管理</div>
            <div class="card-body">
                <form role="form" method="post">
                    <div class="form-group">
                        <label>配置:</label>
                        <?=$creditSystem->buildSelectInput("config", 1, "form-control"); ?>
                    </div>
                    <div class="form-group">
                        <label for="identifier1">识别码:</label>
                        <input type="text" class="form-control" id="identifier1" name="identifier" placeholder="识别码">
                        <p class="help-block">根据所选的配置，它可以是用户ID，账号，邮件，角色。</p>
                    </div>
                    <div class="form-group">

                        <label for="group">所属大区</label>
                        <select name="group" id="group" class="form-control">
                            <?global $serverGrouping;?>
                            <?foreach ($serverGrouping as $item){?>
                            <option value="<?=$item['SERVER_GROUP']?>"><?=$item['SERVER_NAME']?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="credits1">货币:</label>
                        <input type="number" class="form-control" id="credits1" name="credits" placeholder="0">
                    </div>
                    <div class="form-group row justify-content-md-center">
                    <?=enableDisableCheckboxes('transaction',1,'加[+]','减[-]')?>
                    </div>
                    <button type="submit" class="col-md-12 btn btn-success">确定</button>
                </form>

            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">操作记录</div>
            <div class="card-body">
<?
                $creditsLogs = $creditSystem->getLogs();
                if(is_array($creditsLogs)) {
                    ?>
                <table id="datatable" class="table table-hover text-center">
                    <thead>
                    <tr>
                        <th>类型</th>
                        <th>标识符</th>
                        <th>货币</th>
                        <th>操作</th>
                        <th>日期</th>
                        <th>来源(模块)</th>
                        <th>IP</th>
                        <th>管理员</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    foreach($creditsLogs as $data) {
                    $in_admincp = ($data['log_inadmincp'] == 1 ? '<span class="text-success">是</span>' : '<span class="text-danger">否</span>');
                    $transaction = ($data['log_transaction'] == "add" ? '<span class="text-success">+</span>' : '<span class="text-danger">-</span>');
?>
                    <tr>
                        <td><?=$data['log_config']?></td>
                        <td><?=$data['log_identifier']?></td>
                        <td><?=$data['log_credits']?></td>
                        <td><?=$transaction?></td>
                        <td><?=$data['log_date']?></td>
                        <td><?=$data['log_module']?></td>
                        <td><?=$data['log_ip']?></td>
                        <td><?=$in_admincp?></td>
                    </tr>
                    <?
                    }
?>
                    </tbody>
                </table>
                <?
                } else {
                message('warning', '暂时没有记录!');
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}