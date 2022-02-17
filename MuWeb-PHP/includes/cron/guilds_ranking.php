<?php
/**
 * 战盟缓存执行文件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try{
    // 获取目录文件名
    $file_name = basename(__FILE__);

    // 引用排名类函数
    $Rankings = new Rankings();

    // 加载排名配置文件
    loadModuleConfigs('rankings');

    if(mconfig('active')) {
        if(mconfig('enable_guilds')) {
            $Rankings->UpdateRankingCache('guilds');
        }
    }

    // 更新缓存时间
    updateCronLastRun($file_name);

}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}