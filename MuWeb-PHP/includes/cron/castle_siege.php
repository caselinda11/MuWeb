<?php
/**
 * 罗兰攻城缓存执行文件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try{
    // 获取目录文件名
    $file_name = basename(__FILE__);

    global $serverGrouping;
    foreach ($serverGrouping as $code=>$item){
        # 攻城信息
        $db = Connection::Database('MuOnline',$code);
        $castleData[$code] = $db->query_fetch_single("SELECT t1."._CLMN_MCD_GUILD_OWNER_.", t2."._CLMN_GUILD_MASTER_.", CONVERT(varchar(max), t2."._CLMN_GUILD_LOGO_.", 2) as "._CLMN_GUILD_LOGO_.", t1."._CLMN_MCD_MONEY_.", t1."._CLMN_MCD_TRC_.", t1."._CLMN_MCD_TRS_.", t1."._CLMN_MCD_THZ_." FROM "._TBL_MUCASTLE_DATA_." as t1 INNER JOIN "._TBL_GUILD_." as t2 ON t2."._CLMN_GUILD_NAME_." = t1."._CLMN_MCD_GUILD_OWNER_);
        if(empty($castleData[$code])) continue;
        $data = array_filter($castleData);
        # 注册战盟
        if(!empty($data[$code])) {
            $RegData[$code] = $db->query_fetch("SELECT ["._CLMN_MCRS_GUILD_."] FROM "._TBL_MUCASTLE_RS_);
            if(empty($RegData[$code])) continue;

            foreach($RegData[$code] as $cData){
                $guildRegList[$code][] = $cData[_CLMN_MCRS_GUILD_];
            }
            if(empty($guildRegList[$code])) continue;

            $regData = array_filter($guildRegList);
        }
    }

    $result = [
        'castle' => $data,
        'guilds' => $regData
    ];

    if (!empty($result)){
        $cacheDATA = encodeCache($result);
        updateCacheFile('castle_siege.cache', $cacheDATA);
    }

    // 更新数据库缓存时间
    updateCronLastRun($file_name);
}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}