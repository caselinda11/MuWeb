<?php
/**
 * 后台管理员全局函数库
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

/**
 * 加载模块链接方法
 * @param string $module
 * @return string
 */
function admincp_base($module="") {
	if(check_value($module)) return __PATH_ADMINCP_HOME__ . "?module=" . $module;
	return __PATH_ADMINCP_HOME__;
}

/**
 * @param string $module
 * @return string
 */
function admincp_basePlugin($module=""){
    if(check_value($module)) return __PATH_INCLUDES_PLUGINS__ . "?module=" . $module;
    return __PATH_ADMINCP_HOME__;
}

/**
 * 执行缓存任务
 */
function runCronJob(){
    if (DIRECTORY_SEPARATOR == '\\') {
        #windows系统执行文件
        $bat = dirname(__DIR__, 2) . '\定时任务.bat';
        exec($bat);
        return true;
    }else{
        #linux系统执行文件
        return false;
    }
}

/**
 * 单选框
 * @param $name
 * @param $checked
 * @param $e_txt
 * @param $d_txt
 * @return string
 */
function enableDisableCheckboxes($name, $checked, $e_txt, $d_txt) {
    $temp = '';
    $temp.='<div class="form-check-inline my-1">';
    $temp.= '<div class="custom-control custom-radio">';
        if($checked == 1) {
            $temp.= '<input type="radio" name="' . $name . '" id="' . $name . '1" value="1" class="custom-control-input" checked>';
        }else{
            $temp.= '<input type="radio" name="'.$name.'" id="'.$name.'1" value="1" class="custom-control-input">';
        }
    $temp.= '<label class="custom-control-label" for="'.$name.'1">'.$e_txt.'</label>';
    $temp.= '</div>';
    $temp.= '</div>';

    $temp.= '<div class="form-check-inline my-1">';
    $temp.= '<div class="custom-control custom-radio">';
        if($checked == 0) {
            $temp.= '<input type="radio" name="' . $name . '" id="' . $name . '0" value="0" class="custom-control-input" checked>';
        }else{
            $temp.= '<input type="radio" name="' . $name . '" id="' . $name . '0" value="0" class="custom-control-input">';
        }
    $temp.= '<label class="custom-control-label" for="'.$name.'0">'.$d_txt.'</label>';
    $temp.= '</div>';
    $temp.= '</div>';
    return $temp;
}

/**
 * 检查版本
 * @return bool|void
 */
function checkVersion() {
	$url = 'http://version.niudg.com/version/index.php';
	
	$fields = array(
		'version' => urlencode(__X_TEAM_VERSION__),
		'baseurl' => urlencode(__BASE_URL__),
	);
	
	foreach($fields as $key => $value) {
		$fieldsArray[] = $key . '=' . $value;
	}
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, count($fields));
	curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $fieldsArray));
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'XteamFramework');
	curl_setopt($ch, CURLOPT_HEADER, false);

	$result = curl_exec($ch);
	curl_close($ch);
	
	if(!$result) return;
	$resultArray = json_decode($result, true);
	if($resultArray['update']) return true;
	return;
}

/**
 * 获取下载列表
 * @return array|bool|void|null
 * @throws Exception
 */
function getDownloadsList() {
	$db = Connection::Database('Web');
	$result = $db->query_fetch("SELECT * FROM ".X_TEAM_DOWNLOADS." ORDER BY download_type ASC, download_id ASC");
	if(!is_array($result)) return;
	return $result;
}

/**
 * 添加下载
 * @param $title
 * @param string $description
 * @param $link
 * @param int $size
 * @param int $type
 * @return bool|void
 * @throws Exception
 */
function addDownload($title, $description='', $link, $size=0, $type=1) {
	$db = Connection::Database('Web');
	if(!check_value($title)) return;
	if(!check_value($link)) return;
	if(!check_value($size)) return;
	if(!check_value($type)) return;
	if(strlen($title) > 100) return;
	if(strlen($description) > 100) return;

	$result = $db->query("INSERT INTO ".X_TEAM_DOWNLOADS." (download_title, download_description, download_link, download_size, download_type) VALUES (?, ?, ?, ?, ?)", [$title, $description, $link, $size, $type]);
	if(!$result) return;
	
	@updateDownloadsCache();
	return true;
}

/**
 * 编辑下载
 * @param $id
 * @param $title
 * @param string $description |null
 * @param $link
 * @param int $size
 * @param int $type
 * @return bool|void
 * @throws Exception
 */
function editDownload($id, $title, $description='', $link, $size=0, $type=1) {
	$db = Connection::Database('Web');
	if(!check_value($id)) return;
	if(!check_value($title)) return;
	if(!check_value($link)) return;
	if(!check_value($size)) return;
	if(!check_value($type)) return;
	if(strlen($title) > 100) return;
	if(strlen($description) > 100) return;
	
	$result = $db->query("UPDATE ".X_TEAM_DOWNLOADS." SET download_title = ?, download_description = ?, download_link = ?, download_size = ?, download_type = ? WHERE download_id = ?", [$title, $description, $link, $size, $type, $id]);
	if(!$result) return;
	
	@updateDownloadsCache();
	return true;
}

/**
 * 删除下载
 * @param $id
 * @return bool|void
 * @throws Exception
 */
