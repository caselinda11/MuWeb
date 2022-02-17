<?php
/**
 * 定时任务管理
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
                        <li class="breadcrumb-item active">定时任务</li>
                        <li class="breadcrumb-item active">定时任务管理</li>
                    </ol>
                </div>
                <h4 class="page-title">定时任务管理</h4>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="float-right mb-2">
                <a class="btn btn-success btn-lg" href="<?php echo admincp_base("Cron_Add");?>">新增任务</a>
                <a class="btn btn-warning btn-lg" href="index.php?module=<?=$_REQUEST['module']?>&make=1">更新缓存</a>
                <a class="btn btn-danger btn-lg" href="index.php?module=<?=$_REQUEST['module']?>&reset=1">重置缓存</a>
            </div>
        </div>
    </div>
<?php
try {
    #更新缓存
    if(check_value($_REQUEST['make']) && $_REQUEST['make'] == 1) {
    	if(runCronJob()) {
    		message('success','定时更新已执行成功!!');
    	} else {
    		message('error', '无法执行定时任务,如果您是linux系统请手动添加定时任务!!');
    	}
    }
    #重置缓存
    $database = Connection::Database("Web");
    if(check_value($_REQUEST['reset']) && $_REQUEST['reset'] == 1) {
        updateCacheFile('rankings_guilds.cache', '');
        updateCacheFile('rankings_level.cache', '');
        updateCacheFile('rankings_gens.cache', '');
        updateCacheFile('rankings_votes.cache', '');
        updateCacheFile('server_info.cache', '');
        updateCacheFile('castle_siege.cache', '');
        updateCacheFile('rankings_money.cache', '');
        updateCacheFile('rankings_ban.cache', '');
        updateCacheFile('rankings_buy.cache', '');
        updateCacheFile('rankings_shops.cache', '');
        updateCacheFile('BFB_info.cache', '');
        $resetCron = $database->query("UPDATE [".X_TEAM_CRON."] SET [cron_last_run] = NULL");
        redirect(2, 'admincp/?module=Cron_Manage',5);
        if($resetCron) {
            message('success','定时任务计划成功重置!');
        } else {
            message('error', '无法重置定时任务计划!');
        }
    }
    #删除缓存
    if(check_value($_REQUEST['delete'])) {
        deleteCronJob($_REQUEST['delete']);
    }
    #停用
    if(check_value($_REQUEST['toggleStatus'])) {
        toggleStatusCronJob($_REQUEST['toggleStatus']);
    }
    if(check_value($_POST['submit'])){
        if($_POST['submit'] == 'edit'){
            try {
                if (!check_value($_POST['id'])) throw new Exception("数据错误，请重新提交。");
                if (!check_value($_POST['run_time'])) throw new Exception("请正确输入缓存间隔时间。");
                if (!Validator::UnsignedNumber($_POST['run_time'])) throw new Exception("缓存间隔时间必须是纯数字。");
                $update = $database->query("UPDATE [".X_TEAM_CRON."] SET [cron_run_time] = ? WHERE [cron_id] = ?",[$_POST['run_time'],$_POST['id']]);
                if(!$update) throw new Exception("数据操作失败，请确保数据的正确性。");
                message('success','缓存时间编辑成功。');
            }catch (Exception $exception){
                message('error',$exception->getMessage());
            }
        }
    }
    $cronJobs = $database->query_fetch("SELECT * FROM [".X_TEAM_CRON."] ORDER BY [cron_id] ASC");
    if(is_array($cronJobs)) {?>

       <div class="card">
           <div class="card-header">定时任务列表</div>
        <div class="card-body">
            <table class="table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th>定时任务</th>
                    <th>执行文件</th>
                    <th>状态</th>
                    <th>间隔时间(秒)</th>
                    <th>上次执行时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
            <?foreach($cronJobs as $thisCron) {
                if(is_null($thisCron['cron_last_run'])) {
                    $thisCron['cron_last_run'] = '<i>无记录</i>';
                } else {
                    $thisCron['cron_last_run'] = date("Y/m/d H:i", $thisCron['cron_last_run']);
                }
            ?>
                <form class="form-horizontal" action="" method="post">
                    <tr>
                        <td>
                            <strong><?=$thisCron['cron_name']?></strong>
                            <br />
                            <small><?=$thisCron['cron_description']?></small>
                        </td>
                        <td><?=$thisCron['cron_file_run']?></td>
                        <td><a href="index.php?module=Cron_Manage&toggleStatus=<?=$thisCron['cron_id']?>"><?=$thisCron['cron_status'] ? '<span class="ion-toggle-filled font-32 text-success" data-toggle="tooltip" data-placement="left" title="点击禁用"></span>' : '<span class="ion-toggle font-32" data-toggle="tooltip" data-placement="left" title="点击启用"></span>'?></a></td>
                        <td><input type="text" class="form-control" name="run_time" value="<?=$thisCron['cron_run_time']?>"/></td>
                        <td><?=$thisCron['cron_last_run']?></td>
                        <td>
                            <input type="hidden" name="id" value="<?=$thisCron['cron_id']?>"/>
                            <button name="submit" value="edit" class="btn btn-primary">编辑</button>
                            <a href="index.php?module=Cron_Manage&delete=<?=$thisCron['cron_id']?>" class="btn btn-danger">删除</a>
                        </td>
                    </tr>
                </form>
            <?}?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    } else {
        message('error','没有添加定时任务计划!');
    }
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
