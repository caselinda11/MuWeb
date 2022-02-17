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
                        <li class="breadcrumb-item active">任务系统</li>
                        <li class="breadcrumb-item active">任务记录</li>
                    </ol>
                </div>
                <h4 class="page-title">任务记录</h4>
            </div>
        </div>
    </div>
<?php
try {
    $database = Connection::Database("Web");

    $currentMonth = date("m");
//if($currentMonth >11) $currentMonth = 0;
    $nextMonth = $currentMonth + 1;

    $ts1 = strtotime(date("m/01/Y 00:00"));
    $ts2 = strtotime(date("$nextMonth/01/Y 00:00"));

    $voteLogs = $database->query_fetch("SELECT TOP 100 user_id,servercode,COUNT(*) as totalvotes FROM  ".X_TEAM_VOTE_LOGS." GROUP BY user_id,servercode ORDER BY totalvotes DESC");
    if ($voteLogs && is_array($voteLogs)) {
        ?>
        <div class="card">
        <div class="card-body">
        <table id="datatable" class="table table-hover text-center">
        <tr>
        <th>No</th>
        <th>账号</th>
        <th>推广次数</th>
        <th>所属大区</th>
        </tr>
    <?
        $common = new common();
        foreach ($voteLogs as $key => $thisVote) {

            $accountInfo = $common->getUserInfoForUserGID($thisVote['servercode'],$thisVote['user_id']);
            $keyx = $key + 1;
            ?>
            <tr>
            <td><?=$keyx?></td>
            <td><?=$accountInfo[_CLMN_USERNM_]?></td>
            <td><?=$thisVote['totalvotes']?></td>
            <td><?=getGroupNameForServerCode($thisVote['servercode'])?></td>
            </tr>
    <?}?>
        </table>
        </div>
        </div>
<?php
    } else {
        message('error', '找不到推广记录。 此功能需要启用推广日志.');
    }
}catch (Exception $exception){
    message('error', $exception->getMessage());
}
?>