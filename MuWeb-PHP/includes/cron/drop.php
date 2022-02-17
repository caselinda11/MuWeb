<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.1.0
 *
 **/

try{
    // 获取目录文件名
    $file_name = basename(__FILE__);

    $drop = new \Plugin\drop();
    $tConfig = $drop->loadConfig();
    if(!$tConfig['active']) exit;
    $lastFile = "";  //保存最后一次操作的文件名
    $lastLine = 0;   //最后一行
    $lastMD5  = "";  //最后一个文件的MD5值

    $redis = new Redis();
    $redis->connect($tConfig['redis_ip'], $tConfig['redis_port']);
    if($tConfig['redis_pass'])$redis->auth($tConfig['redis_pass']);#密码验证
    #识别目录所有日志文件
    $logFilePath = $drop->loadFile($tConfig['log_path']);
    #如果上次执行过,
    $last = $redis->hGetAll("drop-last");
    if($last && is_array($last)){
        $lasts= [];
        foreach($last as $key=>$val) {array_push($lasts,unserialize($val));}
        #判断上次文件内容是否增加过
        if(md5_file(end($logFilePath)) == $cfg['MD5']) exit;
        $lastFile = $lasts['file'];
        $lastMD5 = md5_file($lasts['file']);
	   
 	    $log = new SplFileObject($lasts['file']);
        foreach($log as $line => $content){
            if ($line <= $cfg['line']) continue;
            $content = get_gb_to_utf8($content);
            if(!strpos($content,'丢弃编号')) continue;
            $redis->lPush("drop-list",substr(strstr(strrchr($file,'/'),"_",1),1).$content);
            $lastLine = $line;
        }
    }else{
        foreach($logFilePath as $name => $file){
            $lastFile = $file;
            $lastMD5 = md5_file($file);
            $log = new SplFileObject($file);
            foreach($log as $line => $content){
                $content = get_gb_to_utf8($content);
                if(!strpos($content,'丢弃编号')) continue;
                $redis->lPush("drop-list",$name.$content);
                $lastLine = $line;
            }
        }
    }

    #把最后读取的文件缓存下来
    if($lastFile && $lastMD5 && $lastLine) {
        $json = ["file" => $lastFile, "line" => $lastLine, "MD5" => $lastMD5];
        foreach($json as $key=>$val) {$redis->hSet("drop-last",$key,serialize($val));}
    }

    // 更新数据库缓存时间
    updateCronLastRun($file_name);

}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}

