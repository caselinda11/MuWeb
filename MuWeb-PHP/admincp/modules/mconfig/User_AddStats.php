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
	foreach($_POST as $setting) {
		if(!check_value($setting)) {
			message('error','缺少数据（请填写所有字段）');
			return;
		}
	}
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'usercp.addstats.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['active'];
	$xml->addstats_price_zen = $_POST['addstats_price_zen'];
    $xml->addstats_mini_points = $_POST['addstats_mini_points'];
	$xml->addstats_max_stats = $_POST['addstats_max_stats'];

	
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

loadModuleConfigs('usercp.addstats');
?>
<div class="card">
    <div class="card-header">加点设置</div>
    <div class="card-body">
        <form action="" method="post">
            <table class="table table-striped table-bordered table-hover module_config_tables">
                <tr>
                    <th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 在线加点模块.</span></th>
                    <td>
                        <?=enableDisableCheckboxes('active',mconfig('active'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>金币(Zen)</strong><span class="ml-3 text-muted">设置使用该功能的金币(Zen)价格，设0则不需要。</span></th>
                    <td>
                        <input class="form-control" type="text" name="addstats_price_zen" value="<?=mconfig('addstats_price_zen')?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>最小点数</strong><span class="ml-3 text-muted">使用网站加点功能最少升级点数.</span></th>
                    <td>
                        <input class="form-control" type="text" name="addstats_mini_points" value="<?=mconfig('addstats_mini_points')?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>最大点数</strong><span class="ml-3 text-muted">限制每项属性点加点的最大点数</span></th>
                    <td>
                        <input class="form-control" type="text" name="addstats_max_stats" value="<?=mconfig('addstats_max_stats')?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
                </tr>
            </table>
        </form>
    </div>
</div>