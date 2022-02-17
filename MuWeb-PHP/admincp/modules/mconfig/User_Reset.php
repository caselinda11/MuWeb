<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
try {
    function saveChanges()
    {
        global $_POST;
        foreach ($_POST as $setting) {
            if (!check_value($setting)) {
                message('error', '缺少数据（请填写所有字段）');
                return;
            }
        }
        $xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__ . 'usercp.reset.xml';
        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->resets_price_zen = $_POST['resets_price_zen'];
        $xml->resets_required_level = $_POST['resets_required_level'];
        $xml->resets_credits_reward = $_POST['resets_credits_reward'];
        $xml->resets_max = $_POST['resets_max'];
        $xml->credit_type = $_POST['credit_type'];
        $xml->resets_point = $_POST['resets_point'];
        $xml->resets_stats_point = $_POST['resets_stats_point'];
        $save = $xml->asXML($xmlPath);

        if ($save) {
            message('success', '已成功保存.');
        } else {
            message('error', '保存时发生错误.');
        }
    }

    if (check_value($_POST['submit_changes'])) {
        saveChanges();
    }
    if (check_value($_POST['submit'])) {
        $web = Connection::Database("Web");
        switch ($_POST['submit']) {
            case "add":#添加
                try {
                    #验证表是否存在
                    $check = $web->query("select name from sysobjects where id = object_id('X_TEAM_RESET_AWARD')");
                    if (!is_array($check)){
                        $web->query("CREATE TABLE [X_TEAM_RESET_AWARD] ([ID] [int] IDENTITY(1,1) NOT NULL, [award_count] [int] NULL, [award_name] [varchar](50) NULL, [award_item] [varchar](255) NULL, [award_description] [text] NULL, [status] [bit] DEFAULT((1)) NOT NULL)");
                        $web->query("CREATE TABLE [X_TEAM_RESET_AWARD_LOG] ([ID] [int] IDENTITY(1,1) NOT NULL, [award_id] [int] NOT NULL,[servercode] [int] NOT NULL, [username] [varchar](10) NOT NULL,[character] [varchar](10) NOT NULL, [receive_time] [smalldatetime] NULL)");
                        $web->query("INSERT INTO [X_TEAM_COMBINE_SERVER] ([Query]) VALUES ('X_TEAM_RESET_AWARD_LOG')");
                    }
                    if (!check_value($_POST['award_count'])) throw new Exception("请填写转身要求。");
                    if (!check_value($_POST['award_name'])) throw new Exception("请填写奖励的物品名称。");
                    if (!check_value($_POST['award_item'])) throw new Exception("请填写奖励的物品代码。");
                    if (!Validator::Items($_POST['award_item'])) throw new Exception("请正确输入物品代码。");
                    $query = $web->query("INSERT INTO [X_TEAM_RESET_AWARD] ([award_count],[award_name],[award_item],[award_description]) VALUES (?,?,?,?)", [$_POST['award_count'], $_POST['award_name'], $_POST['award_item'], $_POST['award_description']]);
                    if (!$query) throw new Exception("添加失败，请确保数据的正确性！");
                    message("success", "添加成功！");
                } catch (Exception $exception) {
                    message("error", $exception->getMessage());
                }
                break;
            case "edit":
                try {
                    if (!check_value($_POST['ID'])) throw new Exception("请求错误，请重新提交。");
                    if (!check_value($_POST['award_count'])) throw new Exception("请填写转身要求。");
                    if (!check_value($_POST['award_name'])) throw new Exception("请填写奖励的物品名称");
                    if (!check_value($_POST['award_item'])) throw new Exception("请填写奖励的物品代码");
                    if (!Validator::Items($_POST['award_item'])) throw new Exception("请正确输入物品代码。");
                    $query = $web->query("UPDATE [X_TEAM_RESET_AWARD] SET [award_count] = ?,[award_name] = ? ,[award_item] = ?,[award_description] = ? WHERE [ID] = ?", [$_POST['award_count'], $_POST['award_name'], $_POST['award_item'], $_POST['award_description'], $_POST['ID']]);
                    if (!$query) throw new Exception("编辑失败，请确保数据的正确性！");
                    message("success", "编辑成功！");
                } catch (Exception $exception) {
                    message("error", $exception->getMessage());
                }
                break;
            case "delete":
                try {
                    $del = Connection::Database("Web")->query("DELETE FROM [X_TEAM_RESET_AWARD] WHERE [ID] = ?", [$_POST['ID']]);
                } catch (Exception $exception) {
                    message("error", $exception->getMessage());
                }
                break;
            default:
                message("error", "禁止非法提交！");
                break;
        }
    }
    $awardList = Connection::Database("Web")->query_fetch("select * from [X_TEAM_RESET_AWARD]");
    loadModuleConfigs('usercp.reset');
    $creditSystem = new CreditSystem();
    message('info', '转身后角色初始属性点基于你的端配置文件,在includes/config/table/' . config('server_files') . '.table.php');

    ?>
    <div class="card">
        <div class="card-header">角色转身设置</div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover module_config_tables">
                    <tr>
                        <th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 角色转身模块.</span>
                        </th>
                        <td>
                            <?= enableDisableCheckboxes('active', mconfig('active'), '启用', '禁用'); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><strong>出生点数</strong><span class="ml-3 text-muted">默认角色送的出生点</span></th>
                        <td>
                            <input class="form-control" type="text" name="resets_point"
                                   value="<?= mconfig('resets_point') ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><strong>金币(Zen)</strong><span class="ml-3 text-muted">使用该功能的金币需求，设0则不需要。</span></th>
                        <td>
                            <input class="form-control" type="text" name="resets_price_zen"
                                   value="<?= mconfig('resets_price_zen') ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><strong>最高转身</strong><span class="ml-3 text-muted">设置一个封顶转身次数。</span></th>
                        <td>
                            <input class="form-control" type="text" name="resets_max"
                                   value="<?= mconfig('resets_max') ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><strong>需要等级</strong><span class="ml-3 text-muted">角色需要达到多少级(普通等级)才能使用该功能。</span></th>
                        <td>
                            <input class="form-control" type="text" name="resets_required_level"
                                   value="<?= mconfig('resets_required_level') ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <th><strong>货币数量</strong><span class="ml-3 text-muted">转身后奖励的货币数量，设0则不奖励。</span></th>
                        <td>
                            <input class="form-control" type="text" name="resets_credits_reward"
                                   value="<?= mconfig('resets_credits_reward') ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><strong>奖励币种</strong><span class="ml-3 text-muted">奖励的货币类型，如果设置了货币数量此项为必填项。</span></th>
                        <td>
                            <?= $creditSystem->buildSelectInput("credit_type", mconfig('credit_type'), "form-control"); ?>
                        </td>
                    </tr>

                    <tr>
                        <th><strong>奖励点数</strong><span class="ml-3 text-muted">转身后奖励的属性点，设0则不奖励。</span></th>
                        <td>
                            <input class="form-control" type="text" name="resets_stats_point"
                                   value="<?= mconfig('resets_stats_point') ?>"/>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2">
                            <div style="text-align:center">
                                <button type="submit" name="submit_changes" value="submit"
                                        class="btn btn-success waves-effect waves-light col-md-2">保存
                                </button>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">转身奖励</div>
        <div class="">

            <table class="table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th>转身次数要求</th>
                    <th>奖励物品名称</th>
                    <th>奖励物品代码</th>
                    <th>奖励奖励说明</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <? if (is_array($awardList)) {?>
                    <? foreach ($awardList as $data) {?>
                        <form action="" method="post">
                            <tr>
                                <td><input type="text" class="form-control" name="award_count" value="<?= $data['award_count'] ?>"/></td>
                                <td><input type="text" class="form-control" name="award_name" value="<?= $data['award_name'] ?>"/></td>
                                <td><input type="text" class="form-control" name="award_item" value="<?= $data['award_item'] ?>"/></td>
                                <td><input type="text" class="form-control" name="award_description" value="<?= $data['award_description'] ?>"/></td>
                                <td>
                                    <div class="btn-group">
                                        <input type="hidden" name="ID" value="<?= $data['ID'] ?>"/>
                                        <button name="submit" value="edit" class="btn btn-primary">编辑</button>
                                        <button name="submit" value="delete" class="btn btn-danger">删除</button>
                                    </div>
                                </td>
                            </tr>
                        </form>
                    <? } ?>
                <? } ?>
                <tr>
                    <td colspan="6"><strong>添加新的</strong></td>
                </tr>
                <form action="" method="post">
                    <tr>
                        <td><input type="text" class="form-control" name="award_count"/></td>
                        <td><input type="text" class="form-control" name="award_name"/></td>
                        <td><input type="text" class="form-control" name="award_item"/></td>
                        <td><input type="text" class="form-control" name="award_description"/></td>
                        <td>
                            <div class="">
                                <button name="submit" value="add" class="btn btn-success col-md-12">添加</button>
                            </div>
                        </td>
                    </tr>
                </form>
                </tbody>
            </table>

        </div>
    </div>
    <?php
}catch (Exception $e){
    message('error',$e->getMessage());
}