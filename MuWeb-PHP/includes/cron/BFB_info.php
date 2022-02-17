<?php
/**
 * 冰风谷堡主统计信息
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try {
    // 文件名
    $file_name = basename(__FILE__);

    #服务器类型
    global $config;
    $serverType = strtolower($config['server_files']);

    if($serverType == "egames"){
        #取分区数据
        global $serverGrouping;
        foreach ($serverGrouping AS $code=>$item){
            $Db = ($item['SQL_USE_2_DB']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
            $re = Connection::Database('MuOnline',$code)->query_fetch_single("SELECT [GuildName],[GuildTT] FROM [BFB_SBK] WHERE [servercode] = ?",[$item['SERVER_GROUP']]);
            $logo = Connection::Database('MuOnline',$code)->query_fetch_single("SELECT CONVERT(varchar(max),G_Mark,2) AS G_Mark FROM [Guild] WHERE [G_Name] = ?",[$re['GuildName']]);
            $data[$code] = [
                $code,
                $re['GuildTT'],
                $re['GuildName'],
                $logo['G_Mark'],
            ];
        }

        #更新写入到缓存文件
        if(is_array($data)) {
            $cacheDATA = encodeCache($data);
            updateCacheFile('BFB_info.cache',$cacheDATA);
        }
    }

    # 更新数据库时间
    updateCronLastRun($file_name);
}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}
