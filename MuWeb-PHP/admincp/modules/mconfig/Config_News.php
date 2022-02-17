<?php

/**
 * 新闻模块配置文件
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

	$xmlPath = __PATH_INCLUDES_CONFIGS_MODULE__.'news.xml';
	$xml = simplexml_load_file($xmlPath);
	$xml->active = $_POST['active'];
    $xml->news_list_limit = $_POST['news_list_limit'];
	$xml->news_enable_comment_system = $_POST['news_enable_like_button'];
	$xml->news_enable_like_button = $_POST['news_enable_like_button'];

	$save = $xml->asXML($xmlPath);

	if($save) {
		message('success','已成功保存.');
	}else{
		message('error','保存时发生错误.');
	}
}

if(check_value($_POST['submit_changes'])) {
	saveChanges();
}
loadModuleConfigs('news');
?>
<div class="card">
    <div class="card-header">新闻设置</div>
    <div class="card-body">
        <form action="" method="post">
            <table class="table table-striped table-bordered table-hover module_config_tables">
                <tr>
                    <th style="width: 60%"><strong>模块开关</strong><span class="ml-3 text-muted">启用/禁用 新闻模块</span></th>
                    <td>
                        <?=enableDisableCheckboxes('active',mconfig('active'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>新闻条数</strong><span class="ml-3 text-muted">仅开启卡片模式才生效，仅显示最新新闻条数。</span></th>
                    <td>
                        <input class="input-mini" type="text" name="news_list_limit" value="<?=mconfig('news_list_limit')?>"/>
                    </td>
                </tr>
                <tr>
                    <th><strong>新闻评论</strong><span class="ml-3 text-muted">如果开启，用户将可以对新闻进行评论。[扩展]</span></th>
                    <td>
                        <?=enableDisableCheckboxes('news_enable_comment_system',mconfig('news_enable_comment_system'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <th><strong>新闻点赞</strong><span class="ml-3 text-muted">如果开启，用户将可以对新闻进行点赞。[扩展]</span></th>
                    <td>
                        <?=enableDisableCheckboxes('news_enable_like_button',mconfig('news_enable_like_button'),'启用','禁用'); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2"><div style="text-align:center"><button type="submit" name="submit_changes" value="submit" class="btn btn-success waves-effect waves-light col-md-2">保存</button></div></td>
                </tr>
            </table>
        </form>
    </div>
</div>