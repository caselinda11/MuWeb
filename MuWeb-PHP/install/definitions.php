<?php
/**
 * 安装程序
 *  常规定义
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();

/**
 * 安装版本
 */
define('INSTALLER_VERSION', '2.1.0');

/**
 * 可写权限目录
 */
define('X_TEAM_WRITABLE_PATHS_FILE', 'writable.paths.json');

/**
 * 协议类型
 */
$install['PDO_DSN'] = [
	1 => 'dblib',
	2 => 'sqlsrv',
	3 => 'odbc',
];

/**
 * 数据库表名
 */
$install['sql_list'] = [
	'X_TEAM_BANS' => X_TEAM_BANS,
	'X_TEAM_BAN_LOG' => X_TEAM_BAN_LOG,
	'X_TEAM_BLOCKED_IP' => X_TEAM_BLOCKED_IP,
	'X_TEAM_CREDITS_CONFIG' => X_TEAM_CREDITS_CONFIG,
	'X_TEAM_CREDITS_LOGS' => X_TEAM_CREDITS_LOGS,
	'X_TEAM_CRON' => X_TEAM_CRON,
	'X_TEAM_DOWNLOADS' => X_TEAM_DOWNLOADS,
	'X_TEAM_FLA' => X_TEAM_FLA,
	'X_TEAM_NEWS' => X_TEAM_NEWS,
	'X_TEAM_PASSCHANGE_REQUEST' => X_TEAM_PASSCHANGE_REQUEST,
	'X_TEAM_PLUGINS' => X_TEAM_PLUGINS,
	'X_TEAM_REGISTER_ACCOUNT' => X_TEAM_REGISTER_ACCOUNT,
	'X_TEAM_VOTES' => X_TEAM_VOTES,
	'X_TEAM_VOTE_LOGS' => X_TEAM_VOTE_LOGS,
	'X_TEAM_VOTE_SITES' => X_TEAM_VOTE_SITES,
	'X_TEAM_ACCOUNT' => X_TEAM_ACCOUNT,
	'X_TEAM_COMBINE_SERVER' => X_TEAM_COMBINE_SERVER,
	'X_TEAM_TOAST' => X_TEAM_TOAST,
];

$install['sql_server_list'] = [
    'X_TEAM_ACCOUNT',
    'X_TEAM_BANS',
    'X_TEAM_BAN_LOG',
    'X_TEAM_VOTE_LOGS',
    'X_TEAM_TOAST',
];

/**
 * 安装步骤
 */
$install['step_list'] = [
	['install_intro.php',  '[1] 许可协议'],
	['install_step_1.php', '[2] 服务器要求'],
	['install_step_2.php', '[3] 可读写路径'],
	['install_step_3.php', '[4] 网站数据库配置'],
	['install_step_4.php', '[5] 创建网站数据表'],
	['install_step_5.php', '[6] 创建网站定时任务'],
	['install_step_6.php', '[7] 游戏数据库配置'],
	['install_step_7.php', '[8] 配置管理员'],
];

/**
 * 缓存文件数据
 */
$install['cron_jobs'] = [
    # 缓存名, 缓存描述, 缓存文件, 缓存时间, 缓存状态, 保护, MD5
	# cron_name,cron_description,cron_file_run,cron_run_time,cron_status,cron_protected,cron_file_md5
	['等级排行','定时缓存等级排行数据','levels_ranking.php','300','1','0'],
	['家族排行','定时缓存家族排行数据','gens_ranking.php','300','1','0'],
	['战盟排行','定时缓存战盟排行数据','guilds_ranking.php','300','1','0'],
	['推广排名','定时缓存推广排名数据','votes_ranking.php','300','1','0'],
	['罗兰城主','定时缓存罗兰城主数据','castle_siege.php','300','1','0'],
	['禁用系统','定时解除限时封停的账号','temporal_bans.php','300','1','0'],
	['服务器信息','定时缓存统计服务器信息','server_info.php','300','1','0'],
];