<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try{

$downloadTypes = [
	1 => '客户端',
	2 => '补丁',
	3 => '工具',
];

function downloadTypesSelect($downloadTypes,$selected=null) {
	foreach($downloadTypes as $key => $typeOPTION) {
		if(check_value($selected)) {
			if($key == $selected) {
				echo '<option value="'.$key.'" selected="selected">'.$typeOPTION.'</option>';
			} else {
				echo '<option value="'.$key.'">'.$typeOPTION.'</option>';
			}
		} else {
			echo '<option value="'.$key.'">'.$typeOPTION.'</option>';
		}
	}
}

function saveChanges() {
	global $_POST;
	foreach($_POST as $setting) {
		if(!check_value($setting)) {
			message('error','缺少数据（请填写所有字段）');
			return;
		}
	}
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'downloads.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['setting_1'];
	$xml->show_client_downloads = $_POST['setting_2'];
	$xml->show_patch_downloads = $_POST['setting_3'];
	$xml->show_tool_downloads = $_POST['setting_4'];
	
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

if(check_value($_POST['downloads_add_submit'])) {
	$action = addDownload($_POST['downloads_add_title'], $_POST['downloads_add_desc'], $_POST['downloads_add_link'], $_POST['downloads_add_size'], $_POST['downloads_add_type']);
	
	if($action) {
		message('success','您的下载链接已成功添加！');
	} else {
		message('error','添加下载链接时出错！');
		
	}
}

if(check_value($_POST['downloads_edit_submit'])) {
	$action = editDownload($_POST['downloads_edit_id'], $_POST['downloads_edit_title'], $_POST['downloads_edit_desc'], $_POST['downloads_edit_link'], $_POST['downloads_edit_size'], $_POST['downloads_edit_type']);
	if($action) {
		message('success','您的下载链接已成功更新！');
	} else {
		message('error','更新下载链接时出错。');
	}
}

if(check_value($_REQUEST['deletelink'])) {
	$action = deleteDownload($_REQUEST['deletelink']);
	if($action) {
		message('success','您的下载链接已成功删除！');
	} else {
		message('error','删除下载链接时出错。');
	}
}

loadModuleConfigs('downloads');
?>
<div class="card">
    <div class="card-header">下载页面设置</div>
    <div class="card-body">
<form action="" method="post">
	<table class="table table-striped table-bordered table-hover module_config_tables">
		<tr>
			<th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 下载模块</span></th>
			<td>
				<?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
			</td>
		</tr>
		<tr>
            <th><strong>显示客户端下载</strong></th>
			<td>
				<?=enableDisableCheckboxes('setting_2',mconfig('show_client_downloads'),'是','否'); ?>
			</td>
		</tr>
		<tr>
            <th><strong>显示补丁下载</strong></th>
			<td>
				<?=enableDisableCheckboxes('setting_3',mconfig('show_patch_downloads'),'是','否'); ?>
			</td>
		</tr>
		<tr>
            <th><strong>显示工具下载</strong></th>
			<td>
				<?=enableDisableCheckboxes('setting_4',mconfig('show_tool_downloads'),'是','否'); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
		</tr>
	</table>
</form>
    </div>
</div>
<hr>
<div class="card">
<div class="card-header">下载管理</div>
    <div class="card-body">
<?php
$downloads = getDownloadsList();
if(is_array($downloads)) {
echo '
<table class="table table-striped table-bordered table-hover text-center">
	<tr>
		<th>标题</th>
		<th>描述</th>
		<th>链接</th>
		<th>大小</th>
		<th>类型</th>
		<th></th>
	</tr>';
	
	foreach($downloads as $thisDownload) {
	echo '
	<form action="index.php?module=Settings_Model&config=Config_Down" method="post">
	<input type="hidden" name="downloads_edit_id" value="'.$thisDownload['download_id'].'"/>
	<tr>
		<td><input type="text" name="downloads_edit_title" class="form-control" value="'.$thisDownload['download_title'].'"/></td>
		<td><input type="text" name="downloads_edit_desc" class="form-control" value="'.$thisDownload['download_description'].'"/></td>
		<td><input type="text" name="downloads_edit_link" class="form-control" value="'.$thisDownload['download_link'].'"/></td>
		<td><input type="text" name="downloads_edit_size" class="form-control" value="'.$thisDownload['download_size'].'"/></td>
		<td>
			<select name="downloads_edit_type" class="form-control">';
				downloadTypesSelect($downloadTypes, $thisDownload['download_type']);
		echo '
			</select>
		</td>
		<td>
		<input type="submit" class="btn btn-success" name="downloads_edit_submit" value="编辑"/>
		<a href="index.php?module=Settings_Model&config=Config_Down&deletelink='.$thisDownload['download_id'].'" class="btn btn-danger">删除</a>
		</td>
	</tr>
	</form>';
	}
	
echo '</table>';
} else {
	message('error','您尚未添加任何下载链接。');
}
?>
    </div>
</div>

<div class="card">
    <div class="card-header">添加新的</div>
    <div class="card-body">
        <form action="index.php?module=Settings_Model&config=Config_Down" method="post">
            <table class="table table-striped table-bordered table-hover text-center">
                <tr>
                    <th>标题</th>
                    <th>描述</th>
                    <th>链接</th>
                    <th>大小</th>
                    <th>类型</th>
                </tr>
                <tr>
                    <td><input type="text" name="downloads_add_title" class="form-control"/></td>
                    <td><input type="text" name="downloads_add_desc" class="form-control"/></td>
                    <td><input type="text" name="downloads_add_link" class="form-control"/></td>
                    <td><input type="text" name="downloads_add_size" class="form-control" value="500.12MB"/></td>
                    <td>
                        <select name="downloads_add_type" class="form-control">
                            <? downloadTypesSelect($downloadTypes); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="5"><div style="text-align:center">
                            <button type="submit" name="downloads_add_submit" value="submit" class="btn btn-success waves-effect waves-light col-md-2">添加</button>
                        </div></td>
                </tr>
            </table>
        </form>

    </div>
</div>
<?php
    }catch (Exception $exception){
    message('error',$exception->getMessage());
}