<?php
/**
 * 后台首页
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

// 访问
define('access', 'admincp');
#测试所用 时间计算开头
$startTme= microtime(true); //开始时间，放在页面头部
try {
	// 加载系统文件

	if (!@include_once('../includes/Init.php')) throw new Exception('无法加载系统初始化文件。');
	// 检查用户是否登录
	if(!isLoggedIn()) { redirect(); }

	// 检查用户是否有权访问
	if(!canAccessAdminCP($_SESSION['username'])) { redirect(); }

	// 加载AdminCP工具
	if(!@include_once(__PATH_ADMINCP_INC__ . 'functions.php')) throw new Exception('无法加载管理员面板功能。');

	// 检查配置
	if(!@include_once(__PATH_ADMINCP_INC__ . 'check.php')) throw new Exception('无法加载管理员面板配置检查。');

} catch (Exception $ex) {
	$errorPage = file_get_contents('../includes/error.html');
	echo str_replace("{ERROR_MESSAGE}", $ex->getMessage(), $errorPage);
	die();
}

$adminCpSidebar = [
        ["货币系统", [
            "Credits_Configs" => "币种配置",
            "Credits_Manager" => "货币管理",
            "Logs_Shop" => "消费记录",
            "Logs_Publicize" => "推广记录",
        ],
            "ion-social-usd"],
        ["账号系统",[
            "Account_Manage"            =>  "账号管理",
            "Account_NewRegister"       =>  "最近注册",
            "Account_Info" => "", // 隐藏
            "Character_Manage"          =>  "角色管理",
            "Character_Edit" => "", // 隐藏
        ],"ion-android-contacts"],
        ["禁用系统", [
                "Ban_Manage"    => "搜索封停",
                "Ban_Account"   => "封停账号",
                "Ban_Latest"    => "最近封停",
                "Ban_Ip"        => "封停IP",
            ],
            "ion-ios7-locked"],
        ["权限系统",[
                "Admin_Manage"=>"管理员列表",
                "Admin_Permissions"=>"管理员权限",
        ],"mdi mdi-layers"],
        ["插件系统", [
                "Plugins_Manage" => "插件管理",
                "Plugins_Install" => "插件上传",
                "Plugins_cloud"  => "插件市场",
            ],
            "fa-plug"],
];
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>奇迹管理系统</title>
    <meta content="X TEAM CMS" name="description" />
    <meta content="mason X" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Plugins css -->
    <link href="assets/plugins/clockpicker/jquery-clockpicker.min.css" rel="stylesheet" />
    <link href="assets/plugins/colorpicker/asColorPicker.min.css" rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href="assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- Dropzone css -->
    <link href="assets/plugins/dropzone/dist/dropzone.css" rel="stylesheet" type="text/css">
    <link href="assets/plugins/dropify/css/dropify.min.css" rel="stylesheet">

    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="fixed-left">
<!-- Begin page -->
<div id="wrapper">
    <!-- 左侧开始 -->
    <div class="left side-menu">
        <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
            <i class="ion-close">
            </i>
        </button>
        <!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center">
                <a href="<?=admincp_base()?>" class="logo">
                    <img src="assets/images/logo-lg.png" alt="" class="logo-large">
                </a>
            </div>
        </div>
        <div class="sidebar-inner niceScrollleft">
            <div id="sidebar-menu">
                <ul>
                    <li class="menu-title">导航</li>

                    <li>
                        <a href="<?=admincp_base("home");?>" class="waves-effect">
                            <i class="mdi mdi-airplay"></i>
                            <span>仪表板<span class="badge badge-pill badge-danger float-right">NEW</span></span>
                        </a>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">
                            <i class="fas fa-cogs"></i>
                            <span>网站设置</span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="<?=admincp_base("Settings_Web")?>">网站设置</a></li>
                            <li><a href="<?=admincp_base('Settings_Group')?>">分区配置</a></li>
                            <li><a href="<?=admincp_base('Settings_Model')?>">模块管理</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?=admincp_base('News_Manage')?>" class="waves-effect">
                            <i class="ion-navicon-round"></i>
                            <span>新闻管理</span>
                            <span class="float-right"><i class="ion-ios7-plus-empty"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?=admincp_base('Settings_Home')?>" class="waves-effect">
                            <i class="mdi mdi-cards-outline"></i>
                            <span>首页设置</span>
                            <span class="float-right"><i class="ion-ios7-plus-empty"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?=admincp_base('Settings_Buy')?>" class="waves-effect">
                            <i class="mdi mdi-currency-usd"></i>
                            <span>赞助设置</span>
                            <span class="float-right"><i class="ion-ios7-plus-empty"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?=admincp_base('Send_WebPost')?>" class="waves-effect">
                            <i class="fa fa-window-maximize"></i>
                            <span>网站公告</span>
                            <span class="float-right"><i class="ion-ios7-plus-empty"></i></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?=admincp_base('Send_GamePost')?>" class="waves-effect">
                            <i class="fa fa-window-maximize"></i>
                            <span>游戏公告</span>
                            <span class="float-right"><i class="ion-ios7-plus-empty"></i></span>
                        </a>
                    </li>
<!--                    <li>-->
<!--                        <a href="--><?//=admincp_base('Logs_pay')?><!--" class="waves-effect">-->
<!--                            <i class="mdi mdi-currency-usd"></i>-->
<!--                            <span>充值日志</span>-->
<!--                            <span class="float-right"><i class="ion-ios7-plus-empty"></i></span>-->
<!--                        </a>-->
<!--                    </li>-->
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect">

                            <i class="ion-ios7-monitor"></i>
                            <span>外观管理</span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="<?=admincp_base("Settings_TempLate")?>">模版主题</a></li>
                            <li><a href="<?=admincp_base("Settings_Sidebar")?>">侧边管理</a></li>
                            <li><a href="<?=admincp_base('Menu_Navbar')?>">导航菜单</a></li>
                            <li><a href="<?=admincp_base('Menu_Usercp')?>">用户面板</a></li>
                        </ul>
                    </li>
                    <li class="menu-title">模块组件</li>
                    <?php
                    foreach($adminCpSidebar as $sidebarItem) {

                        $itemIcon = (check_value($sidebarItem[2]) ? '<i class="fa '.$sidebarItem[2].' fa-fw"></i>&nbsp;' : '');
                        if(is_array($sidebarItem[1])) {
                            echo '<li class="has_sub">';
                            echo '<a href="javascript:void(0);" class="waves-effect">';
                            echo $itemIcon.$sidebarItem[0];
                            echo '<span class="float-right"><i class="mdi mdi-chevron-right"></i></span>';
                            echo '</a>';
                            echo '<ul class="list-unstyled">';
                            foreach($sidebarItem[1] as $sidebarSubItemModule => $sidebarSubItemTitle) {
                                if(check_value($sidebarSubItemTitle)) {
                                    echo '<li>';
                                    echo '<a href="'.admincp_base($sidebarSubItemModule).'">'.$sidebarSubItemTitle.'</a>';
                                    echo '</li>';
                                }
                            }
                            echo '</ul>';
                        } else {
                            echo '<a href="'.admincp_base($sidebarItem[1]).'">'.$itemIcon.$sidebarItem[0].'</a>';
                        }
                    }?>
                    <li class="menu-title">缓存系统</li>
                    <li>
                        <a href="<?=admincp_base('Cron_Manage')?>" class="waves-effect">
                            <i class="ion-android-storage"></i>
                            <span>缓存管理</span>
                            <span class="float-right"><i class="ion-ios7-plus-empty"></i></span>

                        </a>
                    </li>
                    <?php
                    if(isset($extra_admincp_sidebar)) {
                            echo '<li class="menu-title">插件列表</li>';
                            if(is_array($extra_admincp_sidebar)) {
                                echo '<li>';
                                    foreach($extra_admincp_sidebar as $pluginSidebarItem) {
                                        echo '<a href="'.admincp_base($pluginSidebarItem[1]).'" class="waves-effect">';
                                            echo '<i class="'.$pluginSidebarItem[2].'"></i>';
                                            echo '<span>'.$pluginSidebarItem[0].'</span>';
                                            echo '<span class="float-right"><i class="ion-ios7-plus-empty"></i></span>';
                                        echo '</a>';
                                    }
                                echo '</li>';
                            }
                        }
                    ?>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- end sidebarinner -->
    </div>
    <!-- 左侧结束 -->
    <!-- Start right Content here -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <!-- 顶部导航 -->
            <div class="topbar">
                <nav class="navbar-custom">
                    <ul class="list-inline float-right mb-0">
 
                        <li class="list-inline-item dropdown">
                            <a class="text-light" href="//wpa.qq.com/msgrd?V=1&uin=83213956&Menu=yes" target="_blank"><i class="fab fa-qq"></i> 技术支持</a>
                        </li>
                        <li class="list-inline-item dropdown">
                            <a class="text-light" href="<?php echo __BASE_URL__; ?>" target="_blank"><i class="fa fa-fw fa-home"></i> 网站主页</a>
                        </li>
                        <li class="list-inline-item dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <img src="assets/images/users/avatar-1.jpg" alt="user" class="rounded-circle">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5 class="text-center">快捷管理</h5>
                                </div>
                                <a class="dropdown-item" href="#">
                                    <i class="mdi mdi-account-circle m-r-5 text-muted"></i>
                                    <?=$_SESSION['username']; ?></a>
                                <a class="dropdown-item" href="#">
                                    <i class="mdi mdi-wallet m-r-5 text-muted"></i>
                                    自定义</a>
                                <a class="dropdown-item" href="#">
                                    <span class="badge badge-success float-right">5</span>
                                    <i class="mdi mdi-settings m-r-5 text-muted"></i>
                                    自定义</a>
                                <div class="dropdown-divider">
                                </div>
                                <a class="dropdown-item" href="<?php echo __BASE_URL__; ?>logout/">
                                    <i class="mdi mdi-logout m-r-5 text-muted"></i>
                                    退出登陆
                                </a>
                            </div>
                        </li>
                    </ul>
                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left">
                            <button class="button-menu-mobile open-left waves-light waves-effect">
                                <i class="mdi mdi-menu"></i>
                            </button>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </nav>
            </div>
            <!-- 顶部导航END -->
            <div class="page-content-wrapper ">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <?php
                            $req = isset($_REQUEST['module']) ? $_REQUEST['module'] : '';
                            $handler->loadAdminCPModule($req);
                            ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <!-- container -->
            </div>
            <!-- Page content Wrapper -->
        </div>
        <!-- content -->
        <footer class="footer">
            © 2022 奇迹网站系统 by <a href="//wpa.qq.com/msgrd?V=1&uin=827916830&Menu=yes" title="">风行</a>
        </footer>
    </div>
    <!-- End Right content here -->
</div>
<!-- END wrapper -->

    <!-- jQuery -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/modernizr.min.js"></script>
    <script src="assets/js/detect.js"></script>
    <script src="assets/js/fastclick.js"></script>
    <script src="assets/js/jquery.blockUI.js"></script>
    <script src="assets/js/waves.js"></script>
    <script src="assets/js/jquery.nicescroll.js"></script>

    <!-- Required datatable js -->
    <script src="assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Responsive examples -->
    <script src="assets/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="assets/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <!-- Dropzone js -->
    <script src="assets/plugins/dropzone/dist/dropzone.js"></script>
    <script src="assets/plugins/dropify/js/dropify.min.js"></script>
    <script src="assets/pages/upload.init.js"></script>
    <!-- Plugins js -->
    <script src="assets/plugins/timepicker/moment.js"></script>
    <script src="assets/plugins/timepicker/bootstrap-material-datetimepicker.js"></script>
    <script src="assets/plugins/clockpicker/jquery-clockpicker.min.js" ></script>
    <script src="assets/plugins/colorpicker/jquery-asColor.js"  type="text/javascript"></script>
    <script src="assets/plugins/colorpicker/jquery-asGradient.js" type="text/javascript"></script>
    <script src="assets/plugins/colorpicker/jquery-asColorPicker.min.js" type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js"  type="text/javascript"></script>
    <script src="assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js"  type="text/javascript"></script>
    <!-- Plugins Init js -->
    <script src="assets/pages/form-advanced.js"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <script>
        $(document).ready(function(){
            $('#datatable').DataTable({
                "oLanguage": {  //对表格国际化
                    "sLengthMenu": "每页显示 _MENU_ 条",
                    "sZeroRecords": "没有找到符合条件的数据",
                    "sInfo": "当前页第 _START_ - _END_ 条　共计 _TOTAL_ 条",
                    "sInfoEmpty": "木有记录",
                    "sInfoFiltered": "(从 _MAX_ 条记录中过滤)",
                    "sSearch": "搜索：",
                    "oPaginate": {
                        "sFirst": "首页",
                        "sPrevious": "前一页",
                        "sNext": "后一页",
                        "sLast": "尾页"
                    }
                }
            })
        }
        );
    </script>
</body>

</html>
<?php
#===============================
#测试所用 时间计算结尾
if($config['error_reporting']) {
    $mtimeTamp = sprintf("%.3f", microtime(true)); // 带毫秒的时间戳
    $timestamp = floor($mtimeTamp); // 时间戳
    $milliseconds = round(($mtimeTamp - $timestamp) * 1000); // 毫秒
    $datetime = date("H:i:s", $timestamp) . '.' . $milliseconds;
    $microTime = round((microtime(true)-$startTme)*1000,2).'ms';//访问时间戳(毫秒)
    print "<div style='position:fixed;background:rgba(0,0,0,0.6);right:0;color: #fff;padding: 10px;z-index:9;bottom:0;'>访问时间[<span style='color:#48f870'>$datetime</span>]，页面加载耗时:[<span style='color:#48f870'>$microTime</span>]！</div>";//打印加载时间
}
#===============================


