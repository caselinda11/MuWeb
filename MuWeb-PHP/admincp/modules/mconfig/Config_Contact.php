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
	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'contact.xml';
	$xml = simplexml_load_file($xmlPath);
	
	$xml->active = $_POST['setting_1'];
	$xml->subject = $_POST['setting_2'];
	$xml->sendto = $_POST['setting_3'];
	
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

loadModuleConfigs('contact');
?>
<div class="card">
    <div class="card-header">联系设置</div>
    <div class="card-body">
        <form action="" method="post">
            <table class="table table-striped table-bordered table-hover module_config_tables">
                <tr>
                    <th style="width: 60%"><strong>模块状态</strong><span class="ml-3 text-muted">启用/禁用 联系我们模块</span></th>
                    <td>
                        <?=enableDisableCheckboxes('setting_1',mconfig('active'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>邮箱主题</strong><span class="ml-3 text-muted"></span></th>
                    <td>
                        <input type="text" name="setting_2" value="<?=mconfig('subject')?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>发送邮箱至</strong><span class="ml-3 text-muted"></span></th>
                    <td>
                        <input type="text" name="setting_3" value="<?=mconfig('sendto')?>"/>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="text-align:center">
                            <button type="submit" name="submit_changes" value="submit" class="btn btn-success col-md-2">保存</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>



