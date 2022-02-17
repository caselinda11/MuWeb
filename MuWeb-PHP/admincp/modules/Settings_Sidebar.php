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
                    <li class="breadcrumb-item active">外观管理</li>
                    <li class="breadcrumb-item active">侧边管理</li>
                </ol>
            </div>
            <h4 class="page-title">侧边栏管理</h4>
        </div>
    </div>
</div>
<?php
try{
    try{
        function saveChanges() {
            global $_POST;
            foreach($_POST as $setting) {
                if(!check_value($setting)) {
                    message('error','缺少数据（请填写所有字段）');
                    return;
                }
            }
            $xmlPath = __PATH_INCLUDES_CONFIGS__.'sidebar.xml';
            $xml = simplexml_load_file($xmlPath);

            $xml->active = $_POST['active'];
            $xml->login_sidebar = $_POST['login_sidebar'];
            $xml->usercp_sidebar = $_POST['usercp_sidebar'];
            $xml->count_sidebar = $_POST['count_sidebar'];
            $xml->cc_sidebar = $_POST['cc_sidebar'];
            $xml->bfk_sidebar = $_POST['bfk_sidebar'];
            $xml->qq_link_sidebar = $_POST['qq_link_sidebar'];
            $xml->qqun_link_sidebar = $_POST['qqun_link_sidebar'];
            $xml->gens_count_sidebar = $_POST['gens_count_sidebar'];
            $xml->level_ranking_sidebar = $_POST['level_ranking_sidebar'];
            $xml->guild_ranking_sidebar = $_POST['guild_ranking_sidebar'];
            $xml->gens_ranking_sidebar = $_POST['gens_ranking_sidebar'];
            $xml->open_time = $_POST['open_time'];
            $xml->event_sidebar = $_POST['event_sidebar'];

            $save = $xml->asXML($xmlPath);
            if($save) {
                message('success','已成功保存.');
            } else {
                message('error','保存时发生错误.');
            }
        }

        if(check_value($_POST['submit_changes'])) {
            saveChanges();
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }

    #读取侧边栏配置文件
    $cfg = gconfig('sidebar');
    ?>
    <div class="card">
        <div class="card-header">侧边栏管理</div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover module_config_tables">
                    <tbody>
                        <tr>
                            <td style="width: 60%"><strong>状态</strong><span class="ml-3 text-muted">启用/禁用 是否启用显示侧边栏</span></td>
                            <td><?=enableDisableCheckboxes('active',$cfg['active'],'启用','禁用'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>开区时间</strong><span class="ml-3 text-muted">格式: XXXX-MM-DD，例如：2020-01-01</span></td>
                            <td><input type="text" name="open_time" class="form-control" value="<?=$cfg['open_time']?>" /></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>账号登陆</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏登陆模块</span></td>
                            <td><?=enableDisableCheckboxes('login_sidebar',$cfg['login_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>用户面板</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏用户面板(登录后)</span></td>
                            <td><?=enableDisableCheckboxes('usercp_sidebar',$cfg['usercp_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>统计信息</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏服务器统计信息</span></td>
                            <td><?=enableDisableCheckboxes('count_sidebar',$cfg['count_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>罗兰信息</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏罗兰城主统计信息</span></td>
                            <td><?=enableDisableCheckboxes('cc_sidebar',$cfg['cc_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>冰风谷信息</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏冰风谷堡主统计信息(*暂时仅限EG端使用)</span></td>
                            <td><?=enableDisableCheckboxes('bfk_sidebar',$cfg['bfk_sidebar'],'是','否'); ?></td>
                        </tr>

                        <tr>
                            <td style="width: 60%"><strong>腾讯QQ链接</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏腾讯QQ链接</span></td>
                            <td><?=enableDisableCheckboxes('qq_link_sidebar',$cfg['qq_link_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>腾讯Q群链接</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏腾讯Q群链接</span></td>
                            <td><?=enableDisableCheckboxes('qqun_link_sidebar',$cfg['qqun_link_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>家族信息</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏家族统计信息</span></td>
                            <td><?=enableDisableCheckboxes('gens_count_sidebar',$cfg['gens_count_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>等级排名</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏等级排名统计</span></td>
                            <td><?=enableDisableCheckboxes('level_ranking_sidebar',$cfg['level_ranking_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>战盟排名</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏战盟排名统计 <strong>[无]</strong></span></td>
                            <td><?=enableDisableCheckboxes('guild_ranking_sidebar',$cfg['guild_ranking_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>家族排名</strong><span class="ml-3 text-muted">是/否 是否显示侧边栏家族排名统计</span></td>
                            <td><?=enableDisableCheckboxes('gens_ranking_sidebar',$cfg['gens_ranking_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 60%"><strong>事件倒计时</strong><span class="ml-3 text-muted">是/否 显示侧边栏游戏事件倒计时(*修改时间请在api/events.json中修改。)</span></td>
                            <td><?=enableDisableCheckboxes('event_sidebar',$cfg['event_sidebar'],'是','否'); ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
<?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
