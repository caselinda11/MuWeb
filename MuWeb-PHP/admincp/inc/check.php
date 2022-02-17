<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

$configError = array();

$writablePaths = loadJsonFile(X_TEAM_WRITABLE_PATHS);
if(!is_array($writablePaths)) throw new Exception('无法加载奇迹网站系统路径可写路径列表。');

// File permission check
foreach($writablePaths as $thisPath) {
	if(file_exists(__PATH_INCLUDES__ . $thisPath)) {
		if(!is_writable(__PATH_INCLUDES__ . $thisPath)) {
			$configError[] = "<span style=\"color:#aaaaaa;\">[权限错误]</span> " . $thisPath . " <span style=\"color:red;\">（文件必须可写）</span>";
		}
	} else {
		$configError[] = "<span style=\"color:#aaaaaa;\">[未找到]</span> " . $thisPath. " <span style=\"color:orange;\">（重新上传文件）</span>";
	}
}

// Check cURL
if(!function_exists('curl_version')) $configError[] = "<span style=\"color:#aaaaaa;\">[PHP]</span> <span style=\"color:green;\">未加载curl(网站系统需要cURL)</span>";

if(count($configError) >= 1) {
	throw new Exception("<strong>发生以下错误：</strong><br /><br />" . implode("<br />", $configError));
}