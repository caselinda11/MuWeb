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
        <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 75%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">75%</div>
    </div>
<?php
try {
	if(check_value($_POST['install_step_4_submit'])) {
		if(!check_value($_POST['install_step_4_error'])) {
		    #写入定时任务配置文件
            if (DIRECTORY_SEPARATOR == '\\') {
                $phpFile = dirname(__FILE__,4)."/Extensions/php/php".phpversion()."nts/php-win.exe";
                $cronFile = dirname(__FILE__,2)."/includes/cron/cron.php";
                $file = dirname(__FILE__,2)."\定时任务.bat";
                if(file_exists($file) && file_exists($phpFile) && file_exists($cronFile)){
                    $fileContent = $phpFile." -q ".$cronFile;
                    $bat = fopen($file, "w+") or die("定制任务程序写入失败!");
                    fwrite($bat,$fileContent);
                    fclose($bat);
                }else{
                    echo '<div class="alert alert-danger" role="alert">您需要手动写入定时任务配置文件。</div>';
                }
            }

			# 移动至下一步
			$_SESSION['install_cstep']++;
			header('Location: install.php');
			die();
		} else {
			echo '<div class="alert alert-danger" role="alert">错误已记录，无法继续。</div>';
		}
	}


    $muDb = new dB($_SESSION['SQL_DB_HOST'],$_SESSION['SQL_DB_PORT'], $_SESSION['SQL_DB_NAME'],$_SESSION['SQL_DB_USER'],$_SESSION['SQL_DB_PASS'],$_SESSION['SQL_PDO_DRIVER']);

	
	if($muDb->dead) {
		throw new Exception($muDb->error);
	}
	
	# 检测缓存文件列表
    global $install;
	foreach($install['cron_jobs'] as $key => $cron) {
		$cronPath = __PATH_CRON__ . $cron[2];
		if(!file_exists($cronPath)) throw new Exception('网站系统定时任务计划文件夹中缺少更多定时任务文件。');
		array_push($install['cron_jobs'][$key], md5_file($cronPath));
	}
	
	$error = false;
	#  collate Chinese_PRC_CI_AS 《=是否需要对数据库执行
	# 添加Cron数据
    echo '<div class="card">';
    echo '<div class="card-header">'.$install['step_list'][5][1].'</div>';
    echo '<table class="table table-striped">';
        echo '<tbody>';
	foreach($install['cron_jobs'] as $cron) {
		$cronExists = $muDb->query_fetch_single("SELECT * FROM [".X_TEAM_CRON."] WHERE cron_file_run = ?", array($cron[2]));
		if(!$cronExists) {
			$addCron = $muDb->query("INSERT INTO [".X_TEAM_CRON."] (cron_name,cron_description,cron_file_run,cron_run_time,cron_status,cron_protected,cron_file_md5) VALUES (?, ?, ?, ?, ?, ?, ?)", $cron);
			if($addCron) {
				echo '<tr><th>'.$cron[0].'- ['.$cron[2].']</th><td class="text-right"><span class="badge badge-success">添加</span></td></tr>';
			} else {
				echo '<tr><th>'.$cron[0].'- ['.$cron[2].']</th><td class="text-right"><span class="badge badge-danger">错误</span></td></tr>';
				$error = true;
			}
		} else {
			echo '<tr><th>'.$cron[0].'- ['.$cron[2].']</th><td class="text-right"><span class="badge badge-warning">已存在</span></td></tr>';
		}
	}
	    echo '</tbody>';
	echo '</table>';
        echo '<div class="form-group row justify-content-md-center">';
            echo '<form method="post">';
                if($error) echo '<input type="hidden" name="install_step_4_error" value="1"/>';
                echo '<a href="'.__INSTALL_URL__.'install.php" class="btn btn-warning mr-3">重新检查</a> ';
                echo '<button type="submit" name="install_step_4_submit" value="continue" class="btn btn-success">下一步</button>';
            echo '</form>';
        echo  '</div>';
	echo  '</div>';
} catch (Exception $ex) {
	echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';
	echo '<a href="'.__INSTALL_URL__.'install.php" class="btn btn-default">重新检查</a>';
}