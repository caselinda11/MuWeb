<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

function saveChanges() {
	global $_POST;

	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'rankings.xml';
	$xml = simplexml_load_file($xmlPath);
	$xml->active = $_POST['active'];
	$xml->results = $_POST['setting_2'];
	$xml->show_date = $_POST['setting_3'];
	$xml->show_default = $_POST['setting_4'];
	$xml->show_place_number = $_POST['setting_5'];
	$xml->enable_level = $_POST['setting_6'];
	$xml->enable_guilds = $_POST['setting_11'];
	$xml->enable_gens = $_POST['enable_gens'];
	$xml->enable_votes = $_POST['enable_votes'];
	$xml->excluded_characters = $_POST['setting_16'];
    $xml->show_total_status = $_POST['totalStatus'];
    $xml->show_level_group = $_POST['show_level_group'];
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

$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'rankings.xml';
$moduleConfig = simplexml_load_file($xmlPath);
?>
<div class="card">
    <div class="card-header">在线排名系统设置</div>
    <div class="card-body">
        <form class="form" role="form" action="" method="post">
            <table class="table table-striped table-bordered table-hover module_config_tables">
                <tr>
                    <th width="60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 排名模块</span></th>
                    <td>
                        <?=enableDisableCheckboxes('active',$moduleConfig->active,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>排名条数</strong><span class="ml-3 text-muted">限制排名条数有效缓解排名访问性能</span></th>
                    <td>
                        <input class="input-mini" type="text" name="setting_2" value="<?=$moduleConfig->results; ?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>每页条数</strong><span class="ml-3 text-muted">设置排名分页的每一页显示多少条数据。</span></th>
                    <td>
                        <input class="input-mini" type="text" name="show_page" value="<?=$moduleConfig->show_page; ?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>更新日期</strong><span class="ml-3 text-muted">在排名底部显示每个排名的最后更新日期。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('setting_3',$moduleConfig->show_date,'是','否'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>默认排行</strong><span class="ml-3 text-muted">访问排名页面时默认显示的排名。</span>
                    </th>

                    <td>
                        <select name="setting_4" class="form-control" style="width:178.4px">
                            <option value="level"  <?=$moduleConfig->show_default == 'level' ? 'selected' : '' ?>>等级排名</option>
                            <option value="guilds" <?=$moduleConfig->show_default == 'guilds' ? 'selected' : '' ?>>战盟排名</option>
                            <option value="gens" <?=$moduleConfig->show_default == 'gens' ? 'selected' : '' ?>>家族排名</option>
                            <option value="votes"  <?=$moduleConfig->show_default == 'votes' ? 'selected' : '' ?>>推广排名</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><strong>等级排名</strong></th>
                    <td>
                        <?=enableDisableCheckboxes('setting_6',$moduleConfig->enable_level,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>战盟排名</strong></th>
                    <td>
                        <?=enableDisableCheckboxes('setting_11',$moduleConfig->enable_guilds,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>家族排名</strong></th>
                    <td>
                        <?=enableDisableCheckboxes('enable_gens',$moduleConfig->enable_gens,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>推广排名</strong></th>
                    <td>
                        <?=enableDisableCheckboxes('enable_votes',$moduleConfig->enable_votes,'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>排除角色</strong><span class="ml-3 text-muted">添加您要排除在排名之外的角色名（多条用逗号,分隔）。</span></th>
                    <td>
                        <input class="form-control" type="text" name="setting_16" value="<?=$moduleConfig->excluded_characters; ?>"/>
                    </td>
                </tr>
                <tr style="background: #d6e9c6">
                    <th><strong>特殊功能</strong><span class="ml-3 text-muted">隐藏某个大区不在排行榜中显示。</span><span class="text-danger">*仅多个大区可用</span></th>
                    <td>
                        <select class="form-control" name="show_level_group" id="show_level_group">
                                <option value="show">显示所有</option>
                            <? foreach (getServerGroupList() as $group=> $item){ ?>
                                <option value="<?=$group;?>" <?=selected($moduleConfig->show_level_group,(string)$group)?>><?=$item;?></option>
                            <?}?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><strong>总点数</strong><span class="ml-3 text-muted">如果启用角色的总点数信息将显示在排名中。</span></th>
                    <td>
                        <?=enableDisableCheckboxes('totalStatus',$moduleConfig->show_total_status,'显示','隐藏'); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center">
                        <button type="submit" name="submit_changes" value="submit" class="btn btn-success col-md-4">保存</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>