
<?php
/**
 * 文件说明
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
                    <li class="breadcrumb-item active">插件管理</li>
                </ol>
            </div>
            <h4 class="page-title">插件管理</h4>
        </div>
    </div>
</div>
<?php
define('PLUGIN_ALLOW_UNINSTALL',true);
$Plugins = new Plugins();

if(check_value($_REQUEST['enable'])) {
	$Plugins->updatePluginStatus($_REQUEST['enable'],1);
}
if(check_value($_REQUEST['disable'])) {
	$Plugins->updatePluginStatus($_REQUEST['disable'],0);
}
if(check_value($_REQUEST['uninstall'])) {
	$uninstall_plugin = $Plugins->uninstallPlugin($_REQUEST['uninstall']);
	if($uninstall_plugin) {
		message('success','插件已成功卸载!');
	} else {
		message('error','无法卸载插件!');
	}
	$update_cache = $Plugins->rebuildPluginsCache();
	if(!$update_cache) {
		message('error','无法更新插件缓存数据，请确保该文件存在且可写!');
	}
}

$plugins = $Plugins->getInstalledPlugins();
if(is_array($plugins)) {
    echo '<div class="card">';
    echo '<div class="card-header">插件列表</div>';
    echo '<div class="card-body">';
	echo '<table class="table text-center">';
		echo '<thead>';
			echo '<tr>';
			echo '<th>名称</th>';
			echo '<th>作者</th>';
			echo '<th>版本</th>';
			echo '<th>兼容版本</th>';
			echo '<th>安装日期</th>';
			echo '<th>状态</th>';
			echo '<th>操作</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
		foreach($plugins as $thisPlugin) {
		
			if($thisPlugin['status'] == 1) {
				$status = '<span class="text-success">启用</span>';
				$ed = '<a class="btn btn-danger waves-effect waves-light" href="index.php?module=Plugins_Manage&disable='.$thisPlugin['id'].'">禁用</a>';
			} else {
				$status = '<span class="text-danger">禁用</span>';
				$ed = '<a class="btn btn-success waves-effect waves-light" href="index.php?module=Plugins_Manage&enable='.$thisPlugin['id'].'">使用</a>';
			}
			
			$uninstall = '';
			if(PLUGIN_ALLOW_UNINSTALL) {
				$uninstall = '<a class="btn btn-danger btn-xs" href="index.php?module=Plugins_Manage&uninstall='.$thisPlugin['id'].'">卸载</a></td>';
			}
			
			echo '<tr>';
			echo '<td>'.$thisPlugin['name'].'</td>';
			echo '<td>'.$thisPlugin['author'].'</td>';
			echo '<td>'.$thisPlugin['version'].'</td>';
			echo '<td>'.implode(", ",explode(",",$thisPlugin['compatibility'])).'</td>';
			echo '<td>'.date("m/d/Y",$thisPlugin['install_date']).'</td>';
			echo '<td>'.$status.'</td>';
			echo '<td>'.$ed.'  '.$uninstall.'';
			echo '</tr>';
		}
		echo '<tbody>';
	echo '</table>';
	echo '</div>';
	echo '</div>';
} else {
	message('warning','尚未安装任何插件!');
}
?>