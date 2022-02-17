<?php
/**
 * 文件说明
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
                    <li class="breadcrumb-item active">网站设置</li>
                </ol>
            </div>
            <h4 class="page-title">网站设置</h4>
        </div>
    </div>
</div>
<?php
$allowedSettings = [
    'submit', # 提交按钮
    'system_active',
    'error_reporting',
    'website_template',
    'maintenance_page',
    'server_name',
    'website_title',
    'website_meta_description',
    'website_meta_keywords',
    'website_forum_link',
    'server_files',
    'plugins_system_enable',
    'ip_block_system_enable',
    'player_profiles',
    'guild_profiles',
    'username_min_len',
    'username_max_len',
    'password_min_len',
    'password_max_len',
    'cron_api',
	'sendType',
    'cron_api_key',
    'SQL_PDO_DRIVER',
    'SQL_ENABLE_MD5',
    'QQ',
    'QQUN',
    'qq_enable',
];
$active = [
    0 => '',
    1 => '',
    2 => '',
];

#初始状态
if(array_search('active',$active) == false) $active[0] = 'active';
try {

if(check_value($_POST['submit'])) {
    switch ($_POST['submit']){
        case "common_settings":
            try{
                # 网站状态设置
                if (!check_value($_POST['system_active'])) throw new Exception('网站状态设置无效。');
                if (!in_array($_POST['system_active'], array(0, 1))) throw new Exception('网站状态设置无效。');
                # 调试模式
                if (!check_value($_POST['error_reporting'])) throw new Exception('错误报告设置无效。');
                if (!in_array($_POST['error_reporting'], array(0, 1))) throw new Exception('网站状态设置无效。');
                # 维护页面
                if (!check_value($_POST['maintenance_page'])) throw new Exception('维护页面设置无效.');
                if (!Validator::Url($_POST['maintenance_page'])) throw new Exception('维护页面设置不是有效的URL.');
                # 服务器名称
                if (!check_value($_POST['server_name'])) throw new Exception('服务器名称设置无效.');
                # 网站标题
                if (!check_value($_POST['website_title'])) throw new Exception('网站名称设置无效.');
                # 网站描述
                if (!check_value($_POST['website_meta_description'])) throw new Exception('描述设置无效.');
                # 网站关键字
                if (!check_value($_POST['website_meta_keywords'])) throw new Exception('关键字设置无效.');
                # 默认模版风格
                if (!check_value($_POST['website_template'])) throw new Exception('默认模板设置无效。');
                if (!file_exists(__PATH_TEMPLATES__ . $_POST['website_template'] . '/index.php')) throw new Exception('所选模板不存在。');
                # 论坛链接
                if (!check_value($_POST['website_forum_link'])) throw new Exception('论坛链接设置无效.');
                if (!Validator::Url($_POST['website_forum_link'])) throw new Exception('论坛链接设置不是有效的URL.');

                if (!check_value($_POST['qq'])) throw new Exception('QQ号不能为空。');
                if (!Validator::UnsignedNumber($_POST['qq'])) throw new Exception('QQ号必须是纯数字。');
                if (!check_value($_POST['qqun'])) throw new Exception('QQ群链接不能为空。');
                if (!in_array($_POST['qq_enable'], array(0, 1))) throw new Exception('QQ群启用设置无效。');
                #封存数组
                $settings = [
                    'system_active'             => (bool)$_POST['system_active'],
                    'error_reporting'           => (bool)$_POST['error_reporting'],
                    'maintenance_page'          => $_POST['maintenance_page'],
                    'server_name'               => $_POST['server_name'],
                    'website_title'             => $_POST['website_title'],
                    'website_meta_description'  => $_POST['website_meta_description'],
                    'website_meta_keywords'     => $_POST['website_meta_keywords'],
                    'website_forum_link'        => $_POST['website_forum_link'],
                    'website_template'          => $_POST['website_template'],
                    'QQ'                        => (int)$_POST['qq'],
                    'QQUN'                      => $_POST['qqun'],
                    'qq_enable'                 => (bool)$_POST['qq_enable'],
                ];
                # 加载网站设置
                $Configurations = webConfigs();
                # 确保设置在允许列表中
                foreach(array_keys($settings) as $settingName) {
                    if(!in_array($settingName, $allowedSettings)) throw new Exception('提交的一项或多项设置不可修改!');
                    $Configurations[$settingName] = $settings[$settingName];
                }

                $newSystemConfig = json_encode($Configurations, JSON_PRETTY_PRINT);
                $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__ . 'system.json', 'w');
                if (!$cfgFile) throw new Exception('打开配置文件时出现错误！');

                fwrite($cfgFile, $newSystemConfig);
                fclose($cfgFile);
                unset($active);
                $active[0] = 'active';
                message('success', '设置成功保存!');
            }catch(Exception $ex) {
                message('error', $ex->getMessage());
            }
            break;
        case "web_settings":
            try{
                # 插件系统
                if(!check_value($_POST['plugins_system_enable'])) throw new Exception('插件系统设置无效.');
                if(!in_array($_POST['plugins_system_enable'], array(0, 1))) throw new Exception('插件系统设置无效.');
                # IP屏蔽系统
                if(!check_value($_POST['ip_block_system_enable'])) throw new Exception(' IP屏蔽系统设置无效.');
                if(!in_array($_POST['ip_block_system_enable'], array(0, 1))) throw new Exception('IP屏蔽系统设置无效.');
                # 玩家资料详细链接
                if(!check_value($_POST['player_profiles'])) throw new Exception('无效设置 (player_profiles)');
                if(!in_array($_POST['player_profiles'], array(0, 1))) throw new Exception('无效设置 (player_profiles)');
                # 战盟资料详细链接
                if(!check_value($_POST['guild_profiles'])) throw new Exception('无效设置 (guild_profiles)');
                if(!in_array($_POST['guild_profiles'], array(0, 1))) throw new Exception('无效设置 (guild_profiles)');
                # pdo dsn
                if(!check_value($_POST['SQL_PDO_DRIVER'])) throw new Exception('无效的PDO驱动程序设置。');
                if(!Validator::UnsignedNumber($_POST['SQL_PDO_DRIVER'])) throw new Exception('无效的PDO驱动程序设置。');
                if(!in_array($_POST['SQL_PDO_DRIVER'], array(1, 2, 3))) throw new Exception('无效的PDO驱动程序设置。');
                # md5
                if(!check_value($_POST['SQL_ENABLE_MD5'])) throw new Exception('无效的MD5设置!');
                if(!in_array($_POST['SQL_ENABLE_MD5'], array(0, 1))) throw new Exception('无效的MD5设置!');
                # 服务器文件
                if (!check_value($_POST['server_files'])) throw new Exception('服务器文件设置无效.');
                global $Games;
                if (!array_key_exists($_POST['server_files'], $Games['serverType'])) throw new Exception('服务器文件设置无效.');

                $settings = [
                    'plugins_system_enable'     => (bool)$_POST['plugins_system_enable'],
                    'ip_block_system_enable'    => (bool)$_POST['ip_block_system_enable'],
                    'player_profiles'           => (bool)$_POST['player_profiles'],
                    'guild_profiles'            => (bool)$_POST['guild_profiles'],
                    'server_files'              => $_POST['server_files'],
                    'SQL_PDO_DRIVER'            => (int)$_POST['SQL_PDO_DRIVER'],
                    'SQL_ENABLE_MD5'            => (bool)$_POST['SQL_ENABLE_MD5'],
                ];
                # 加载网站设置
                $Configurations = webConfigs();
                # 确保设置在允许列表中
                foreach(array_keys($settings) as $settingName) {
                    if(!in_array($settingName, $allowedSettings)) throw new Exception('提交的一项或多项设置不可修改!');
                    $Configurations[$settingName] = $settings[$settingName];
                }

                $newSystemConfig = json_encode($Configurations, JSON_PRETTY_PRINT);
                $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__ . 'system.json', 'w');
                if (!$cfgFile) throw new Exception('打开配置文件时出现错误！');

                fwrite($cfgFile, $newSystemConfig);
                fclose($cfgFile);
                unset($active);
                $active[1] = 'active';
                message('success', '设置成功保存!');
            }catch(Exception $ex) {
                message('error', $ex->getMessage());
            }
            break;
        case "other_settings":
            try{
                # username_min_len
                if(!check_value($_POST['username_min_len'])) throw new Exception('无效设置 (username_min_len)');
                if(!Validator::UnsignedNumber($_POST['username_min_len'])) throw new Exception('无效设置 (username_min_len)');
                # username_max_len
                if(!check_value($_POST['username_max_len'])) throw new Exception('无效设置 (username_max_len)');
                if(!Validator::UnsignedNumber($_POST['username_max_len'])) throw new Exception('无效设置 (username_max_len)');
                # password_min_len
                if(!check_value($_POST['password_min_len'])) throw new Exception('无效设置 (password_min_len)');
                if(!Validator::UnsignedNumber($_POST['password_min_len'])) throw new Exception('无效设置 (password_min_len)');
                # password_max_len
                if(!check_value($_POST['password_max_len'])) throw new Exception('无效设置 (password_max_len)');
                if(!Validator::UnsignedNumber($_POST['password_max_len'])) throw new Exception('无效设置 (password_max_len)');
                # cron_api
                if(!check_value($_POST['cron_api'])) throw new Exception('无效设置 (cron_api)');
                if(!in_array($_POST['cron_api'], array(0, 1))) throw new Exception('无效设置 (cron_api)');
                # cron_api_key
                if(!check_value($_POST['cron_api_key'])) throw new Exception('无效设置 (cron_api_key)');

                $settings = [
                    'username_min_len'          => (int)$_POST['username_min_len'],
                    'username_max_len'          => (int)$_POST['username_max_len'],
                    'password_min_len'          => (int)$_POST['password_min_len'],
                    'password_max_len'          => (int)$_POST['password_max_len'],
					'sendType'          => (int)$_POST['sendType'],
                    'cron_api'                  => (bool)$_POST['cron_api'],
                    'cron_api_key'              => $_POST['cron_api_key'],
                ];

                # 加载网站设置
                $Configurations = webConfigs();
                # 确保设置在允许列表中
                foreach(array_keys($settings) as $settingName) {
                    if(!in_array($settingName, $allowedSettings)) throw new Exception('提交的一项或多项设置不可修改!');
                    $Configurations[$settingName] = $settings[$settingName];
                }

                $newSystemConfig = json_encode($Configurations, JSON_PRETTY_PRINT);
                $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__ . 'system.json', 'w');
                if (!$cfgFile) throw new Exception('打开配置文件时出现错误！');

                fwrite($cfgFile, $newSystemConfig);
                fclose($cfgFile);
                unset($active);

                $active[2] = 'active';
                message('success', '设置成功保存!');

            }catch(Exception $ex) {
                message('error', $ex->getMessage());
            }
            break;
        default:
            message('error', '禁止非法提交,请确保数据的正确性!');
            break;
    }

}
?>
    <div class="card">
        <div class="card-header">网站设置</div>
        <div class="card-body">
            <?$config = webConfigs()?>
            <?if(is_array($config)){?>
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=$active[0]?>" id="home-tab" data-toggle="tab" href="#common" role="tab" aria-controls="common" aria-selected="true">基础设置</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=$active[1]?>" id="profile-tab" data-toggle="tab" href="#website" role="tab" aria-controls="website" aria-selected="false">站点设置</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=$active[2]?>" id="profile-tab" data-toggle="tab" href="#other" role="tab" aria-controls="other" aria-selected="false">其他设置</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show <?=$active[0]?>" id="common" role="tabpanel" aria-labelledby="home-tab">
                    <form action="" method="post">
                        <table class="table table-striped table-bordered table-hover" style="table-layout: fixed;">
                            <tr>
                                <td style="width: 60%">
                                    <strong>网站状态</strong>
                                    <p class="text-muted mb-0 font-14">启用/禁用您的网站。 如果禁用，访客将被跳转到维护页面。</p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('system_active',$config['system_active'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>调试模式</strong>
                                    <p class="text-muted mb-0 font-14">在调试模式下，仅当您希望网站显示任何错误时才启用此设置。</p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('error_reporting',$config['error_reporting'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>维护页面网址</strong>
                                    <p class="text-muted mb-0 font-14">您网站维护页面的完整URL地址。网站禁用后，访客将被跳转到您的维护页面。</p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="maintenance_page" value="<?=$config['maintenance_page']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 60%">
                                    <strong>服务器名</strong>
                                    <p class="text-muted mb-0 font-14">您的游戏名称 <kbd>server_name</kbd></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="server_name" value="<?=$config['server_name']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>网站名</strong>
                                    <p class="text-muted mb-0 font-14">您网站的标题 <kbd>website_title</kbd></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="website_title" value="<?=$config['website_title']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>网站描述 </strong>
                                    <p class="text-muted mb-0 font-14">定义网站的描述，用于搜索引擎可以更好的搜索到您！<kbd>website_meta_description</kbd></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="website_meta_description" value="<?=$config['website_meta_description']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>网站关键词</strong>
                                    <p class="text-muted mb-0 font-14">定义网站的关键词，用于搜索引擎可以更好的搜索到您！<kbd>website_meta_keywords</kbd></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="website_meta_keywords" value="<?=$config['website_meta_keywords']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>论坛链接</strong>
                                    <p class="text-muted mb-0 font-14">您论坛的完整URL。<kbd>website_forum_link</kbd></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="website_forum_link" value="<?=$config['website_forum_link']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>默认主题</strong>
                                    <p class="text-muted mb-0 font-14">您网站的模版风格美化，您的模版美化应该放在<code>templates</code>目录</p>
                                </td>
                                <td>
                                    <select class="form-control" name="website_template">
                                        <? foreach (getDirectoryListFromPath(__PATH_TEMPLATES__) as $dir){?>
                                            <option value="<?=$dir?>" <?=selected($config['website_template'],(string)$dir)?>><?=$dir?></option>
                                        <?}?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>客服QQ</strong>
                                    <p class="text-muted mb-0 font-14">用于前端显示的客服QQ,将自动生成链接。<kbd>QQ</kbd></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="qq" value="<?=$config['QQ']?>">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>QQ群链接</strong>
                                    <p class="text-muted mb-0 font-14">用于前端显示的客服QQ群链接,此链接可到自己的群->资料->分享中获取。<kbd>QQUN</kbd></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="qqun" value="<?=$config['QQUN']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>QQ群弹窗</strong>
                                    <p class="text-muted mb-0 font-14">启用/禁用 如果启用，访客访问首页将弹出添加QQ群链接。</p>
                                </td>
                                <td>
                                    <?=enableDisableCheckboxes('qq_enable',$config['qq_enable'],'启用','禁用')?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center"><button type="submit" name="submit" value="common_settings" class="btn btn-success col-md-3">保存</button></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="tab-pane fade show <?=$active[1]?>" id="website" role="tabpanel" aria-labelledby="profile-tab">
                    <form action="" method="post">
                        <table class="table table-striped table-bordered table-hover" style="table-layout: fixed;">
                            <tr>
                                <td width="60%">
                                    <strong>插件系统</strong>
                                    <p class="text-muted mb-0 font-14">启用/禁用 插件系统.</p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('plugins_system_enable',$config['plugins_system_enable'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>IP屏蔽系统状态</strong>
                                    <p class="text-muted mb-0 font-14">启用/禁用IP屏蔽系统.</p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('ip_block_system_enable',$config['ip_block_system_enable'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>玩家资料</strong>
                                    <p class="text-muted mb-0 font-14">如果启用，玩家名称将带有指向其公开资料的链接。</p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('player_profiles',$config['player_profiles'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>战盟资料</strong>
                                    <p class="text-muted mb-0 font-14">如果启用，战盟名称将带有指向其公开资料的链接。</p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('guild_profiles',$config['guild_profiles'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>游戏类型</strong>
                                    <p class="text-muted mb-0 font-14">定义你的服务端文件类型。<span class="text-danger">*不要乱改</span></p>
                                </td>
                                <td>
                                    <select class="form-control" name="server_files">
                                        <?
                                        global $Games;
                                        $fileCompatibilityList = $Games['serverType'];
                                        if(is_array($fileCompatibilityList)) {
                                            foreach($fileCompatibilityList as $value => $fileCompatibilityInfo) {?>
                                                <option value="<?=$value?>"<?=selected(strtolower($config['server_files']),(string)$value)?>><?=$fileCompatibilityInfo['name']?></option>';
                                            <?}
                                        }?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong>驱动协议</strong>
                                    <p class="text-muted mb-0 font-14">网站系统选择用来远程连接到MSSQL服务器的驱动程序。<span class="text-danger">*不要乱改</span></p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <div class="form-check-inline my-1">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="SQL_PDO_DRIVER1" name="SQL_PDO_DRIVER" value="1" class="custom-control-input" <?=($config['SQL_PDO_DRIVER']== 1 ? 'checked' : '')?>>
                                                    <label class="custom-control-label" for="SQL_PDO_DRIVER1">DBLIB (linux)</label>
                                                </div>
                                            </div>
                                            <div class="form-check-inline my-1">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="SQL_PDO_DRIVER2" name="SQL_PDO_DRIVER" value="2" class="custom-control-input" <?=($config['SQL_PDO_DRIVER']== 2 ? 'checked' : '')?>>
                                                    <label class="custom-control-label" for="SQL_PDO_DRIVER2">SQLSRV</label>
                                                </div>
                                            </div>
                                            <div class="form-check-inline my-1">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="SQL_PDO_DRIVER3" name="SQL_PDO_DRIVER" value="3" class="custom-control-input" <?=($config['SQL_PDO_DRIVER']== 3 ? 'checked' : '')?>>
                                                    <label class="custom-control-label" for="SQL_PDO_DRIVER3">ODBC</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>MD5</strong>
                                    <p class="text-muted mb-0 font-14">启用/禁用 MD5 </p>
                                </td>
                                <td>
                                    <div class="form-group row">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('SQL_ENABLE_MD5',$config['SQL_ENABLE_MD5'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center"><button type="submit" name="submit" value="web_settings" class="btn btn-success col-md-3">保存</button></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="tab-pane fade show <?=$active[2]?>" id="other" role="tabpanel" aria-labelledby="profile-tab">
                    <form action="" method="post">
                        <table class="table table-striped table-bordered table-hover" style="table-layout: fixed;">
                            <tr>
                                <td style="width: 60%">
                                    <strong>账号最小长度</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="username_min_len" value="<?=$config['username_min_len']?>" required>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>账号最大长度</strong><span class="text-danger ml-2">*不要瞎改,很多端限制最长10位</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="username_max_len" value="<?=$config['username_max_len']?>" required>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>密码最小长度</strong>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="password_min_len" value="<?=$config['password_min_len']?>" required>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>密码最大长度</strong><span class="text-danger ml-2">*不要瞎改,很多端限制最长10位</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="password_max_len" value="<?=$config['password_max_len']?>" required>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <strong>定时任务API</strong>
                                    <p class="text-muted mb-0 font-14">启用/禁用 定时缓存任务的[API]，用于站外触发缓存任务系统的链接。</p>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <?=enableDisableCheckboxes('cron_api',$config['cron_api'],'启用','禁用')?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
							
							<tr>
                                <td>
                                    <strong>多作者保管箱选择</strong>
									 <p class="text-muted mb-0 font-14">1.EG,2.TL,3.无保管箱直发仓库。</p>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" name="sendType" value="<?=$config['sendType']?>" required>
                                        </div>
                                    </div>
                                </td>
                            </tr>
							
							<tr>
                                <td>
                                    <strong>任务链接密钥</strong>
                                    <p class="text-muted mb-0 font-14">定时缓存任务的密匙，用法：<code><?=__BASE_URL__; ?>api/cron.php?key=<span style="color:red"><?=$config['cron_api_key']?></span></code></p>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="cron_api_key" value="<?=$config['cron_api_key']?>" required>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center"><button type="submit" name="submit" value="other_settings" class="btn btn-success col-md-3">保存</button></td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <?}?>
    </div>
<?php
} catch (Exception $ex) {
    message('error', $ex->getMessage());
}
