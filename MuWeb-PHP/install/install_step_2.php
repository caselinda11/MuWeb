<?php
/**
 * 安装程序第二步
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();
if(check_value($_POST['install_step_1_submit'])) {
    try {
        # 移至下一步
        $_SESSION['install_cstep']++;
        header('Location: install.php');
        die();
    } catch (Exception $ex) {

        echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';

    }
}
 ?>
<div class="progress mb-3">
    <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 30%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">30%</div>
</div>
    <div class="card"><?global $install;?>
    <div class="card-header"><?=$install['step_list'][2][1]?></div>
        <table class="table table-striped table-sm">
            <tbody>
    <?
if(is_array($writablePaths)) {
    foreach($writablePaths as $filepath) {
        if(file_exists(__PATH_INCLUDES__ . $filepath)) {
            if(!is_writable(__PATH_INCLUDES__ . $filepath)) {
                echo '<tr>';
                    echo '<th>'.$filepath.'</th>';
                    echo '<td class="text-right"><span class="badge badge-warning">不可写</span></td>';
                echo '</tr>';
            } else {
                echo '<tr>';
                    echo '<th>'.$filepath.'</th>';
                    echo '<td class="text-right"><span class="badge badge-success">正常</span></td>';
                echo '</tr>';
            }
        } else {
            echo '<tr>';
                echo '<th>'.$filepath.'</th>';
                echo '<td class="text-right"><span class="badge badge-danger">文件丢失</span></td>';
            echo '</tr>';
        }
    }
}
?>
            </tbody>
        </table>
        <p class="text-center text-danger">我们强烈建议您在继续操作之前先解决上述所有问题!</p>
        <form class="form-group row justify-content-md-center" action="" method="post">
            <button type="submit" name="install_step_1_submit" value="ok" class="btn btn-success mr-3 col-md-3">下一步</button>
            <a href="<?=__INSTALL_URL__?>install.php" class="btn btn-primary">重新检查</a>
            </form>
    </div>
