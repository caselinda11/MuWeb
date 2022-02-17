<?php
/**
 * 安装程序第三步
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();

if(check_value($_POST['submit'])) {
	try {
		$_SESSION['SQL_DB_HOST'] = $_POST['SQL_DB_HOST'];
		if(!check_value($_POST['SQL_DB_HOST'])) throw new Exception('您必须填写服务器数据库地址~');
		
		$_SESSION['SQL_DB_PORT'] = $_POST['SQL_DB_PORT'];
		if(!check_value($_POST['SQL_DB_PORT'])) throw new Exception('您必须填写服务器数据库端口~');

        $_SESSION['SQL_DB_NAME'] = $_POST['SQL_DB_NAME'];
        if(!check_value($_POST['SQL_DB_NAME'])) throw new Exception('您必须填写服务器数据库名称~');

        $_SESSION['SQL_DB_USER'] = $_POST['SQL_DB_USER'];
		if(!check_value($_POST['SQL_DB_USER'])) throw new Exception('您必须填写服务器数据库端口~');
		
		$_SESSION['SQL_DB_PASS'] = $_POST['SQL_DB_PASS'];
		if(!check_value($_POST['SQL_DB_PASS'])) throw new Exception('您必须填写服务器数据库密码~');

		$_SESSION['SQL_PDO_DRIVER'] = $_POST['SQL_PDO_DRIVER'];
		if(!check_value($_POST['SQL_PDO_DRIVER'])) throw new Exception('您必须选择服务器数据库协议~');
		global $install;
        if(!array_key_exists($_POST['SQL_PDO_DRIVER'], $install['PDO_DSN'])) throw new Exception('数据库协议错误！');

		$_SESSION['SQL_ENABLE_MD5'] = (check_value($_POST['SQL_ENABLE_MD5']) ? true : false);

        # 测试连接网站数据库
        $create = new dB($_SESSION['SQL_DB_HOST'],$_SESSION['SQL_DB_PORT'], "master",$_SESSION['SQL_DB_USER'],$_SESSION['SQL_DB_PASS'],$_SESSION['SQL_PDO_DRIVER']);
        if($create->dead){
            throw new Exception($create->error);
        }

        $create->query("CREATE DATABASE ".$_SESSION['SQL_DB_NAME']."");

		# 移动至下一步
		$_SESSION['install_cstep']++;
		header('Location: install.php');
		die();
	} catch (Exception $ex) {
		echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';
	}
}
?>
<div class="progress mb-3">
    <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 45%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">45%</div>
</div>
<div class="card">  <?global $install?>
    <div class="card-header"><?=$install['step_list'][3][1]?></div>
    <div class="card-body">
        <form class="form-horizontal" method="post">
            <div class="form-group row justify-content-md-center">
                <label for="SQL_DB_HOST" class="col-sm-2 text-right control-label">数据库地址</label>
                <input type="text" name="SQL_DB_HOST" class="form-control col-sm-4" id="SQL_DB_HOST" value="<?php echo (check_value($_SESSION['install_sql_host']) ? $_SESSION['install_sql_host'] : '127.0.0.1'); ?>">
                <small id="SQL_DB_HOST" class="form-text text-muted col-sm-2">您服务器IP地址</small>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="SQL_DB_PORT" class="col-sm-2 text-right control-label">数据库端口</label>
                <input type="text" name="SQL_DB_PORT" class="form-control col-sm-4" id="SQL_DB_PORT" value="<?php echo (check_value($_SESSION['install_sql_port']) ? $_SESSION['install_sql_port'] : '1433'); ?>">
                <small id="SQL_DB_PORT" class="form-text text-muted col-sm-2">一般默认1433</small>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="SQL_DB_NAME" class="col-sm-2 text-right control-label">数据库名称</label>
                <input type="text" name="SQL_DB_NAME" class="form-control col-sm-4" id="SQL_DB_NAME" value="<?php echo (check_value($_SESSION['install_sql_db1']) ? $_SESSION['install_sql_db1'] : 'X_MuWeb'); ?>">
                <small id="SQL_DB_NAME" class="form-text text-muted col-sm-2">*自动创建</small>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="SQL_DB_USER" class="col-sm-2 text-right control-label">数据库账号</label>
                <input type="text" name="SQL_DB_USER" class="form-control col-sm-4" id="SQL_DB_USER" value="<?php echo (check_value($_SESSION['install_sql_user']) ? $_SESSION['install_sql_user'] : 'sa'); ?>">
                <small id="SQL_DB_USER" class="form-text text-muted col-sm-2">一般默认sa</small>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="SQL_DB_PASS" class="col-sm-2 text-right control-label">数据库密码</label>
                <input type="text" name="SQL_DB_PASS" class="form-control col-sm-4" id="SQL_DB_PASS" value="<?php echo (check_value($_SESSION['install_sql_pass']) ? $_SESSION['install_sql_pass'] : null); ?>">
                <small id="SQL_DB_PASS" class="form-text text-muted col-sm-2">您的数据库密码</small>
            </div>

            <div class="form-group row justify-content-md-center">
                <label for="SQL_PDO_DRIVER" class="col-sm-2 text-right control-label">服务器协议</label>
                <?
                $serverType = (DIRECTORY_SEPARATOR == '\\') ? 'selected' : '';
                ?>
                <select name="SQL_PDO_DRIVER" id="SQL_PDO_DRIVER" class="form-control col-sm-4">
                    <option value="1" <?=$serverType?>>PDO_Dblib (Linux)</option>
                    <option value="2" <?=$serverType?>>PDO_SqlSrv (Windows)</option>
                    <option value="3" >ODBC</option>
                </select>
                <small id="SQL_PDO_DRIVER" class="form-text text-muted col-sm-2">*</small>
            </div>
            <div class="form-group row justify-content-md-center">
                <div class="custom-control custom-switch">
                    <input type="checkbox" name="SQL_ENABLE_MD5" value="1" class="custom-control-input" id="SQL_ENABLE_MD5" <?php if($_SESSION['install_sql_md5'] == 1) echo 'checked'; ?>>
                    <label class="custom-control-label" for="SQL_ENABLE_MD5">
                        是否使用MD5
                    </label>
                </div>
            </div>
            <div class="form-group row justify-content-md-center">
                <button type="submit" name="submit" value="continue" class="btn btn-success col-md-3 mr-3">下一步</button>
            </div>
        </form>
    </div>
</div>