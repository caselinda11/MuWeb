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
                        <li class="breadcrumb-item active">权限管理</li>
                        <li class="breadcrumb-item active">管理员管理</li>
                    </ol>
                </div>
                <h4 class="page-title">管理员管理</h4>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php
if(check_value($_POST['settings_submit'])) {
    try {
        #  configs
        $Configurations = webConfigs();

        $newAdminUser = $_POST['new_admin'];
        $newAdminLevel = $_POST['new_access'];

        # remove elements
        unset($_POST['settings_submit']);
        unset($_POST['new_admin']);
        unset($_POST['new_access']);

        # check configs
        foreach($_POST as $adminUsername => $accessLevel) {
            if(!Validator::AlphaNumeric($adminUsername)) throw new Exception('输入的账号无效.');
            if(!Validator::UsernameLength($adminUsername)) throw new Exception('输入的账号无效.');
            if(!array_key_exists($adminUsername, config('admins'))) continue;
            if(!Validator::UnsignedNumber($accessLevel)) throw new Exception('访问权限必须是0到100之间的数字');
            if(!Validator::Number($accessLevel, 100, 0)) throw new Exception('访问权限必须是0到100之间的数字');
            if($accessLevel == 0) {
                if($adminUsername == $_SESSION['username']) throw new Exception('您无法删除自己！');
                continue; # admin removal
            }

            $adminAccounts[$adminUsername] = (int) $accessLevel;
        }

        if(check_value($newAdminUser)) {
            if(array_key_exists($newAdminUser, config('admins'))) throw new Exception('具有的相同管理员账号已在列表中!');
            if(!Validator::UnsignedNumber($newAdminLevel)) throw new Exception('访问权限必须是0到100之间的数字!');
            if(!Validator::Number($newAdminLevel, 100, 0)) throw new Exception('访问权限必须是0到100之间的数字!');

            $adminAccounts[$newAdminUser] = (int) $newAdminLevel;
        }

        $Configurations['admins'] = $adminAccounts;

        $newSystemConfig = json_encode($Configurations, JSON_PRETTY_PRINT);
        $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'system.json', 'w');
        if(!$cfgFile) throw new Exception('打开配置文件时出现错误.');

        fwrite($cfgFile, $newSystemConfig);
        fclose($cfgFile);

        message('success', '设置成功保存!');

    } catch(Exception $ex) {
        message('error', $ex->getMessage());
    }
}
try {
$admins = config('admins');

if(is_array($admins)) {
    echo '<div class="card">
    <div class="card-body">';
    echo '<form action="" method="post">';
    echo '<table class="table table-striped table-bordered table-hover" style="table-layout: fixed;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>管理员账号</th>';
    echo '<th>访问权限(100最高)</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($admins as $admin_account => $access_level) {
        echo '<tr>';
        echo '<td>';
        echo '<strong>'.$admin_account.'</strong>';
        echo '</td>';
        echo '<td>';
        echo '<input type="number" class="form-control" min="0" max="100" name="'.$admin_account.'" value="'.$access_level.'" required>';
        echo '</td>';
        echo '</tr>';
    }
    echo '<tr>';
    echo '<td>';
    echo '<input type="text" class="form-control" min="0" max="100" name="new_admin" placeholder="账号">';
    echo '</td>';
    echo '<td>';
    echo '<input type="number" class="form-control" min="0" max="100" name="new_access" placeholder="0">';
    echo '</td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    echo '<div style="text-align:center">';
    echo '<button type="submit" name="settings_submit" value="ok" class="btn btn-success col-md-3">保存</button>';
    echo '</div>';
    echo '</form>';
    echo '</div></div>';
} else {
    message('error', '管理员列表为空.');
}
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}