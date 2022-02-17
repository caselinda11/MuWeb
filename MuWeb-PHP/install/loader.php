<?php
/**
 * 安装程序
 * 初始化加载
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();

session_name('XTeamFrameworkInstall');
session_start();
ob_start();

@ini_set('default_charset', 'UTF-8');

define('HTTP_HOST', $_SERVER['HTTP_HOST']);
define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://');
define('__ROOT_DIR__', str_replace('\\','/',dirname(dirname(__FILE__))).'/');
define('__RELATIVE_ROOT__', str_ireplace(rtrim(str_replace('\\','/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__));// /
define('__BASE_URL__', SERVER_PROTOCOL.HTTP_HOST.__RELATIVE_ROOT__);
define('__PATH_INCLUDES__', __ROOT_DIR__.'includes/');
define('__PATH_CLASSES__', __PATH_INCLUDES__.'classes/');
define('__PATH_CRON__', __PATH_INCLUDES__.'cron/');
define('__PATH_CONFIGS__', __PATH_INCLUDES__.'config/');
define('__INSTALL_ROOT__', __ROOT_DIR__ . 'install/');
define('__INSTALL_URL__', __BASE_URL__ . 'install/');
define('__PATH_INCLUDES_CACHE__', __PATH_INCLUDES__.'cache/');
define('__PATH_INCLUDES_CACHE_NEWS__', __PATH_INCLUDES_CACHE__.'news/');
define('__PATH_PUBLIC__', __BASE_URL__.'public/');				//公共路径
define('__PATH_PUBLIC_CSS__', __PATH_PUBLIC__.'css/');			//公共css目录
define('__PATH_PUBLIC_JS__', __PATH_PUBLIC__.'js/');			//公共js目录

try {
	if(!@include(__PATH_CONFIGS__ . 'system.tables.php')) throw new Exception('无法加载网站程序数据库表定义!');
	if(!@include(__INSTALL_ROOT__ . 'definitions.php')) throw new Exception('无法加载网站程序安装程序定义!');

    $ConfigsPath = __PATH_CONFIGS__.'system.json';
    if(!file_exists($ConfigsPath)) throw new Exception('网站程序缺少配置文件!');
    if(!is_readable($ConfigsPath)) throw new Exception('网站程序配置文件不可读!');
    if(!is_writable($ConfigsPath)) throw new Exception('网站程序配置文件不可写!');

    $serverPath = __PATH_CONFIGS__.'server.json';
    if(!file_exists($serverPath)) throw new Exception('网站程序缺少分区配置文件!');
    if(!is_readable($serverPath)) throw new Exception('网站程序分区配置文件不可读!');
    if(!is_writable($serverPath)) throw new Exception('网站程序分区配置文件不可写!');

    $ConfigsFile = file_get_contents($ConfigsPath);
    if($ConfigsFile) {
        $Config = json_decode($ConfigsFile, true);
        if(!is_array($Config)) throw new Exception('系统配置文件加载失败!');
        if($Config['system_status'] == true) throw new Exception('网站程序安装完成后，建议重命名或删除此目录！');
    }
    #加载系统默认配置文件
    $ConfigsPathDefault = __PATH_CONFIGS__.'system.json.default';
    if(!file_exists($ConfigsPathDefault)) throw new Exception('网站程序缺少默认配置文件!');
    if(!is_readable($ConfigsPathDefault)) throw new Exception('网站程序默认配置文件不可读!');
    $ConfigsFileDefault = file_get_contents($ConfigsPathDefault);

    if(!$ConfigsFileDefault) throw new Exception('网站程序无法加载默认配置文件.');

    $DefaultConfig = json_decode($ConfigsFileDefault, true);
    if(!is_array($DefaultConfig)) throw new Exception('系统配置文件加载失败!');

    if(!@include_once(__PATH_INCLUDES__ . 'functions.php')) throw new Exception('无法加载助手函数![functions]');
    if(!@include_once(__PATH_CLASSES__ . 'class.validator.php')) throw new Exception('无法加载验证基类![class.validator.php]');
    if(!@include_once(__PATH_CLASSES__ . 'class.database.php')) throw new Exception('无法加载数据基类![class.database.php]');
    if(!@include_once(__PATH_CONFIGS__ . 'GamesType.php')) throw new Exception('无法加载游戏版本兼容程序![GamesType.php]');
    # 时区
    date_default_timezone_set('Asia/Shanghai');

	$writablePaths = loadJsonFile(__PATH_CONFIGS__.X_TEAM_WRITABLE_PATHS_FILE);
	if(!is_array($writablePaths)) throw new Exception('无法加载网站程序可写路径列表.');
	
	if(!check_value($_SESSION['install_cstep'])) {
		$_SESSION['install_cstep'] = 0;
	}

	function stepListSidebar() {
		global $install;
		if(is_array($install['step_list'])) {
			echo '<ul class="list-group">';
            echo '<li class="list-group-item bg-light">安装步骤</li>';
			foreach($install['step_list'] as $key => $row) {
				if($key == $_SESSION['install_cstep']) {
					echo '<li class="list-group-item active">'.$row[1].'</li>';
					continue;
				}
				echo '<li class="list-group-item">'.$row[1].'</li>';
			}
			echo '</ul>';
			echo '<br>';
		}
		if($_SESSION['install_cstep'] > 0) {

			echo '<a href="?action=restart" class="col-md-12 btn btn-danger">重新开始</a>';

		}
	}

	if(check_value($_GET['action'])) {
		if($_GET['action'] == 'restart') {
			# 重启安装过程
			$_SESSION = array();
			session_destroy();
			header('Location: install.php');
			die();
		}
	}
	
} catch (Exception $ex) {
	die($ex->getMessage());
}