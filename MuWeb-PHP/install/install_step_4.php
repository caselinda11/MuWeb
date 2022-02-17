<?php
/**
 * 安装程序
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();
?>
<div class="progress mb-3">
    <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 60%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">60%</div>
</div>
<?php
try {
	if(check_value($_POST['install_step_3_submit'])) {
		if(!check_value($_POST['install_step_3_error'])) {
			# move to next step
			$_SESSION['install_cstep']++;
			header('Location: install.php');
			die();
		} else {
			echo '<div class="alert alert-danger" role="alert">已记录错误，无法继续。</div>';
		}
	}

   $muDb = new dB($_SESSION['SQL_DB_HOST'],$_SESSION['SQL_DB_PORT'], $_SESSION['SQL_DB_NAME'],$_SESSION['SQL_DB_USER'],$_SESSION['SQL_DB_PASS'],$_SESSION['SQL_PDO_DRIVER']);

	if($muDb->dead) {
		throw new Exception($muDb->error);
	}
	
	// SQL List
    global $install;
	if(!is_array($install['sql_list'])) throw new Exception('无法加载网站系统所需得SQL表列表。');
	foreach($install['sql_list'] as $sqlFileName => $sqlTableName) {
		if(!file_exists('sql/' . $sqlFileName . '.txt')) {
			throw new Exception('安装脚本缺少SQL表。');
		}
	}

	$error = false;
	echo '<div class="card">';
	    echo '<div class="card-header">'.$install['step_list'][4][1].'</div>';
    echo '<div class="card-body">';
	    echo '<table class="table table-striped table-sm">';
	foreach($install['sql_list'] as $sqlFileName => $sqlTableName) {
		$sqlFileContents = file_get_contents('sql/'.$sqlFileName.'.txt');
		if(!$sqlFileContents) continue;
		
		# 重命名表
		$query = str_replace('{TABLE_NAME}', $sqlTableName, $sqlFileContents);
		if(!$query) continue;

        # 删除表数据
		if($_GET['force'] == 1) {
			$muDb->query("DROP TABLE [".$sqlTableName."]");

		}

        # 检测是否存在
		$tableExists = $muDb->query_fetch_single("SELECT * FROM sysobjects WHERE xtype = 'U' AND name = ?", [$sqlTableName]);
		
		if(!$tableExists) {
			$create = $muDb->query($query);

            if($create) {
				echo '<tr><th>'.$sqlTableName.'</th><td class="text-right"><span class="badge badge-success">创建成功</span></td></tr>';
			} else {
				echo '<tr><th>'.$sqlTableName.'</th><td class="text-right"><span class="badge badge-danger">创建失败</span></td></tr>';
				$error = true;
			}
		} else {
			echo '<tr><th>'.$sqlTableName.'</th><td class="text-right"><span class="badge badge-warning">已存在</span></td></tr>';
		}
	}

        echo '</table>';

        echo '<form method="post" class="mt-3 mb-3">';
            if($error) echo '<input type="hidden" name="install_step_3_error" value="1"/>';
            echo '<a href="'.__INSTALL_URL__.'install.php" class="btn btn-info">重新检查</a> ';
            echo '<button type="submit" name="install_step_3_submit" value="continue" class="btn btn-success col-md-3">下一步</button>';
            echo '<a href="'.__INSTALL_URL__.'install.php?force=1" class="btn btn-danger float-right">删除表并重新创建</a>';
        echo '</form>';
        echo '</div>';
    echo '</div>';
} catch (Exception $ex) {
	echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';
}