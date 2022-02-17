<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {

function saveChanges() {
	global $_POST;
	foreach($_POST as $setting) {
		if(!check_value($setting)) {
			message('error','缺少数据（请填写所有字段）');
			return;
		}
	}
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'profiles.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['active'];
	$xml->update_time = $_POST['update_time'];
	$xml->show_level = $_POST['show_level'];
	$xml->show_online = $_POST['show_online'];
	$xml->show_guild = $_POST['show_guild'];
    $xml->show_gens = $_POST['show_gens'];
	$xml->show_location = $_POST['show_location'];
	$xml->show_total_status = $_POST['show_total_status'];
	$save = $xml->asXML($xmlPath);
	if($save) {
		message('success','已成功保存.');
	} else {
		message('error','保存时发生错误.');
	}
}

if(check_value($_POST['submit_changes'])) {
	saveChanges();
}
$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'profiles.xml';
$moduleConfig = simplexml_load_file($xmlPath);
?>
<div class="card">
    <div class="card-header">资料设置</div>
    <div class="card-body">
        <form action="" method="post">
            <table class="table table-striped table-bordered table-hover module_config_tables">
                <tr>
                    <th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 个人资料模块</span></th>
                    <td>
                        <?=enableDisableCheckboxes('active',$moduleConfig->active,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>缓存时间</strong><span class="ml-3 text-muted">每次间隔缓存一次排名数据，单位:秒</span></th>
                    <td>
                        <input class="input-mini" type="text" name="update_time" value="<?php echo $moduleConfig->update_time; ?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>显示等级</strong><span class="ml-3 text-muted">是否显示角色等级信息。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('show_level',$moduleConfig->show_level,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>在线信息</strong><span class="ml-3 text-muted">是否显示在线详细信息。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('show_online',$moduleConfig->show_online,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>战盟信息</strong><span class="ml-3 text-muted">是否显示战盟详细信息。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('show_guild',$moduleConfig->show_guild,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>家族信息</strong><span class="ml-3 text-muted">是否显示家族详细信息。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('show_gens',$moduleConfig->show_gens,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>位置信息</strong><span class="ml-3 text-muted">是否显示所在位置信息。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('show_location',$moduleConfig->show_location,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>总点数统计</strong><span class="ml-3 text-muted">是否显示总点数统计。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('show_total_status',$moduleConfig->show_total_status,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
                </tr>
            </table>
        </form>

    </div>
</div>
<?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}