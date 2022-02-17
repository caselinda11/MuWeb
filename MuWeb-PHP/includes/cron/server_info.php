<?php
/**
 * 服务器统计信息缓存执行文件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try{
    // 文件名
    $file_name = basename(__FILE__);

    $serverType = strtolower($config['server_files']);

    $totalAccounts = 0;     # 统计账号
    $totalCharacters = 0;   # 统计角色
    $totalGuilds = 0;       # 统计战盟
    $totalOnline = 0;       # 统计在线
    $totalGensD = 0;        # D家族统计
    $totalGensV = 0;        # V家族统计
    #取分区数据
    global $serverGrouping;
    foreach ($serverGrouping AS $code=>$value){
        $Db = ($value['SQL_USE_2_DB']) ? $value['SERVER_DB2_NAME'] : $value['SERVER_DB_NAME'];
        $countAccounts[] = Connection::Database('Me_MuOnline',$code)->query_fetch_single("SELECT COUNT(*) as totalAccounts FROM "._TBL_MI_." where servercode = ".$value['SERVER_GROUP']);
        $countCharacters[] = Connection::Database('MuOnline',$code)->query_fetch_single("SELECT COUNT(*) as totalCharacters FROM Character AS CharacterSystem LEFT JOIN [".$Db."].[dbo].MEMB_INFO AS AccountSystem ON AccountSystem.memb___id = CharacterSystem.AccountID where AccountSystem.servercode = ?",[$value['SERVER_GROUP']]);
        $countGuilds[] = Connection::Database('MuOnline',$code)->query_fetch_single("SELECT COUNT(Guild.G_Master) as totalGuilds FROM Guild LEFT JOIN Character on Guild.G_Master = Character.Name LEFT JOIN [".$Db."].[dbo].[MEMB_INFO] on MEMB_INFO.memb___id = Character.AccountID where MEMB_INFO.servercode = ?",[$value['SERVER_GROUP']]);
        $countOnline[] = Connection::Database('Me_MuOnline',$code)->query_fetch_single("SELECT COUNT(*) as totalOnline FROM MEMB_STAT as OlineSystem LEFT JOIN MEMB_INFO as AccountSystem ON AccountSystem.memb___id = OlineSystem.memb___id collate Chinese_PRC_CI_AS WHERE OlineSystem.ConnectStat = 1 and AccountSystem.servercode = ?",[$value['SERVER_GROUP']]);
        $countGens[] = Connection::Database("MuOnline",$code)->query_fetch_single("SELECT sum(case when "._CLMN_GENS_TYPE_." = 1 then 1 else 0 end) Gens1,sum(case when "._CLMN_GENS_TYPE_." = 2 then 1 else 0 end) Gens2 from "._TBL_GENS_);
    }
    if(!empty($countAccounts)) $totalAccounts = array_sum(array_column($countAccounts,'totalAccounts'));
    if(!empty($countCharacters)) $totalCharacters = array_sum(array_column($countCharacters,'totalCharacters'));
    if(!empty($countGuilds)) $totalGuilds = array_sum(array_column($countGuilds,'totalGuilds'));
    if(!empty($countOnline)) $totalOnline = array_sum(array_column($countOnline,'totalOnline'));
    if(!empty($countGens)) $totalGensD = array_sum(array_column($countGens,'Gens1'));
    if(!empty($countGens)) $totalGensV = array_sum(array_column($countGens,'Gens2'));

    #封装数组
    $serverInfo = [
        0 => $totalAccounts,
        1 => $totalCharacters,
        2 => $totalGuilds,
        3 => $totalOnline,
        4 => $totalGensD,
        5 => $totalGensV
    ];

    #更新写入到缓存文件
    if(is_array($serverInfo)) {
        $cacheDATA = implode(",",$serverInfo);
        updateCacheFile('server_info.cache',$cacheDATA);
    }

    # 更新数据库时间
    updateCronLastRun($file_name);
}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}