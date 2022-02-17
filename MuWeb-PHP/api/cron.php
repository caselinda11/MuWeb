<?php
/**
 * 加载CRON API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

// 访问定义
define('access', 'api');

try {
	// 引入网站系统
	if(!@include_once('../includes/Init.php')) throw new Exception('无法加载网站系统文件!');

	// 检查状态
	if(config('cron_api') == false) throw new Exception('定时任务计划API已禁用！');
	if(!check_value(config('cron_api_key'))) throw new Exception('配置的定时任务计划API密钥无效。');
	
	// 检测密钥
	if(!check_value($_REQUEST['key'])) throw new Exception('密钥无效!');
	if($_REQUEST['key'] != config('cron_api_key')) throw new Exception('API密钥无效!!');
	
	// 获取缓存列表
	$cronList = getCronList();
	if(!is_array($cronList)) throw new Exception('没有定时任务计划!');
	
	// 封装引入
	function loadCronFile($path) {
		include($path);
	}

	// 执行缓存
	foreach($cronList as $cron) {
		if($cron['cron_status'] != 1) continue;
		if(!check_value($cron['cron_last_run'])) {
			$lastRun = $cron['cron_run_time'];
		} else {
			$lastRun = $cron['cron_last_run']+$cron['cron_run_time'];
		}
		if(time() > $lastRun) {
			$filePath = __PATH_INCLUDES_CRON__.$cron['cron_file_run'];
			if(file_exists($filePath)) {
				loadCronFile($filePath);
				$executedCron[] = $cron['cron_file_run'];
			}
		}
	}
	if(empty($executedCron)) throw new Exception('加载错误!');
	http_response_code(200);
	header('Content-Type: application/json');
	echo json_encode(array('code' => 200, 'message' => '执行定时任务计划成功。', 'executed' => $executedCron));
	
} catch(Exception $ex) {
	http_response_code(500);
	header('Content-Type: application/json');
	echo json_encode(array('code' => 500, 'error' => $ex->getMessage()));
}