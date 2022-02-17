<?php
/**
 * 安装程序
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();
if(check_value($_POST['install_step_1_submit'])) {
	try {
		# 移至下一步
		$_SESSION['install_cstep']++;
		header('Location: install.php');
		die();
	} catch (Exception $ex) {

		echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';

	}
}
?>
<div class="progress mb-3">
    <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 15%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">15%</div>
</div>
    <div class="card"> <?global $install;?>
    <div class="card-header"><?=$install['step_list'][1][1]?></div>
    <div class="">
<?php
echo '<table class="table table-striped">';
echo '<tbody>';

    $chk_1 = version_compare(PHP_VERSION, '5.6', '>=');
    $check_1 = ($chk_1 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>PHP版本要求: 5.6 或者更高</th>';
        echo '<td>[当前PHP '.PHP_VERSION.']</td>';
        echo '<td class="text-right">'.$check_1.'</td>';
    echo '</tr>';
    $chk_2 = (ini_get('short_open_tag') == 1 ? true : false);
    $check_2 = ($chk_2 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>short_open_tag</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_2.'</td>';
    echo '</tr>';

    $chk_3 = (extension_loaded('openssl') ? true : false);
    $check_3 = ($chk_3 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>OpenSSL 扩展</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_3.'</td>';
    echo '</tr>';

    $chk_4 = (extension_loaded('curl') ? true : false);
    $check_4 = ($chk_4 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>cURL 扩展</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_4.'</td>';
    echo '</tr>';

    $chk_6 = (extension_loaded('sockets') ? true : false);
    $check_6 = ($chk_6 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>Socket</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_6.'</td>';
    echo '</tr>';

    $chk_5 = (extension_loaded('pdo') ? true : false);
    $check_5 = ($chk_5 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>PDO</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_5.'</td>';
    echo '</tr>';

if($chk_5) {
    if(DIRECTORY_SEPARATOR != '\\') {
        $chk_6 = (extension_loaded('pdo_dblib') ? true : false);
        $check_6 = ($chk_6 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
        echo '<tr>';
            echo '<th>PDO dblib (linux)</th>';
            echo '<td></td>';
            echo '<td class="text-right">'.$check_6.'</td>';
        echo '</tr>';
    }
    $chk_7 = (extension_loaded('pdo_odbc') ? true : false);
    $check_7 = ($chk_7 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-warning">可选使用</span>');
    echo '<tr>';
        echo '<th>PDO ODBC [linux/windows]</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_7.'</td>';
    echo '</tr>';

    $chk_8 = (extension_loaded('pdo_sqlsrv') ? true : false);
    $check_8 = ($chk_8 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>PDO Sqlsrv [Windows]</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_8.'</td>';
    echo '</tr>';
}

    $chk_9 = (extension_loaded('json') ? true : false);
    $check_9 = ($chk_9 ? '<span class="badge badge-success">正确</span>' : '<span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>JSON</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$check_9.'</td>';
    echo '</tr>';

    $chk_10 = file_get_contents(__BASE_URL__ . 'includes/config/system.json');
    $chk_10_code = explode(' ', $http_response_header[0]);

    $chk_10 = ($chk_10_code[1] == '403' ? '<span class="badge badge-success">正确</span>' : '（每个人都可以访问私有目录） <span class="badge badge-danger">错误</span>');
    echo '<tr>';
        echo '<th>Apache .htaccess</th>';
        echo '<td></td>';
        echo '<td class="text-right">'.$chk_10.'</td>';
    echo '</tr>';
echo '</tbody>';
echo '</table>';

echo '<p class="text-center text-danger">[强烈建议您在继续操作之前先解决上述所有问题!]</p>';
echo '<form class="form-group row justify-content-md-center" action="" method="post">';
//		$mysql_support = function_exists('sqlsrv_connect');
//		$pdo_mysql_support = extension_loaded('pdo_sqlsrv');
//		$myisam_support = extension_loaded('pdo_sqlsrv');
//		$innodb_support = extension_loaded('pdo_sqlsrv');
	echo '<button type="submit" name="install_step_1_submit" value="ok" class="btn btn-success mr-3 col-md-3">下一步</button>';
    echo '<a href="'.__INSTALL_URL__.'install.php" class="btn btn-primary">重新检查</a>';
echo '</form>';
?>
    </div>
</div>
