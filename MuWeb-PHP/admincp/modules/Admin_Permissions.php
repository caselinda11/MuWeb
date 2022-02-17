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
                        <li class="breadcrumb-item active">管理员访问权限</li>
                    </ol>
                </div>
                <h4 class="page-title">管理员访问权限</h4>
            </div>
        </div>
    </div>
<?php
if(check_value($_POST['settings_submit'])) {
    try {
        # configs
        $Configurations = webConfigs();

        # remove submit button element
        unset($_POST['settings_submit']);

        # check if module is in configs
        foreach($_POST as $moduleFile => $accessLevel) {
            if(!array_key_exists($moduleFile, config('admincp_modules_access'))) continue;
            if(!Validator::UnsignedNumber($accessLevel)) throw new Exception('访问权限必须是0到100之间的数字');
            if(!Validator::Number($accessLevel, 100, 0)) throw new Exception('访问权限必须是0到100之间的数字');
            $modulesConfig[$moduleFile] = (int) $accessLevel;
        }

        $Configurations['admincp_modules_access'] = $modulesConfig;

        $newSystemConfig = json_encode($Configurations, JSON_PRETTY_PRINT);
        $cfgFile = fopen(__PATH_CONFIGS__.'system.json', 'w');
        if(!$cfgFile) throw new Exception('打开配置文件时出现错误.');

        fwrite($cfgFile, $newSystemConfig);
        fclose($cfgFile);

        message('success', '设置成功保存!');
    } catch(Exception $ex) {
        message('error', $ex->getMessage());
    }
}

try {
$admincpModulesAccess = config('admincp_modules_access');

if(is_array($admincpModulesAccess)) {
    echo '<div class="card">
    <div class="card-body">';
    echo '<form action="" method="post">';
    echo '<table class="table table-striped table-bordered table-hover" style="table-layout: fixed;">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>模块</th>';
    echo '<th>访问权限(最高100)</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach($admincpModulesAccess as $module_file => $moduleAccess) {
        echo '<tr>';
        echo '<td>';
        echo '<strong>'.$module_file.'</strong>';
        echo '</td>';
        echo '<td>';
        echo '<input type="number" class="form-control" min="0" max="100" name="'.$module_file.'" value="'.$moduleAccess.'" required>';
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '<div style="text-align:center">';
    echo '<button type="submit" name="settings_submit" value="ok" class="btn btn-success col-md-3">保存</button>';
    echo '</div>';
    echo '</form>';
    echo '</div></div>';
} else {
    message('error', '模块列表为空.');
}
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}