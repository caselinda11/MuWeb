<?php
/**
 * 排名缓存执行文件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try{
# 获取目录文件名
$file_name = basename(__FILE__);

# 引用排名类函数
$Rankings = new Rankings();

# 加载排名配置文件
loadModuleConfigs('rankings');

if(mconfig('active')) {
	if(mconfig('enable_gens')) {
		$Rankings->UpdateRankingCache('gens');
	}
}

# 更新数据库时间
updateCronLastRun($file_name);


}catch (Exception $exception){
    @error_log("[".date('Y-m-d h:i:s', time())."] [CRON][".$file_name."] ".$exception->getMessage(). "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
}