function deleteDownload($id) {
    $db = Connection::Database('Web');
	if(!check_value($id)) return;
	$result = $db->query("DELETE FROM ".X_TEAM_DOWNLOADS." WHERE download_id = ?", array($id));
	if(!$result) return;
	
	@updateDownloadsCache();
	return true;
}

/**
 * 下载链接缓存
 * @return bool
 * @throws Exception
 */
function updateDownloadsCache() {
    $db = Connection::Database('Web');
	$downloadsData = $db->query_fetch("SELECT * FROM ".X_TEAM_DOWNLOADS." ORDER BY download_type ASC, download_id ASC");
	$cacheData = encodeCache($downloadsData);
	updateCacheFile('downloads.cache', $cacheData);
	return true;
}

/**
 * 写文本内容
 * @param $file
 * @param $_content
 */
function setTextContent($file,$_content){

    $writeFile = fopen($file, "w") or die("无法打开文件！");
    if(fwrite($writeFile,$_content)){
        message('success', '文本编辑成功!');
    }else{
        message('error', '写入失败');
    }
    fclose($writeFile);
}

/**
 * 时间转义数组
 * @return array
 */
function commonCronTimes() {
	return [
		60 => '1分钟 (60 秒)',
		300 => '5分钟 (300 秒)',
		600 => '10分钟 (600 秒)',
		900 => '15分钟 (900 秒)',
		1800 => '30分钟 (1,800 秒)',
		3600 => '1小时 (3,600 秒)',
		21600 => '6小时 (21,600 秒)',
		43200 => '12小时 (43,200 秒)',
		86400 => '1天 (86,400 秒)',
		604800 => '7天 (604,800 秒)',
		1296000 => '15天 (1,296,000 秒)',
		2592000 => '1月 (2,592,000 秒)',
		7776000 => '3月 (7,776,000 秒)',
		15552000 => '6月 (15,552,000 秒)',
		31104000 => '1年 (31,104,000 秒)',
	];
}


/**
 * 统计各类角色
 * @return array|void
 * @throws Exception
 */
function charCount(){

    $DW  = 0 ;
    $DK  = 0 ;
    $ELF = 0 ;
    $MG  = 0 ;
    $DL  = 0 ;
    $SUM = 0 ;
    $RF  = 0 ;
    $GL  = 0 ;
    $RW  = 0 ;
    $SLA = 0 ;
    $GUN = 0 ;

#取分区数据
    global $serverGrouping;
    foreach ($serverGrouping AS $code=>$value){
        $Db = ($value['SQL_USE_2_DB']) ? $value['SERVER_DB2_NAME'] : $value['SERVER_DB_NAME'];
        $charCount[$code] = Connection::Database("MuOnline",$code)->query_fetch_single("SELECT	sum(case when Character.Class in  (0,1,3,7) then 1 else 0 end) DW,
		sum(case when Character.Class in  (16,17,19,23) then 1 else 0 end) DK,
		sum(case when Character.Class in  (32,33,35,39) then 1 else 0 end) ELF,
		sum(case when Character.Class in  (48,50,54   ) then 1 else 0 end) MG,
		sum(case when Character.Class in  (64,66,70   ) then 1 else 0 end) DL,
		sum(case when Character.Class in  (80,81,83,87) then 1 else 0 end) SUM,
		sum(case when Character.Class in  (96,98,102  ) then 1 else 0 end) RF,
		sum(case when Character.Class in  (112,114,118) then 1 else 0 end) GL,
		sum(case when Character.Class in  (128,129,131,135 ) then 1 else 0 end) RW,
		sum(case when Character.Class in  (144,145,147,151 ) then 1 else 0 end) SLA,
		sum(case when Character.Class in  (160,161,162,164 ) then 1 else 0 end) GUN
		from [Character] 
		LEFT JOIN [".$Db."].[dbo].[MEMB_INFO] on MEMB_INFO.memb___id = Character.AccountID 
		where MEMB_INFO.servercode = ?",[$value['SERVER_GROUP']]);

    }
    if(!empty($charCount)){
        $DW  = array_sum(array_column($charCount,'DW'));
        $DK  = array_sum(array_column($charCount,'DK'));
        $ELF = array_sum(array_column($charCount,'ELF'));
        $MG  = array_sum(array_column($charCount,'MG'));
        $DL  = array_sum(array_column($charCount,'DL'));
        $SUM = array_sum(array_column($charCount,'SUM'));
        $RF  = array_sum(array_column($charCount,'RF'));
        $GL  = array_sum(array_column($charCount,'GL'));
        $RW  = array_sum(array_column($charCount,'RW'));
        $SLA = array_sum(array_column($charCount,'SLA'));
        $GUN = array_sum(array_column($charCount,'GUN'));
    }

    return $char_count = [
        '魔法师'    => $DW ,
        '剑士'      => $DK ,
        '弓箭手'    => $ELF,
        '魔剑士 '   => $MG ,
        '圣导师'    => $DL ,
        '召唤术师'  => $SUM,
        '角斗士'   => $RF ,
        '梦幻骑士' => $GL ,
        '符文法师' => $RW ,
        '刺客'     => $SLA,
        '铳手'     => $GUN,
    ];
}
