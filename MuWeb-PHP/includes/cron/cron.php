<?php
/**
 * 定时缓存任务文件
 * 使用说明:添加到php.exe内执行
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
// 访问
define('access', 'cron');
try{

    // 加载初始化文件
    if(!@include_once(str_replace('\\','/',dirname(dirname(__FILE__))).'/' . 'Init.php')) die('加载初始化系统文件失败!');

    // 封装过程
    function loadCronFile($path) {
        include($path);
    }

    // 读取缓存列表
    $cronList = getCronList();
    if(!is_array($cronList)) die();
	
    // 循环执行缓存任务
    foreach($cronList as $cron) {
        #检查是否开放状态
        if($cron['cron_status'] != 1) continue;

        if(!check_value($cron['cron_last_run'])) {
            $lastRun = $cron['cron_run_time'];
        } else {
            $lastRun = $cron['cron_last_run'] + $cron['cron_run_time'];
        }
        #计算时间
        if($lastRun < time()) {
            $filePath = __PATH_INCLUDES_CRON__.$cron['cron_file_run'];
		    if(file_exists($filePath)) {
                loadCronFile($filePath);
            }
        }
    }

}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}