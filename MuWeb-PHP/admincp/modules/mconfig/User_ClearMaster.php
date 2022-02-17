<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

function saveChanges() {
	global $_POST;
	foreach($_POST as $setting) {
		if(!check_value($setting)) {
			message('error','缺少数据（请填写所有字段）');
			return;
		}
	}
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'usercp.clearmaster.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['active'];
	$xml->clearst_enable_zen_requirement = $_POST['clearst_enable_zen_requirement'];
    $xml->clearst_required_level = $_POST['clearst_required_level'];
	$xml->clearst_master_point = $_POST['clearst_master_point'];
    $xml->clearst_credit_type = $_POST['clearst_credit_type'];
	$xml->clearst_price = $_POST['clearst_price'];

	$save = $xml->asXML($xmlPath);
	if($save) {
		message('success','已成功保存.');
	} else {
		message('error','保存时发生错误.');
	}
}

if (check_value($_POST['submit'])) {
    switch ($_POST['submit']){
        case "submit_changes":
            try {
                if(!$_POST['clearst_enable_zen_requirement'] && !$_POST['clearst_credit_type']) throw new Exception("您可以选择货币类型，请先预设一种货币类型。");
                saveChanges();
            }catch (Exception $exception){
                message('error',$exception->getMessage());
            }
            break;
        case "submit_add":
            try {
                if (!check_value($_POST['Class'])) throw new Exception("请正确输入角色代码。");
                if (!check_value($_POST['Name'])) throw new Exception("请正确输入角色名称。");
                if (!check_value($_POST['MagicList'])) throw new Exception("请正确输入角色技能数据。");
                $sql = Connection::Database("Web")->query("INSERT INTO [X_TEAM_CLEAR_MASTER] ([Class],[Name],[MagicList],[status]) VALUES (?, ?, CONVERT(VARBINARY(MAX),?,2),1)",[$_POST['Class'],$_POST['Name'],$_POST['MagicList']]);
                if(!$sql) throw new  Exception("操作失败，请确保数据的正确性！");
                message('success', "恭喜您，配置修改成功！");
            }catch (Exception $exception){
                message('error',$exception->getMessage());
            }
            break;
            case "submit_edit":
            try {
                if (!check_value($_POST['ID'])) throw new Exception("提交错误，请重新尝试。");
                if (!check_value($_POST['Class'])) throw new Exception("请正确输入角色代码。");
                if (!check_value($_POST['Name'])) throw new Exception("请正确输入角色名称。");
                if (!check_value($_POST['MagicList'])) throw new Exception("请正确输入角色技能数据。");
                $sql = Connection::Database("Web")->query("UPDATE [X_TEAM_CLEAR_MASTER] SET [Class] = ?,[Name] = ?,[MagicList] = CONVERT(VARBINARY(MAX),?,2) WHERE [ID] = ?",[$_POST['Class'],$_POST['Name'],$_POST['MagicList'],$_POST['ID']]);
                if(!$sql) throw new  Exception("操作失败，请确保数据的正确性！");
                message('success', "恭喜您，配置修改成功！");
            }catch (Exception $exception){
                message('error',$exception->getMessage());
            }
            break;
        case "submit_delete":
            try {
                if (!check_value($_POST['ID'])) throw new Exception("提交错误，请重新尝试。");
                $sql = Connection::Database("Web")->query("DELETE FROM [X_TEAM_CLEAR_MASTER] WHERE [ID] = ?",[$_POST['ID']]);
                if(!$sql) throw new  Exception("操作失败，请确保数据的正确性！");
                message('success', "恭喜您，配置删除成功！");
            }catch (Exception $exception){
                message('error',$exception->getMessage());
            }
            break;
            default:
                message('error',"提交错误，请重新尝试。");
                break;
    }
}
loadModuleConfigs('usercp.clearmaster');
$creditSystem = new CreditSystem();
?>
<div class="card">
    <div class="card-header">清洗大师技能设置</div>
    <div class="card-body">
        <form action="" method="post">
            <table class="table table-striped table-bordered table-hover module_config_tables">
                <tr>
                    <th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 清洗大师技能模块.</span></th>
                    <td>
                        <?=enableDisableCheckboxes('active',mconfig('active'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>大师等级</strong><span class="ml-3 text-muted">清洗大师功能所需要的最低大师等级要求。</span></th>
                    <td>
                        <input class="form-control" type="number" name="clearst_required_level" value="<?=mconfig('clearst_required_level')?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>大师点数</strong><span class="ml-3 text-muted">设置每一级大师赠送的点数</span></th>
                    <td>
                        <input class="form-control" type="number" class="form-control"name="clearst_master_point" value="<?=mconfig('clearst_master_point')?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>货币要求<strong><span class="ml-3 text-muted">选择使用Mu币作为费用还是货币作为费用。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('clearst_enable_zen_requirement',mconfig('clearst_enable_zen_requirement'),'金币(Zen)','货币'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>货币类型<strong><span class="ml-3 text-muted">如果上述使用货币选项此项则生效。</span></th>
                    <td>
                        <?=$creditSystem->buildSelectInput("clearst_credit_type", mconfig('clearst_credit_type'), "form-control"); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>货币价格</strong><span class="ml-3 text-muted">基于上诉选择的货币类型，选择金币(Zen)此项则为金币(Zen)，如果选择货币此项则为货币价格。</span></th>
                    <td>
                        <input type="number" class="form-control" name="clearst_price" value="<?=mconfig('clearst_price')?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><div style="text-align:center"><button type="submit" name="submit" value="submit_changes" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
                </tr>
            </table>
        </form>
    </div>
</div>
<?
try {
    @$list = Connection::Database("Web")->query_fetch("SELECT [ID],[Class],[Name],CONVERT(varchar(max),[MagicList],2) AS [MagicList],[status] FROM [X_TEAM_CLEAR_MASTER]");
    if (!is_array($list)) throw new Exception("暂时无法获取技能列表配置信息。");
    ?>
    <div class="card">
    <div class="card-header">技能配置</div>
    <div class="">
        <table class="table table-striped table-bordered text-center">
            <thead>
            <tr>
                <th width="10%">角色编号</th>
                <th width="10%">角色类型</th>
                <th width="65%">技能数据<span class="text-danger">(*字段:[MagicList])</span></th>
                <th width="15%">操作</th>
            </tr>
            </thead>
            <tbody>
            <?if (is_array($list)) {?>
                <?foreach ($list as $data){?>
                    <form action="" method="post">
                        <tr>
                            <td><input type="text" name="Class" class="form-control" value="<?=$data['Class']?>" /></td>
                            <td><input type="text" name="Name" class="form-control" value="<?=$data['Name']?>" /></td>
                            <td><input type="text" name="MagicList" class="form-control" value="<?=$data['MagicList']?>" /></td>

                            <td>
                                <div class="btn-group col-md-12">
                                    <input type="hidden" name="ID" value="<?=$data['ID']?>">
                                    <button type="submit" name="submit" value="submit_edit" class="btn btn-primary">编辑</button>
                                    <button type="submit" name="submit" value="submit_delete" class="btn btn-danger">删除</button>
                                </div>
                            </td>
                        </tr>
                    </form>
                <?}?>
            <?}else{?>
                <tr><td colspan="5">暂无数据</td></tr>
            <?}?>
            <tr><td colspan="5">添加新的</td></tr>
            <form action="" method="post">
                <tr>
                    <td><input type="text" name="Class" class="form-control" /></td>
                    <td><input type="text" name="Name" class="form-control"  /></td>
                    <td><input type="text" name="MagicList" class="form-control" /></td>
                    <td>
                        <div class=" ">
                            <button type="submit" name="submit" value="submit_add" class="btn btn-success col-md-12">添加</button>
                        </div>
                    </td>
                </tr>
            </form>
            </tbody>
        </table>
    </div>
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}