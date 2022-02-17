<?php
/**
 * 模块设置页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="<?=admincp_base()?>">官方主页</a>
                    </li>
                    <li class="breadcrumb-item active">网站设置</li>
                    <li class="breadcrumb-item active">模块管理</li>
                </ol>
            </div>
            <h4 class="page-title">模块管理</h4>
        </div>
    </div>
</div>
<?php
$SystemModules = [
	'_global' => [
		['新闻中心','Config_News'],
		['账号登陆','Config_Login'],
		['账号注册','Config_Register'],
		['游戏下载','Config_Down'],
		['英雄排名','Config_Rankings'],
		['罗兰攻城','Config_castlesiege'],
		['邮箱系统','Config_Email'],
		['个人资料','Config_ProFiles'],
		['忘记密码','Config_ForGotPassword'],
		['赞助系统','Config_Donation'],
        ['联系我们','Config_Contact']
	],

	'_usercp' => [
        ['个人面板','User_MyAccount'],
        ['更改密码','User_MyPassword'],
        ['更改邮箱','User_MyEmail'],
        ['角色自救','User_UnStick'],
		['在线加点','User_AddStats'],
		['在线洗红','User_ClearPk'],
        ['在线洗点','User_ResetStats'],
		['大师洗点','User_ClearMaster'],
		['在线转身','User_Reset'],
		['购买金币','User_BuyZen'],
        ['任务系统','User_Vote'],
	],
];

?>
<div class="card">
    <nav id="navbar-example2" class="navbar navbar-light">
        <a class="navbar-brand" href="#">全局模块</a>
        <ul class="nav nav-pills">
            <?foreach($SystemModules['_global'] as $moduleList) {?>
                <li class="nav-item">
                    <a class="nav-link <?=($_GET['config'] == $moduleList[1]) ?'active' : '';?>" href="<?=admincp_base("Settings_Model&config=".$moduleList[1]);?>"><?=$moduleList[0];?></a>
                </li>
            <?}?>
        </ul>
    </nav>
</div>
<div class="card">
    <nav id="navbar-example2" class="navbar navbar-light">
        <a class="navbar-brand" href="#">用户模块</a>
        <ul class="nav nav-pills">
            <?foreach($SystemModules['_usercp'] as $moduleList) {?>
                <li class="nav-item">
                    <a class="nav-link <?=($_GET['config'] == $moduleList[1]) ?'active' : '';?>" href="<?=admincp_base("Settings_Model&config=".$moduleList[1]);?>"><?=$moduleList[0];?></a>
                </li>
            <?}?>
        </ul>
    </nav>
</div>
<?
    if(check_value($_GET['config'])) {
        $filePath = __PATH_ADMINCP_MODULES__.'mconfig/'.$_GET['config'].'.php';
        if(file_exists($filePath)) {
            include($filePath);
        } else {
            message('error','该模块无效，请检测该文件是否存在！');
        }
    }
?>

