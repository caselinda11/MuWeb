<?php
/**
 * 系统初始化
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

//session_name('XTeamFramework'); # session访问名称（更改为服务器名称和取消注释）
//session_set_cookie_params(0, '/', 'x-mu.cn'); # 使用和不使用www协议的同一访问（使用您的域并取消注释进行编辑）
    if (access != 'cron') {
        @ob_start();
       session_start();
    }

# 版本信息
    define('__X_TEAM_VERSION__', '2.1.0');
# 网站编码
    @ini_set('default_charset', 'UTF-8');
# 授权信息
    define('KEY','6SZqS/DPZkA9OtxDl57XxSk');
# CloudFlare IP 解决方案
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
# CloudFlare HTTPS 解决方案
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) $_SERVER['HTTPS'] = $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'on' : 'off';

# 全局路径
    define('HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'CLI');
    define('SERVER_PROTOCOL', (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') ? 'https://' : 'http://');
    define('__ROOT_DIR__', str_replace('\\', '/', dirname(dirname(__FILE__))) . '/'); // /home/user/public_html/
    define('__RELATIVE_ROOT__', (!empty($_SERVER['SCRIPT_NAME'])) ? str_ireplace(rtrim(str_replace('\\', '/', realpath(str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['SCRIPT_FILENAME']))), '/'), '', __ROOT_DIR__) : '/');// /
    define('__BASE_URL__', SERVER_PROTOCOL . HTTP_HOST . __RELATIVE_ROOT__); // http(s)://www.mysite.com/
#使用套套强制域名
//define('__BASE_URL__', SERVER_PROTOCOL.'域名(不需要端口)'.__RELATIVE_ROOT__); // http(s)://www.mysite.com/

# 私有路径
    define('__PATH_ADMINCP__', __ROOT_DIR__ . 'admincp/');
    define('__PATH_ADMINCP_INC__', __ROOT_DIR__ . 'admincp/inc/');
    define('__PATH_ADMINCP_MODULES__', __ROOT_DIR__ . 'admincp/modules/');
    define('__PATH_INCLUDES__', __ROOT_DIR__ . 'includes/');
    define('__PATH_INCLUDES_EMAILS__', __PATH_INCLUDES__ . 'emails/');
    define('__PATH_INCLUDES_LANGUAGES__', __PATH_INCLUDES__ . 'languages/');
    define('__PATH_INCLUDES_CRON__', __PATH_INCLUDES__ . 'cron/');
    define('__PATH_INCLUDES_CLASSES__', __PATH_INCLUDES__ . 'classes/');
    define('__PATH_INCLUDES_LOGS__', __PATH_INCLUDES__ . 'logs/');
    define('__PATH_INCLUDES_CACHE__', __PATH_INCLUDES__ . 'cache/');
    define('__PATH_INCLUDES_CACHE_NEWS__', __PATH_INCLUDES_CACHE__ . 'news/');
    define('__PATH_INCLUDES_CACHE_NEWS_TRANSLATIONS__', __PATH_INCLUDES_CACHE_NEWS__ . 'translations/');
    define('__PATH_INCLUDES_PLUGINS__', __PATH_INCLUDES__ . 'plugins/');
    define('__PATH_INCLUDES_CONFIGS__', __PATH_INCLUDES__ . 'config/');
    define('__PATH_INCLUDES_CONFIGS_MODULE__', __PATH_INCLUDES_CONFIGS__ . 'modules/');
    define('__PATH_MODULES__', __ROOT_DIR__ . 'modules/');
    define('__PATH_MODULES_USERCP__', __PATH_MODULES__ . 'usercp/');
    define('__PATH_TEMPLATES__', __ROOT_DIR__ . 'templates/');

# 公共路径
    define('__PATH_MODULES_RANKINGS__', __BASE_URL__ . 'rankings/');    //排名
    define('__PATH_ADMINCP_HOME__', __BASE_URL__ . 'admincp/');        //管理员面板
    define('__PATH_PUBLIC__', __BASE_URL__ . 'public/');                //公共路径
    define('__PATH_PUBLIC_CSS__', __PATH_PUBLIC__ . 'css/');            //公共css目录
    define('__PATH_PUBLIC_JS__', __PATH_PUBLIC__ . 'js/');            //公共js目录
    define('__PATH_PUBLIC_FONT__', __PATH_PUBLIC__ . 'fonts/');        //公共字体目录
    define('__PATH_PUBLIC_IMG__', __PATH_PUBLIC__ . 'img/');                //公共图片
    define('__PATH_COUNTRY_FLAGS__', __PATH_PUBLIC_IMG__ . 'flags/');        //公共国旗
    define('__PATH_API__', __BASE_URL__ . 'api/');                        //公共API
    define('__PATH_ONLINE_STATUS__', __PATH_PUBLIC_IMG__ . 'online.png');        //在线图标
    define('__PATH_OFFLINE_STATUS__', __PATH_PUBLIC_IMG__ . 'offline.png');    //离线图标

# 其他路径
    define('X_TEAM_DATABASE_ERRORLOG', __PATH_INCLUDES_LOGS__ . 'database_errors.log');
    define('X_TEAM_WRITABLE_PATHS', __PATH_INCLUDES_CONFIGS__ . 'writable.paths.json'); #写入权限路径列表

# X-TEAM CMS 数据表
    if (!@include_once(__PATH_INCLUDES_CONFIGS__ . 'system.tables.php')) throw new Exception('无法加载网站程序系统数据表文件[system.tables.php]!');

# 时区
    date_default_timezone_set('Asia/Shanghai');
# 加载助手函数
    if (!@include_once(__PATH_INCLUDES__ . 'functions.php')) throw new Exception('无法加载Functions!');
//# 加载库
    if (!@include_once(__ROOT_DIR__.'vendor/autoload.php')) throw new Exception('无法加载数据类函数[autoload]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.database.php')) throw new Exception('无法加载数据类函数[database]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.common.php')) throw new Exception('无法加载基础类函数[common]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.handler.php')) throw new Exception('无法加载系统类函数[handler]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.validator.php')) throw new Exception('无法加载效验类函数[validator]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.login.php')) throw new Exception('无法加载登录类函数[login]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.vote.php')) throw new Exception('无法加载推广类函数[vote]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.character.php')) throw new Exception('无法加载角色类函数[character]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.rankings.php')) throw new Exception('无法加载排名类函数[rankings]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.news.php')) throw new Exception('无法加载新闻类函数[news]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.plugins.php')) throw new Exception('无法加载插件类函数[plugins]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.profiles.php')) throw new Exception('无法加载个人类函数[profiles]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.credits.php')) throw new Exception('无法加载货币类函数[credits]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.email.php')) throw new Exception('无法加载邮箱类函数[email]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.account.php')) throw new Exception('无法加载账户类函数[account]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.connection.php')) throw new Exception('无法加载连接类函数[connection]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.machine.php')) throw new Exception('无法加载机器码类函数[machine]!');
 //这里类库
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.token.php')) throw new Exception('无法加载令牌类函数[token]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.warehouse.php')) throw new Exception('无法加载仓库类函数[warehouse]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.sms.php')) throw new Exception('无法加载短信类函数[sms]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.pagination.php')) throw new Exception('无法加载类分页函数[pagination]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.GeeTestLib.php')) throw new Exception('无法加载类验证码函数[GeetestLib]!');
    if (!@include_once(__PATH_INCLUDES_CLASSES__ . 'class.gm_inventory.php')) throw new Exception('无法加载类验证码函数[GeetestLib]!');
    
	
	
	# CMS配置
    $config = webConfigs();
    $serverGrouping = GroupConfig();
    # 错误报告
    if ($config['error_reporting']) {
       // ini_set('display_errors', true);
       // error_reporting(E_ALL & ~E_NOTICE);
    } else {
       // ini_set('display_errors', false);
       // error_reporting(0);
    }

	  ini_set('display_errors', true);
       error_reporting(E_ALL & ~E_NOTICE);
	   
	   
    $map = (include_once __PATH_INCLUDES_CONFIGS__.'map.php');  #加载地图

    # 兼容配置
    if (!@include_once(__PATH_INCLUDES_CONFIGS__ . 'GamesType.php')) throw new Exception('无法加载数据库兼容配置文件[GamesType.php]!');
    global $Games;
    if (!array_key_exists(strtolower($config['server_files']), $Games['serverType'])) throw new Exception('游戏数据库兼容配置文件无效[GamesType.php]!');
    # 加载数据表定义
    if (!@include_once(__PATH_INCLUDES_CONFIGS__ . 'table/' . $Games['serverType'][strtolower($config['server_files'])]['file'])) throw new Exception('严重错误::无法加载表定义[' . $Games['serverType'][strtolower($config['server_files'])]['file'] . ']!');

# 默认模板
    if (!file_exists(__PATH_TEMPLATES__ . $config['website_template'])) throw new Exception('严重错误::默认模板美化文件不存在!');

# CMS 状态
    if (!$config['system_active'] && access != 'cron') {
        if (!array_key_exists($_SESSION['username'], $config['admins'])) {
            header('Location: ' . $config['maintenance_page']);
            die();
        }
        # 向管理员显示网站状态
        echo '<div style="text-align:center;border-bottom:1px solid #aa0000;padding:15px;background:#000;color:#ff0000;font-size:12pt;">';
            echo '离线模式';
        echo '</div>';
    }

# IP封锁系统
    if ($config['ip_block_system_enable']) if (checkBlockedIp()) throw new Exception('您的IP地址已被禁止访问!');


# 加载插件列表
    if ($config['plugins_system_enable']) {
        $pluginsCache = loadCache('plugins.cache');
        if (is_array($pluginsCache)) {
            foreach ($pluginsCache as $pluginData) {
                if (!is_array($pluginData['files'])) continue;
                foreach ($pluginData['files'] as $pluginFile) {
                    try {
                        if (!@include_once(__PATH_INCLUDES_PLUGINS__ . $pluginData['folder'] . '/' . $pluginFile)) throw new Exception('无法加载插件 [' . $pluginData['folder'] . '/' . $pluginFile . '].');
                    } catch (Exception $exception) {
                        message('error', $exception->getMessage());
                    }
                }
            }
        }
    }

# 自定义主题模板美化路径
    define('__PATH_TEMPLATE_ROOT__', __PATH_TEMPLATES__ . $config['website_template'] . '/');
    define('__PATH_TEMPLATE__', __BASE_URL__ . 'templates/' . $config['website_template'] . '/');
# 自定义主题模板美化路径外链
    define('__PATH_TEMPLATE_IMG__', __PATH_TEMPLATE__ . 'img/');
    define('__PATH_TEMPLATE_CSS__', __PATH_TEMPLATE__ . 'css/');
    define('__PATH_TEMPLATE_JS__', __PATH_TEMPLATE__ . 'js/');
    define('__PATH_TEMPLATE_FONTS__', __PATH_TEMPLATE__ . 'fonts/');

# 处理程序实例
    $handler = new Handler();
    $handler->loadPage();

