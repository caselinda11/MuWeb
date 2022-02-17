<?php
/**
 * 在线商城插件后台模块
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
                        <li class="breadcrumb-item active">插件系统</li>
                        <li class="breadcrumb-item active">系统任务</li>
                    </ol>
                </div>
                <h4 class="page-title">系统任务</h4>
            </div>
        </div>
    </div>
<?php
    $vote = new Vote();
    try {
        if (check_value($_POST['submit'])) {
            submit();
        }
        $propagate = new \Plugin\propagate();
        if (check_value($_POST['add_submit'])) {

            $add = $propagate->addVoteSite($_POST['add_title'], $_POST['add_link'], $_POST['add_reward'], $_POST['add_time']);
            if ($add) {
                message('success', '系统任务链接已成功添加!');
            } else {
                message('error', '添加系统任务链接时出错!');
            }
        }

        if (check_value($_REQUEST['delete'])) {
            $delete = $vote->deleteVoteSite($_REQUEST['delete']);
            if ($delete) {
                message('success', '系统任务链接已成功删除!.');
            } else {
                message('error', '删除系统任务链接时出现错误!');
            }
        }
    }catch (Exception $exception){
            message('error',$exception->getMessage());
    }
try {
    $moduleConfig = $propagate->loadConfig();
    $creditSystem = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            系统任务
            <a href="<?= admincp_base('/Plugin/propagate_log'); ?>" class="border border-danger rounded text-danger">
                任务日志
            </a>
        </div>
        <div class="card-body">
            <p></p>
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td width="60%"><strong>模块状态</strong><br><span
                                    class="text-muted">禁用/启用 启用此扩展必须禁用 <kbd>模块管理</kbd>中的<kbd>系统任务</kbd>功能</span></td>
                        <td><?=enableDisableCheckboxes('active', $moduleConfig['active'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>日志生成</strong><br><span class="text-muted">禁用/启用 如果启用将在数据库中写入日志 </span> <strong>日志表:[X_TEAM_VOTE_LOGS]</strong></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <th><strong>货币类型</strong></th>
                        <td>
                            <?php echo $creditSystem->buildSelectInput("credit_config", 0, "form-control"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="text-align:center">
                                <button type="submit" name="submit" value="submit" class="btn btn-success col-md-2">保存
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <?php $votesiteList = $vote->retrieveVoteSite();
    if (is_array($votesiteList)) { ?>

        <div class="card">
            <div class="card-header">管理推广网站</div>
            <div class="">
                <table class="table table-bordered text-center">
                    <tr>
                        <th>标题</th>
                        <th>链接（完整的网址，包括http）</th>
                        <th>奖励物品</th>
                        <th>限制 [单位:小时]</th>
                        <th></th>
                    </tr>
                    <? foreach ($votesiteList as $thisVoteSite){ ?>
                        <tr>
                            <td><?= $thisVoteSite['votesite_title'] ?></td>
                            <td><?= $thisVoteSite['votesite_link'] ?></td>
                            <td><?= $thisVoteSite['reward_item'] ?></td>
                            <td><?= $thisVoteSite['votesite_time'] ?></td>
                            <td>
                                <a href="index.php?module=Plugin/propagate&delete=<?=$thisVoteSite['votesite_id']?>"
                                   class="btn btn-danger col-md-12"><i class="ion-trash-b"></i></a>
                            </td>
                        </tr>
                    <? } ?>
                </table>
            </div>
        </div>
    <? } ?>

    <div class="card">
        <div class="card-header">添加推广网站</div>
        <div class="">
            <table class="table table-bordered table-hover text-center">
                <tr>
                    <th>标题</th>
                    <th>推广链接 [完整的网址，包括http]</th>
                    <th>奖励物品[代码]</th>
                    <th>限制 [单位:小时]</th>
                    <th></th>
                </tr>
                <form action="index.php?module=Plugin/propagate" method="post">
                    <tr>
                        <td><input name="add_title" class="form-control" type="text"/></td>
                        <td><input name="add_link" class="form-control" type="text"/></td>
                        <td><input name="add_reward" class="form-control" type="text"/></td>
                        <td><input name="add_time" class="form-control" type="text"/></td>
                        <td><input type="submit" name="add_submit" class="btn btn-success col-md-12" value="添加"/></td>
                    </tr>
                </form>
            </table>
        </div>
    </div>
<?php
    function submit()
    {
        $xmlPath = __PATH_PLUGIN_PROPAGATE_ROOT__ . 'config.xml';
        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->credit_config = $_POST['credit_config'];

        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '保存时发生错误！');
        }
    }
}catch (Exception $exception){
        message('error',$exception->getMessage());
} ?>



