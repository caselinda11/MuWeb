<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try{
    // 加载文件名
    $file_name = basename(__FILE__);

    // 加载数据库
    $database = Connection::Database('Web');

    $temporalBans = $database->query_fetch("SELECT * FROM ".X_TEAM_BANS);
    if(is_array($temporalBans)) {
        foreach($temporalBans as $tempBan) {
            $banTimestamp = $tempBan['ban_days']*86400+$tempBan['ban_date'];
            if(time() > $banTimestamp) {
                // lift ban
                $unban = $database->query("UPDATE "._TBL_MI_." SET "._CLMN_BLOCCODE_." = 0 WHERE "._CLMN_USERNM_." = ?", [$tempBan['account_id']]);
                if($unban) {
                    $database->query("DELETE FROM ".X_TEAM_BAN_LOG." WHERE account_id = ?", [$tempBan['account_id']]);
                    $database->query("DELETE FROM ".X_TEAM_BANS." WHERE account_id = ?", [$tempBan['account_id']]);
                }
            }
        }
    }

    // 更新数据库缓存时间
    updateCronLastRun($file_name);

}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}