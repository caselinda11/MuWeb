<?php
/**
 * 安装程序
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();

if(check_value($_POST['install_step_5_submit'])) {
	try {
		if(!check_value($_POST['username'])) throw new Exception('您必须填写所有必填字段.');
		if(!check_value($_POST['game_type'])) throw new Exception('您必须填写所有必填字段.');
		
		# 验证用户名规则
		if(!Validator::AlphaNumeric($_POST['username'])) throw new Exception('管理员帐户用户名只能包含字母与数字。');
		
		# 验证链接数据是否存在
		if(!check_value($_SESSION['SQL_DB_HOST'])) throw new Exception('缺少数据库连接信息，请重新启动安装向导。');
		if(!check_value($_SESSION['SQL_DB_NAME'])) throw new Exception('缺少数据库连接信息，请重新启动安装向导。');
		if(!check_value($_SESSION['SQL_DB_USER'])) throw new Exception('缺少数据库连接信息，请重新启动安装向导。');
		if(!check_value($_SESSION['SQL_DB_PASS'])) throw new Exception('缺少数据库连接信息，请重新启动安装向导。');
		if(!check_value($_SESSION['SQL_DB_PORT'])) throw new Exception('缺少数据库连接信息，请重新启动安装向导。');
		if(!check_value($_SESSION['SQL_PDO_DRIVER'])) throw new Exception('缺少数据库连接信息，请重新启动安装向导。');
		
		# 检查有效的服务器文件类型
        global $Games;
		if(!array_key_exists($_POST['game_type'], $Games['serverType'])) throw new Exception('所选的服务器文件类型与网站系统不兼容。');
		
		# 设置配置文件
        $DefaultConfig['admins'] = [$_POST['username'] => 100];
        $DefaultConfig['server_files'] = strtolower($_POST['game_type']);
        $DefaultConfig['SQL_DB_HOST'] = $_SESSION['SQL_DB_HOST'];
        $DefaultConfig['SQL_DB_NAME'] = $_SESSION['SQL_DB_NAME'];
        $DefaultConfig['SQL_DB_USER'] = $_SESSION['SQL_DB_USER'];
        $DefaultConfig['SQL_DB_PASS'] = $_SESSION['SQL_DB_PASS'];
        $DefaultConfig['SQL_DB_PORT'] = (int)$_SESSION['SQL_DB_PORT'];
        $DefaultConfig['SQL_PDO_DRIVER'] = (int)$_SESSION['SQL_PDO_DRIVER'];
        $DefaultConfig['SQL_ENABLE_MD5'] = (bool)$_SESSION['SQL_ENABLE_MD5'];
        $DefaultConfig['system_status'] = true;

        # 配置文件转码
        $newSystemConfigs = json_encode($DefaultConfig, JSON_PRETTY_PRINT);
        if($newSystemConfigs == false) throw new Exception('无法编码对配置进行转码。');

        # 保存配置文件
        $cfgFile = fopen($ConfigsPath, 'w');
        if(!$cfgFile) throw new Exception('无法打开网站程序配置文件。');
        $cfgUpdate = fwrite($cfgFile, $newSystemConfigs);
        if(!$cfgUpdate) throw new Exception('无法保存网站程序配置文件。');
        fclose($cfgFile);

		#更新一下缓存文件
        $database = new dB($_SESSION['SQL_DB_HOST'],$_SESSION['SQL_DB_PORT'], $_SESSION['SQL_DB_NAME'],$_SESSION['SQL_DB_USER'],$_SESSION['SQL_DB_PASS'],$_SESSION['SQL_PDO_DRIVER']);
        $downloadsData = $database->query_fetch("SELECT * FROM [".X_TEAM_DOWNLOADS."] ORDER BY download_type ASC, download_id ASC");
        $newsData = $database->query_fetch("SELECT * FROM [".X_TEAM_NEWS."] ORDER BY [news_id] DESC");
        updateCacheFile('downloads.cache', (encodeCache($downloadsData)));
        updateCacheFile('news.cache', (encodeCache($newsData)));
        try{
            $database->beginTransaction();
            global $install;
            foreach ($install['sql_server_list'] as $data){
                $database->query("INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES (?)",[$data]);
            }
            $database->commit();
        }catch (Exception $exception){
            $database->rollBack();
            message('error',$exception->getMessage());
        }

        updateCacheFile('plugins.cache', '');
        updateCacheFile('rankings_level.cache', '');
        updateCacheFile('rankings_guilds.cache', '');
        updateCacheFile('rankings_gens.cache', '');
        updateCacheFile('rankings_votes.cache', '');
        updateCacheFile('server_info.cache', '');
        updateCacheFile('castle_siege.cache', '');
        updateCacheFile('rankings_money.cache', '');
        updateCacheFile('rankings_ban.cache', '');
        updateCacheFile('rankings_buy.cache', '');
        updateCacheFile('rankings_shops.cache', '');
        updateCacheFile('rankings_shop.cache', '');
        updateCacheFile('BFB_info.cache', '');
		# 清空 session 数据
		$_SESSION = [];
		session_destroy();

		# 重定向到首页
        echo '<script type="text/javascript">';
        echo 'alert("恭喜您已经成功安装完成！\n为了您的网站安全，请您手动删除[install]目录。\n点击确定将为您跳转到您的网站首页！");';
        echo 'window.location.href = "'.__BASE_URL__.'";';
        echo '</script>';
		die();
		
	} catch (Exception $ex) {
		echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';
	}
}

?>
<div class="progress mb-3">
    <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 99%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">99%</div>
</div>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>提示:</strong>
    管理员账号必须是你现有的帐号,例如你可以使用您的GM工具创建一个账号!
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="card"><?global $install?>
    <div class="card-header"><?=$install['step_list'][7][1]?></div>
    <div class="card-body">
        <form class="form-horizontal" method="post">
            <div class="form-group row justify-content-md-center">
                <label for="username" class="col-sm-2 col-form-label text-right">
                    管理员帐号
                </label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="username" name="username" placeholder="admin">
                </div>
                <div class="col-sm-2">
                    <span class="text-danger">*</span>
                </div>
            </div>
            <div class="form-group row justify-content-md-center">
                <label for="game_type" class="col-sm-2 col-form-label text-right">
                    服务器文件
                </label>
                <div class="col-sm-5">
                    <select class="form-control" id="game_type" name="game_type">
                        <?
                        global $Games;
                        foreach($Games['serverType'] as $serverFileValue => $serverFileInfo) { ?>
                            <option value="<?php echo $serverFileValue; ?>"><?php echo $serverFileInfo['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <span class="text-danger">*</span>
                </div>
            </div>
            <div class="form-group row justify-content-md-center mt-2">
                <button type="submit" name="install_step_5_submit" value="continue" class="btn btn-success col-md-5">完成安装</button>
            </div>
        </form>
    </div>
</div>
