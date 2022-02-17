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
                        <li class="breadcrumb-item active">禁用系统</li>
                        <li class="breadcrumb-item active">封停IP</li>
                    </ol>
                </div>
                <h4 class="page-title">封停IP - [仅网站]</h4>
            </div>
        </div>
    </div>
<?php
$common = new common();

if(check_value($_POST['ip_address'])) {
    try {
        if($common->blockIpAddress($_POST['ip_address'],$_SESSION['username'])) {
            message('success','IP地址已经封停');
        } else {
            message('error','IP地址错误,无法封停!');
        }
    }catch (Exception $ex){
        message('error', $ex->getMessage());
    }
}else if(check_value($_GET['unblock'])) {
    try {
        if($common->unblockIpAddress($_REQUEST['unblock'])) {
            message('success','IP地址已经解封!');
        } else {
            message('error','IP地址错误,无法解封!');
        }
    }catch (Exception $ex){
        message('error', $ex->getMessage());
    }
}
?>
    <div class="card">
        <div class="card-header">搜索</div>
        <div class="card-body mb-3">
            <div class="col-md-12">
                <form action="" method="post" role="form">
                    <div class="row justify-content-center">
                        <div class="input-group col-md-3 mt-2">
                            <input type="text" class="form-control" name="ip_address" placeholder="0.0.0.0" aria-label="0.0.0.0">
                            <span class="input-group-append">
                           <button class="btn btn-primary" type="submit" name="submit_block" value="ok">封停</button>
                        </span>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3  font-14">
                封停IP地址
            </footer>
        </div>
    </div>
<?php
try {
    $blockedIPs = $common->retrieveBlockedIPs();
    if (is_array($blockedIPs)) {
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<table id="datatable" class="table table-striped table-condensed table-hover text-center">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>IP地址</th>';
        echo '<th>操作管理员</th>';
        echo '<th>日期</th>';
        echo '<th></th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($blockedIPs as $thisIP) {
            $thisGroup = $common->getGroupForUsername($thisIP['block_by'],false);
            echo '<tr>';
            echo '<td>' . $thisIP['block_ip'] . '</td>';
            echo '<td><a class="btn btn-outline-success" href="'.admincp_base("Account_Info&group=".$thisGroup."&id=".$common->getUserGIDForUsername(getGroupIDForServerCode($thisGroup),$thisIP['block_by'])).'">'.$thisIP['block_by'].'</a></td>';
            echo '<td>' . date("m/d/Y H:i", $thisIP['block_date']) . '</td>';
            echo '<td><a href="' . admincp_base($_REQUEST['module'] . "&unblock=" . $thisIP['id']) . '" class="btn btn-xs btn-danger">解锁</a></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}catch (Exception $ex){
    message('error', $ex->getMessage());
}