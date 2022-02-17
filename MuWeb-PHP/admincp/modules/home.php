
<?php
/**
 * 后台仪表盘
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
                    <li class="breadcrumb-item active">仪表盘</li>
                </ol>
            </div>
            <h4 class="page-title">
                网站基础信息</h4>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<?php
try {
// 检查安装目录
    if (file_exists(__ROOT_DIR__ . 'install/')) {
        message('warning', '您的网站程序<strong>install</strong>安装目录仍然存在，建议您重命名或删除它。', '警告 ');
    }

// 检查Web服务器主机
    if (DIRECTORY_SEPARATOR == '\\') {
        message('info', '您当前使用的是 <strong>Windows</strong> 系统，您需要手动配置定时任务！', '提示 ');
    } else {
        message('info', '您当前使用的是 <strong>Linux</strong> 系统，您需要手动配置定时任务！', '提示 ');
    }
// 插件状态
    $pluginStatus = (config('plugins_system_enable') ? '使用' : '禁用');
// 计划任务
    $scheduledTasks = Connection::Database('Web')->query_fetch_single("SELECT COUNT(*) as result FROM " . X_TEAM_CRON);
// 管理员
    $adminCpUsers = implode(", ", array_keys(config('admins')));

    $common = new common();
    ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="col-3 align-self-center">
                                    <div class="round">
                                        <i class="ion-android-social-user"></i>
                                    </div>
                                </div>

                                <div class="col-9 align-self-center text-right">
                                    <div class="m-l-10">
                                        <h5 class="mt-0"><?= $common->getServerAccountCount(); ?></h5>
                                        <p class="mb-0 text-muted">总账号
                                            <!--                                        <span class="badge bg-soft-success"><i class="mdi mdi-arrow-up"></i>2.35%</span>-->
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="progress mt-3" style="height:3px;">
                                <div class="progress-bar  bg-success" role="progressbar" style="width: 35%;"
                                     aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>

                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-row">
                                <div class="col-3 align-self-center">
                                    <div class="round">
                                        <i class="ion-android-contact"></i>
                                    </div>
                                </div>

                                <div class="col-9 text-right align-self-center">
                                    <div class="m-l-10 ">
                                        <h5 class="mt-0"><?= $common->getServerCharacterCount(); ?></h5>
                                        <p class="mb-0 text-muted">总角色</p>
                                    </div>
                                </div>

                            </div>
                            <div class="progress mt-3" style="height:3px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 48%;"
                                     aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="search-type-arrow"></div>
                            <div class="d-flex flex-row">
                                <div class="col-3 align-self-center">
                                    <div class="round ">
                                        <i class="ion-load-c"></i>
                                    </div>
                                </div>

                                <div class="col-9 align-self-center text-right">
                                    <div class="m-l-10 ">
                                        <h5 class="mt-0"><?= $common->getServerOnlineCount(); ?></h5>
                                        <p class="mb-0 text-muted">在线总数</p>
                                    </div>
                                </div>
                            </div>
                            <div class="progress mt-3" style="height:3px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 61%;"
                                     aria-valuenow="61" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->
            </div>
            <div class="card">
                <div class="card-header">自定义模块</div>
                <div class="card-body">
                    <h4 class="mt-0 header-title">自定义</h4>
                    <p class="text-muted mb-2 font-14 d-inline-block text-truncate w-100">您可以在此处放置您的自定义版本</p>
                    <div class="media mb-2">
                        <div class="media-body">
                            <h5 class="mt-0 font-18">网站安装指南</h5>
                            <p>如果这是你第一次安装好网站，请记得到货币系统中添加一个货币，然后通过系统添加一个定时任务。</p>
                            <h5 class="mt-0 font-18">网站转移指南</h5>
                            <p>假设您想转移您的网站到一个新的网站，您只需要复制<code>includes</code>下的<code>config</code>目录，并修改<code>config</code>目录内的server.json与system.json内的新服务器IP\数据库信息即可。</p>
                            <h5 class="mt-0 font-18">创建定时任务</h5>
                            <code>windows[开始]</code>-><code>管理工具</code>-><code>任务计划程序</code>-><code>任务计划程序库</code>->右键添加任务
                            <h5 class="mt-3 font-18">目录说明</h5>
                            <p>
                                <code>admincp</code> = 后台目录<br>
                                <code>api</code> = 开放接口目录（不懂不要动）<br>
                                <code>includes</code> = 网站系统关键目录（一般不要动）<br>
                                <code>includes\plugins</code> = 网站插件系统目录(插件放这里)<br>
                                <code>includes\config\system.json</code>是网站系统所用到的关键配置文件 <strong>(*如果要重装网站,清空它即可)</strong><br>
                                <code>includes\config\server.json</code>是连接游戏数据库的分区配置文件<br>
                                <code>install</code> = 网站安装目录（安装网站系统）<br>
                                <code>modules</code> = 前台模块目录（每个页面对应一个文件）<br>
                                <code>public</code> = 公共资源目录（一些公共图片之类的）<br>
                                <code>templates</code> = 模版美化目录（美化放在这里目录）<br>
                                <code>.htaccess</code>=关键文件，如果除了主页其他网站无法打开，请注意此文件是否与我们的原始文件一致。
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">各类角色比例图</div>
                <div class="card-body">
                    <?
                    $count_char = charCount();
                    global $custom;
                    foreach ($count_char as $char=>$value){
                        if($value){
                            $percentage = round($value / array_sum($count_char) * 100);
                            $percentage = ($percentage > 0) ? $percentage+30 : $percentage;
                        }else{
                            $percentage = 0;
                        }
                        ?>
                        <div class="progress mb-1">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: <?=$percentage?>%"><?=$char?> <?=$percentage?>% - (<?=$value?>个)</div>
                        </div>
                    <?}?>
                </div>
            </div>
            <div class="card">
                <div class="card-header">系统信息</div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item">
                            网站系统
                            <span class="badge badge-pill badge-primary float-right">
						    <em><?= config('server_name'); ?></em>
					    </span>
                        </div>
                        <div class="list-group-item">
                            操作系统
                            <span class="badge badge-pill badge-primary float-right">
						<em><?=PHP_OS;?></em>
					</span>
                        </div>
                        <div class="list-group-item">
                            PHP版本
                            <span class="badge badge-pill badge-primary float-right">
						<em><?=phpversion();?></em>
					</span>
                        </div>
                        <a href="http://www.niudg.com/" class="list-group-item" target="_blank">
                            网站版本
                            <span class="badge badge-pill badge-primary float-right">
                            <?php
                              //     if(checkVersion()) <span class="label label-danger">有可用更新</span>
                            ?>
						<em><?= __X_TEAM_VERSION__; ?></em>
					</span>
                        </a>
                    </div>

                    <div class="list-group">
                        <div class="list-group-item">
                            扩展系统
                            <span class="badge badge-pill badge-primary float-right"><?= $pluginStatus; ?></span>
                        </div>
                        <div class="list-group-item">
                            定时任务
                            <span class="badge badge-pill badge-primary float-right"><?= number_format($scheduledTasks['result']); ?></span>
                        </div>
                        <div class="list-group-item">
                            超级管理
                            <span class="badge badge-pill badge-primary float-right"><?= $adminCpUsers; ?></span>
                        </div>
                        <div class="list-group-item">
                            <div style="margin-bottom:7px;"><a href="//wpa.qq.com/msgrd?V=1&uin=83213956&Menu=yes"
                                                               class="btn btn-success btn-lg btn-block" target="_blank">技术支持</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}

