<?php
/**
 * 安装程序
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();

if(check_value($_GET['action'])) {
	if($_GET['action'] == 'install') {
		$_SESSION['install_cstep']++;
		header('Location: install.php');
		die();
	}
}

?>

<div class="card">
    <div class="card-header"><?global $install?><?=$install['step_list'][0][1]?></div>
    <div class="card-body">
        <h5>感谢您使用我们的网站系统作为您的游戏网站，请务必遵照以下规则。</h5>
        <p>
            1、禁止使用我们的网站系统损害社会、国家利益、违法买卖，由您运行的网站发生的一切法律责任将于作者无关！<br>
            2、每套网站程序将会有对应的体别码，禁止私下传播我们的网站系统，私下传播将停止一切服务，情节严重将追究法律。<br>
        </p>
        <hr>
        <h4>安装：</h4>
        <p>
            安装我们的网站系统非常容易，在安装过程中，安装程序将帮助您确保Web服务器满足运行CMS的指定要求。<br>
            如果要在云服务器或者托管服务器中安装我们的网站系统，请确保您的托管服务提供商允许到Microsoft SQL Server端口的传出远程连接[通常为端口1433]。
        </p>
        <hr>
        <h4>版本：</h4>
        <p>为确保使用我们的软件获得最佳体验，请确保您正在安装最新的稳定版本。</p>
        <a href="http://wpa.qq.com/msgrd?V=1&uin=83213956&Menu=yes" target="_blank" class="btn btn-default">支持</a>
        <hr>
        <p>如果要继续安装，请单击下面的“开始安装”按钮。</p>
        <div style="text-align:center">
            <a href="?action=install" class="btn btn-success col-md-4">开始安装</a>
        </div>
    </div>
</div>

