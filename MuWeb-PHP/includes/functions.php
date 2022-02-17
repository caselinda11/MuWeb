<?php
/**
 * 助手函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

 
/**
 * unicode转中文
 * @param $value
 */
function unicodeDecode($unicode_str){
    $json = '{"str":"'.$unicode_str.'"}';
    $arr = json_decode($json,true);
    if(empty($arr)) return '';
    return $arr['str'];
}

 
 
 
/**
 * 打印函数
 * @param $value
 */
function debug($value) {
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

/**
 * 打印系统已定义的所有常量
 * @return mixed $constArr 数组格式所有的预定义常量
 */
function printConst()
{
    $constArr = get_defined_constants(true);
    echo 'array count ['.count($constArr['user']).']';
    echo '<pre>';
    print_r($constArr['user']);
    echo '</pre>';
    return;
}


function getUuid(){
        $chars = md5(uniqid(mt_rand(), true));
        $uuid = substr ( $chars, 0, 8 ) . ''
            . substr ( $chars, 8, 4 ) . ''
            . substr ( $chars, 12, 4 ) . ''
            . substr ( $chars, 16, 4 ) . ''
            . substr ( $chars, 20, 12 );
        return $uuid ;
 }

 
 
  function getPrintKeyNum($c,$key){
	   $c=str_replace($key,'',$c);
	   $str='';
	   for($i_=0;$i_<strlen($c);$i_++)
       {
	      if(is_numeric(substr($c,$i_,1))){
			  $str=$str.substr($c,$i_,1);
		  }
		}
	   return $str;
   }
 
 
   function getPrintKeyValue($str,$key){
	   if(strpos($str,$key)!== false){
		   
		   $begin=strpos($str,$key);
		   $end=strpos($str,'</div>',$begin);
		   $c=substr($str,$begin,$end-$begin);
		   $num=getPrintKeyNum($c,$key);
		   return $num;			
		}
		return '';
	}
/**
 * 校验是否有值
 * @param $value
 * @return bool
 */
function check_value($value) {
	if((@count($value) > 0 && !@empty($value) && @isset($value)) || $value=='0') {
		return true;
	}
    return false;
}

/**
 * 重定向页面函数
 * @param int $type 类型
 * @param null $location  页面
 * @param int $delay 秒
 */
function redirect($type = 1, $location = null, $delay = 0) {

	if(!check_value($location)) {
		$to = __BASE_URL__;
	} else {
		$to = __BASE_URL__ . $location;

		if($location == 'login') {
			$_SESSION['login_last_location'] = $_REQUEST['page'].'/';
			if(check_value($_REQUEST['subpage'])) {
				$_SESSION['login_last_location'] .= $_REQUEST['subpage'].'/';
			}
		}
	}

	switch($type) {
        case 1:
        default:
			header('Location: '.$to.'');
			die();
		break;
        case 2:
			echo '<meta http-equiv="REFRESH" content="'.$delay.';url='.$to.'">';
		break;
		case 3:
			header('Location: '.$location.'');
			die();
		break;
	}
}

/**
 * 打开一个新的窗口JS代码
 * @param $uid
 */
function newTarget($uid){
    echo '<script lang="javascript">';
        echo 'function toTarget'.$uid.' (url){';
            echo 'let win = window.open("about:blank");';
            echo 'if(url){';
                echo 'win.location.href = url';
             echo '}else{';
                echo 'win.document.body.innerHTML = "没有找到目标地址"';
            echo '}';
        echo '}';
    echo '</script>';
}

/**
 * 校验是否登陆
 * @return bool|void
 * @throws Exception
 */
function isLoggedIn() {
	if(!$_SESSION['valid']) return;
	if(!check_value($_SESSION['group'])) return;
	if(!check_value($_SESSION['userid'])) return;
	if(!check_value($_SESSION['username'])) return;
	if(!check_value($_SESSION['timeout'])) return;

	$loginConfigs = loadConfigurations('login');
	if(is_array($loginConfigs)) {
		if($loginConfigs['enable_session_timeout']) {
			if(time()-$_SESSION['timeout'] > $loginConfigs['session_timeout']) {
				logOutUser();
			}
		}
	}
	$_SESSION['timeout'] = time();
	return true;
}

/**
 * 登出账号
 * @throws Exception
 */
function logOutUser() {
	$login = new login();
	$login->logout();
}

/**
 * 发送讯息函数
 * @param string $type
 * @param string $message
 * @param string $title
 */
function message($type='info', $message="", $title="") {
	switch($type) {
		case 'error':
			$class = ' alert-danger';
			$m_type = '<i class="fa fa-times-circle" aria-hidden="true"></i>  ';
		    break;
		case 'success':
			$class = ' alert-success';
			$m_type = '<i class="fa fa-check-circle" aria-hidden="true"></i>  ';
		    break;
		case 'warning':
			$class = ' alert-warning';
			$m_type = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  ';
		    break;
		default:
			$class = ' alert-info';
			$m_type = '<i class="fa fa-question-circle" aria-hidden="true"></i>  ';
		    break;
	}

	if(check_value($title)) {
		echo '<div class="alert'.$class.'" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$m_type.'<strong>'.$title.' </strong>'.$message.'</div>';
	} else {
		echo '<div class="alert'.$class.'" role="alert"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'.$m_type.$message.'</div>';
	}
}

/**
 * 弹窗消息
 * @param null $url
 * @param string $message
 */
function alert($url=null, $message=""){
    $temp = '';
        $temp.= '<script type="text/javascript">';
            $temp.=  '$(function () {';
                $temp.=  'modal_url("'.__BASE_URL__.$url.'","'.$message.'");';
            $temp.=  '});';
        $temp.=  '</script>';
    echo $temp;
}


/**
 * 验证是否具有管理员权限
 * @param $username
 * @return bool|void
 * @throws Exception
 */
function canAccessAdminCP($username) {
	if(!check_value($username)) return;
	if(array_key_exists($username, config('admins'))) return true;
	return false;
}

/**
 * 构建缓存数据
 * @param $data_array
 * @return string|null
 */
function BuildCacheData($data_array) {
	$result = null;
	if(is_array($data_array)) {
		foreach($data_array as $row) {
			$count = count($row);
			$i = 1;
			foreach($row as $data) {
				$result .= $data;
				if($i < $count) {
					$result .= ',';
				}
				$i++;
			}
			$result .= "\n";
		}
		return $result;
	} else {
		return null;
	}
}



/**
 * 加载缓存数据
 * @param $file_name
 * @return array|void
 */
function LoadCacheData($file_name) {
	$file = __PATH_INCLUDES_CACHE__.$file_name;
	if(!file_exists($file)) return;
	if(!is_readable($file)) return;
    $line_data = [];
	$cache_file = file_get_contents($file);
	$file_lanes = explode("\n",$cache_file);
	$nLines = count($file_lanes);
	for($i=0; $i<$nLines; $i++) {
		if(check_value($file_lanes[$i])) {
			$line_data[$i] = explode(",",$file_lanes[$i]);
		}
	}
	return $line_data;
}

/**
 * 时间转换
 * @param int $input_seconds
 * @return array
 */
function sec_to_hms($input_seconds=0) {
	$result = sec_to_dhms($input_seconds);
	if(!is_array($result)) return array(0,0,0);
	return array((($result[0]*24)+$result[1]), $result[2], $result[3]);
}

/**
 * 时间转换
 * @param int $input_seconds
 * @return array
 */
function sec_to_dhms($input_seconds=0) {
	if($input_seconds < 1) return array(0,0,0,0);
	$days_module = $input_seconds % 86400;
	$days = ($input_seconds-$days_module)/86400;
	$hours_module = $days_module % 3600;
	$hours = ($days_module-$hours_module)/3600;
	$minutes_module = $hours_module % 60;
	$minutes = ($hours_module-$minutes_module)/60;
	$seconds = $minutes_module;
	return array($days,$hours,$minutes,$seconds);
}

/**
 * 计算下一次罗兰攻城之前剩下的确切时间。
 *  配置:
 * 	- cs_battle_day: 值: 1(星期一) 至 7(星期日)
 * 	- cs_battle_time: 值: h:m:s (in 24 hour format!)
 * 	- cs_battle_duration: 值: numeric (time in minutes!)
 * @return false|int|void
 */
function cs_CalculateTimeLeft() {
	loadModuleConfigs('castlesiege');
	$weekDays = ["", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
	$battleDay = $weekDays[mconfig('cs_battle_day')];
	$today = date("l");
	$battleTime = mconfig('cs_battle_time');
	$battleDate = strtotime("next $battleDay $battleTime");
	$timeOffset = $battleDate - time();
	if($today == $battleDay) {
		$currentTime = strtotime(date("H:i:s"));
		$battleTimeToday = strtotime((string)$battleTime);
		$timeOffsetToday = $battleTimeToday - time();
		if($battleTimeToday > $currentTime) {
			// CS BATTLE IS TODAY
			return $timeOffsetToday;
		} else {
			$timeOffsetToday = $timeOffsetToday*(-1);
			if((mconfig('cs_battle_duration')*60) > $timeOffsetToday) {
				// CS BATTLE IN PROGRESS
				return;
			} else {
				// CS BATTLE IS ON NEXT DATE
				return $timeOffset;
			}
		}
	} else {
		// CS BATTLE IS ON NEXT DATE
		return $timeOffset;
	}
}

/**
 * 读取缓存列表文件
 * @param string $selected
 * @return string|void
 */

function listCronFiles($selected="") {
    $dir = opendir(__PATH_INCLUDES_CRON__);
    while(($file = readdir($dir)) !== false) {
        if(filetype(__PATH_INCLUDES_CRON__ . $file) == "file" && $file != ".htaccess" && $file != "cron.php") {

            if(check_value($selected) && $selected == $file) {
                $return[] = "<option value=\"$file\" selected=\"selected\">$file</option>";
            } else {
                $return[] = "<option value=\"$file\">$file</option>";
            }
        }
    }
    closedir($dir);
    if(empty($return)) return;
    return join('', $return);
}

/**
 * @param $cron_file
 * @return bool
 * @throws Exception
 */
function cronFileAlreadyExists($cron_file) {
	$check = Connection::Database('Web')->query_fetch_single("SELECT * FROM ".X_TEAM_CRON." WHERE cron_file_run = ?", array($cron_file));
	if(!is_array($check)) {
		return true;
	}
    return false;
}

/**
 * 添加定时任务
 * @throws Exception
 */
function addCron() {
	if(check_value($_POST['cron_name']) && check_value($_POST['cron_file']) && check_value($_POST['cron_time'])) {

		$filePath = __PATH_INCLUDES_CRON__.$_POST['cron_file'];

		// 检查 Cron 文件是否存在
		if(!file_exists($filePath)) {
			message('error','所选文件不存在。');
			return;
		}
		// 检查 Cron 文件数据是否有相同
		if(!cronFileAlreadyExists($_POST['cron_file'])) {
			message('error','具有相同文件的定时任务已存在!');
			return;
		}
		//  检查 Cron 文件时间
		if(!Validator::UnsignedNumber($_POST['cron_time'])) {
			$_POST['cron_time'] = 300;
		}

		$sql_data = array(
			$_POST['cron_name'],
			$_POST['cron_description'],
			$_POST['cron_file'],
			$_POST['cron_time'],
			1,
			0,
			md5_file($filePath)
		);

		$query = Connection::Database('Web')->query("INSERT INTO ".X_TEAM_CRON." (cron_name, cron_description, cron_file_run, cron_run_time, cron_status, cron_protected, cron_file_md5) VALUES (?, ?, ?, ?, ?, ?, ?)", $sql_data);
		if($query) {
			message('success','系统定时任务计划已成功添加!');
		} else {
			message('error','无法添加系统定时任务计划.');
		}

	} else {
		message('error','请填写所有必填字段。');
	}
}

/**
 * 缓存时间更新到数据库
 * @param $file
 * @return bool|void
 * @throws Exception
 */
function updateCronLastRun($file) {
	$update = Connection::Database('Web')->query("UPDATE [".X_TEAM_CRON."] SET [cron_last_run] = ? WHERE [cron_file_run] = ?", [time(), $file]);
	if(!$update) return;
	return true;
}

/**
 * 从ID获取定时计划任务列表
 * @param $id
 * @return mixed|null
 * @throws Exception
 */
function getCronJobDataForId($id) {
	$result = Connection::Database('Web')->query_fetch_single("SELECT * FROM ".X_TEAM_CRON." WHERE cron_id = ?", array($id));
	if(is_array($result)) {
		return $result;
	}
	return null;
}

/**
 * 删除定时计划任务
 * @param $id
 * @throws Exception
 */
function deleteCronJob($id) {
	$cronDATA = getCronJobDataForId($id);
	if(is_array($cronDATA)) {
		if($cronDATA['cron_protected']) {
			message('error','该系统定时任务计划受保护，因此不能删除。');
			return;
		}
		$delete = Connection::Database('Web')->query("DELETE FROM ".X_TEAM_CRON." WHERE cron_id = ?", array($id));
		if($delete) {
			message('success','系统定时任务计划 "<strong>'.$cronDATA['cron_name'].'</strong>" 成功删除！');
		} else {
			message('error','无法删除系统定时任务计划.');
		}
	} else {
		message('error','找不到系统定时任务计划。');
	}
}

/**
 * 更改定时缓存状态
 * @param $id
 * @throws Exception
 */
function toggleStatusCronJob($id) {
	$cronDATA = getCronJobDataForId($id);
	if(is_array($cronDATA)) {
		if($cronDATA['cron_status'] == 1) {
			$status = 0;
		} else {
			$status = 1;
		}
		$toggle = Connection::Database('Web')->query("UPDATE [".X_TEAM_CRON."] SET cron_status = ? WHERE cron_id = ?", array($status, $id));
		if($toggle) {
			message('success','任务 "<strong>'.$cronDATA['cron_name'].'</strong>" 状态已成功更改！');
		} else {
			message('error','无法更新系统定时任务状态!');
		}
	} else {
		message('error','找不到定时任务计划!');
	}
}

/**
 * 编辑缓存文件
 * @param $id
 * @param $name
 * @param $desc
 * @param $file
 * @param $time
 * @param $cron_times
 * @param $current_file
 * @throws Exception
 */
function editCronJob($id,$name,$desc,$file,$time,$cron_times,$current_file) {
	if(check_value($name) && check_value($file) && check_value($time)) {
		$filePath = __PATH_INCLUDES_CRON__.$file;

		// 检查缓存文件是否存在
		if(!file_exists($filePath)) {
			message('error','所选文件不存在。');
			return;
		}
		// 检查缓存文件数据库
		if($file != $current_file) {
			if(!cronFileAlreadyExists($file)) {
				message('error','具有相同文件的系统定时任务计划已存在。');
				return;
			}
		}
		// 检查时间
		if(!array_key_exists($time, $cron_times)) {
			message('error','所选的计划时间不存在。');
			return;
		}
		$query = Connection::Database('Web')->query("UPDATE ".X_TEAM_CRON." SET cron_name = ?, cron_description = ?, cron_file_run = ?, cron_run_time = ? WHERE cron_id = ?", array($name, $desc, $file, $cron_times[$time], $id));
		if($query) {
			message('success','系统定时任务计划已成功更新！');
		} else {
			message('error','无法编辑系统定时任务计划(cron job)。');
		}
	} else {
		message('error','您必须填写所有必填字段。');
	}
}

/**
 * 战盟图标生成API
 * @param string $binaryData
 * @param int $size
 * @param string $class
 * @return string
 */
function getGuildLogo($binaryData="", $size=40, $class="rounded") {
	$imgSize = Validator::UnsignedNumber($size) ? $size : 40;
	return '<img src="'.__PATH_API__.'guildmark.php?data='.$binaryData.'&size='.urlencode($size).'" width="'.$imgSize.'" height="'.$imgSize.'" class="'.$class.'">';
}

/**
 * 家族贡献排名称号
 * @param $contributionPoints
 * @return mixed
 */
function getGensRank($contributionPoints) {
    global $custom;
    foreach($custom['gens_ranks'] as $points => $title) {
        if($contributionPoints >= $points){
            return '<span data-order="'.$points.'">'.$title.'</span>';
        }
    }
    return '<span data-order="99999">'.$custom['gens_ranks'][0].'</span>';
}

/**
 * 家族排名类型称号
 * @param $rankPosition
 * @return int|string|void
 */
function getGensLeadershipRank($rankPosition) {
	global $custom;
	foreach($custom['gens_ranks_leadership'] as $title => $range) {
		if($rankPosition >= $range[0] && $rankPosition <= $range[1]) return $title;
	}
	return;
}

/**
 * 从家族类型ID获取图片
 * @param $GensStatus
 * @param $classType
 * @return string
 */
function getImgForGensTypeId($GensStatus,$classType){
    if($classType > 14 | $classType == 0) $classType = 14;
    #两位数自动左边补0;
    $class = str_pad($classType,2,'0',STR_PAD_LEFT);
    switch ($GensStatus){
        case 1:
            $gensType = '<img class="rankings-gens-img" width="30" data-toggle="tooltip" src="'.__PATH_PUBLIC_IMG__.'Gens/level_gens0'.$GensStatus.'_'.$class.'.png" title="多普瑞恩家族" alt="多普瑞恩家族"/>';
            break;
        case 2:
            $gensType = '<img class="rankings-gens-img" width="30" data-toggle="tooltip" src="'.__PATH_PUBLIC_IMG__.'Gens/level_gens0'.$GensStatus.'_'.$class.'.png" title="巴纳尔特家族" alt="巴纳尔特家族"/>';
            break;
        default:
            $gensType = '<small style="color: red">未加入</small>';
            break;
    }

    return $gensType;
}

/**
 * @param $s
 * @return string|string[]
 */
function xUrlEncode($s) {
    $s = urlencode($s);
    $s = str_replace('_', '_5f', $s);
    $s = str_replace('-', '_2d', $s);
    $s = str_replace('.', '_2e', $s);
    $s = str_replace('+', '_2b', $s);
    $s = str_replace('=', '_3d', $s);
    $s = str_replace('%', '_', $s);
    return $s;
}

/**
 * 加载网站配置文件
 * @return mixed
 * @throws Exception
 */
function webConfigs() {
	if(!file_exists(__PATH_INCLUDES_CONFIGS__.'system.json')) throw new Exception('配置文件不存在，请重新上传网站系统。');
	$systemConfigs = file_get_contents(__PATH_INCLUDES_CONFIGS__.'system.json');
	if(!check_value($systemConfigs)){
        header('Location: '.__BASE_URL__.'install/');
        exit;
    }

	return json_decode($systemConfigs, true);
}

/**
 * 加载网站分区配置文件
 * @return mixed
 * @throws Exception
 */
function GroupConfig() {
    if(!file_exists(__PATH_INCLUDES_CONFIGS__ . 'server.json')) throw new Exception('分区文件不存在，请重新上传网站系统!');

    $server = file_get_contents(__PATH_INCLUDES_CONFIGS__ . 'server.json');
    if(!check_value($server)) throw new Exception('配置文件为空，请运行安装脚本!');

    return json_decode($server, true);
}

/**
 * 取配置文件
 * @param $config_name
 * @param bool $return
 * @return mixed
 * @throws Exception
 */
function config($config_name, $return = false) {
	global $config;
    if(!array_key_exists($config_name,$config)) return '[ERROR]['.$config_name.']';
    return $config[$config_name];
}

/**
 * 转码
 * @param $object
 * @return mixed
 */
function convertXML($object) {
	return json_decode(json_encode($object), true);
}

/**
 * 加载 includes/config/modules/$module.xml配置文件
 * @param $module
 */
function loadModuleConfigs($module) {
	global $mconfig;
	if(moduleConfigExists($module)) {
		$xml = simplexml_load_file(__PATH_INCLUDES_CONFIGS_MODULE__.$module.'.xml');
		$mconfig = [];
		if($xml) {
			$moduleCONFIGS = convertXML($xml->children());
			$mconfig = $moduleCONFIGS;
		}
	}
}

/**
 * 验证[includes/config/module/文件.xml]是否存在
 * @param $module
 * @return bool
 */
function moduleConfigExists($module) {
	if(file_exists(__PATH_INCLUDES_CONFIGS_MODULE__.$module.'.xml')) {
		return true;
	}
    return false;
}

/**
 * 验证[includes/config/文件.xml]是否存在
 * @param $config_file
 * @return bool
 */
function globalConfigExists($config_file) {

	if(file_exists(__PATH_INCLUDES_CONFIGS__.$config_file.'.xml')) {
		return true;
	}
    return false;
}

/**
 * 加载模块配置文件
 * @param $configuration
 * @return string
 */
function mconfig($configuration) {
	global $mconfig;
	if(@array_key_exists($configuration, $mconfig)) {
		return $mconfig[$configuration];
	}
	return null;
}

/**
 * 加载 includes/config/$config_file.xml配置文件
 * @param $config_file
 * @param bool $return
 * @return mixed
 */
function gconfig($config_file,$return=true) {
	global $gconfig;
	if(globalConfigExists($config_file)) {
		$xml = simplexml_load_file(__PATH_INCLUDES_CONFIGS__.$config_file.'.xml');
		$gconfig = [];
		if($xml) {
			$globalCONFIGS = convertXML($xml->children());
			if($return) {
				return $globalCONFIGS;
			} else {
				$gconfig = $globalCONFIGS;
			}
		}
	}
    return '['.$config_file.'][ERROR]';
}

/**
 * 加载 includes\config\modules\($file).xml
 * @param $file
 * @return mixed|void
 */
function loadConfigurations($file) {
	if(!check_value($file)) return;
	if(!moduleConfigExists($file)) return;
	$xml = simplexml_load_file(__PATH_INCLUDES_CONFIGS_MODULE__ . $file . '.xml');
	if($xml) return convertXML($xml->children());
	return;
}

/**
 * 加载网站配置文件
 * @param string $name
 * @return mixed|void
 */
function loadConfig($name="system") {
	if(!check_value($name)) return;
	if(!file_exists(__PATH_INCLUDES_CONFIGS__ . $name . '.json')) return;
	$cfg = file_get_contents(__PATH_INCLUDES_CONFIGS__ . $name . '.json');
	if(!check_value($cfg)) return;
	return json_decode($cfg, true);
}


/**
 * 获取角色职业
 * @param int $class
 * @return string
 */
function getPlayerClassName($class = 0){
    global $custom;
    return array_key_exists($class, $custom['character_class']) ? $custom['character_class'][$class][0] : "未知";
}

/**
 * 获取玩家角色类头像
 * @param int $code class
 * @param bool $htmlImageTag
 * @param bool $tooltip
 * @param null $class
 * @param null $width
 * @return string
 */
function getPlayerClassAvatar($code=0, $htmlImageTag=true, $tooltip=true, $class=null, $width=null) {
	global $custom,$config;
	$imageFileName = array_key_exists($code, $custom['character_class']) ? $custom['character_class'][$code][2] : 'avatar.jpg';
	$imageFullPath = __PATH_PUBLIC_IMG__ . $config['character_avatars_dir'] . '/' . $imageFileName;
	$className = $custom['character_class'][$code][0];
	if(!$htmlImageTag) return $imageFullPath;
	$result = '<img';
	if(check_value($width)) $result .= ' width="'.$width.'"';
	if($tooltip) $result .= ' data-toggle="tooltip" data-placement="top" title="'.$className.'" alt="'.$className.'"';
	$result .= ' class="'.$class.'" src="'.$imageFullPath.'" />';
	return $result;
}

/**
 * 玩家个人资料链接生成
 * @param $group
 * @param $playerName
 * @param string $class
 * @return string
 */
function playerProfile($group,$playerName,$class='') {
    global $config;
	if(!$config['player_profiles']) return $playerName;
	return '<a href="'.__BASE_URL__.'profile/player/group/'.$group.'/name/'.urlencode($playerName).'" class="'.$class.'" target="_blank">'.$playerName.'</a>';
}

/**
 * 战盟资料链接生成
 * @param $group
 * @param $guildName
 * @param string $class css样式
 * @return string
 */
function guildProfile($group,$guildName,$class='') {
    global $config;
	if(!$config['guild_profiles']) return $guildName;
	return '<a href="'.__BASE_URL__.'profile/guild/group/'.$group.'/gname/'.urlencode($guildName).'/" class="'.$class.'" target="_blank">'.$guildName.'</a>';
}

/**
 * 编码函数
 * @param $data
 * @param bool $pretty
 * @return false|string
 */
function encodeCache($data, $pretty=false) {
	if($pretty) return json_encode($data, JSON_PRETTY_PRINT);
	return json_encode($data);
}

/**
 * 解码函数
 * @param $data
 * @return mixed
 */
function decodeCache($data) {
	return json_decode($data, true);
}

/**
 * 更新缓存文件
 * @param $fileName
 * @param $data
 * @return bool|void
 */
function updateCacheFile($fileName, $data) {
	$file = __PATH_INCLUDES_CACHE__ . $fileName;
	$fp = fopen($file, 'w');
	fwrite($fp, $data);
	fclose($fp);
	return true;
}

/**
 * 加载.cache缓存文件
 * @param $fileName
 * @return mixed|void
 */
function loadCache($fileName) {
	$file = __PATH_INCLUDES_CACHE__ . $fileName;
	if(!file_exists($file)) return;
	if(!is_readable($file)) return;

	$cacheDataRaw = file_get_contents($file);
	if(!check_value($cacheDataRaw)) return;

	$cacheData = decodeCache($cacheDataRaw);
	if(!is_array($cacheData)) return;

	return $cacheData;
}

/**
 * 校验访问用户是否被封停IP
 * @return bool|void
 */
function checkBlockedIp() {
	if(in_array(access, ['cron'])) return;
	if(!check_value($_SERVER['REMOTE_ADDR'])) return true;
	if(!Validator::Ip($_SERVER['REMOTE_ADDR'])) return true;
	$blockedIpCache = loadCache('blocked_ip.cache');
	if(!is_array($blockedIpCache)) return;
	if(in_array($_SERVER['REMOTE_ADDR'], $blockedIpCache)) return true;
}

/**
 * 从数据中获取缓存列表
 * @return array|bool|void|null
 * @throws Exception
 */
function getCronList() {
	$result = Connection::Database('Web')->query_fetch("SELECT * FROM [".X_TEAM_CRON."] ORDER BY [cron_id] ASC");
	if(!is_array($result)) return;
	return $result;
}

/**
 * 获取插件排名导航链接
 */
function getRankingMenuLinks() {
	global $rankingMenuLinks;
	if(!is_array($rankingMenuLinks)) return null;
	return $rankingMenuLinks;
}

/**
 * 加载Json文件
 * @param $filePath
 * @return mixed|void
 */
function loadJsonFile($filePath) {
	if(!file_exists($filePath)) return;
	if(!is_readable($filePath)) return;
	$jsonData = file_get_contents($filePath);
	if($jsonData == false) return;
	$result = json_decode($jsonData, true);
	if(!is_array($result)) return;
	return $result;
}

/**
 * 获取IP国家信息
 * @param $ip
 * @return mixed|void
 */
function getCountryCodeFromIp($ip) {
	$api = 'http://ip-api.com/json/'.$ip.'?lang=zh-CN';
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $api);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($handle);
    curl_close($handle);
    if(!check_value($json)) return;
    $result = json_decode($json, true);
	if(!is_array($result)) return;
	if($result['status'] == 'fail') return;
	if(!check_value($result['country'])) return;
	$return = '<span>'.$result['country'].'-'.$result['regionName'].'</span>';
	if(empty($return)) return '未知';
	return $return;
}

/**
 * 获取国旗
 * @param string $countryCode
 * @return string
 */
function getCountryFlag($countryCode='default') {
	if(!check_value($countryCode)) $countryCode = 'default';
	return __PATH_COUNTRY_FLAGS__ . strtolower($countryCode) . '.gif';
}

/**
 * 返回在线图标
 * @param $status
 * @return string
 */
function onlineStatus($status){
    if(!check_value($status)) $status = 0;
    if($status == 1)
        return '<i class="fa fa-dot-circle-o text-success" data-toggle="tooltip" data-placement="right" title="在线"></i>';
    else
        return '<i class="fa fa-circle-o text-danger" data-toggle="tooltip" data-placement="right" title="离线"></i>' ;
}

/**
 * 返回地图名
 * @param int $mapID
 * @return mixed|string|void
 */
function getMapName($mapID=0) {
	global $map;
    if(!is_array($map)) return;
    if(!array_key_exists($mapID,$map)) return '未知';
	return $map[$mapID];
}

/**
 * 返回PK等级名
 * @param $id
 * @return mixed|void
 */
function getPkLevel($id) {
	global $custom;
	if(!is_array($custom['pk_level'])) return;
	if(!array_key_exists($id, $custom['pk_level'])) return;
	return $custom['pk_level'][$id];
}

/**
 * 从路径获取目录列表
 * @param $path
 * @return array|void
 */
function getDirectoryListFromPath($path) {
	if(!file_exists($path)) return;
	$files = scandir($path);
    $result = [];
	foreach($files as $row) {
		if(in_array($row, array('.','..'))) continue;
		if(!is_dir($path.$row)) continue;
		$result[] = $row;
	}
	if(!is_array($result)) return;
	return $result;
}

/**
 * 获取语言文件
 * @return array|void
 */
function getInstalledLanguagesList() {
    $languageDir = getDirectoryListFromPath(__PATH_INCLUDES_LANGUAGES__);
    if(!is_array($languageDir)) return;

    foreach($languageDir as $language) {
        if(!file_exists(__PATH_INCLUDES_LANGUAGES__.$language.'/language.php')) continue;
        $result[] = $language;
    }
    if(!is_array($result)) return;
    return $result;
}

/**
 * 获取文本内容
 * @param $file
 * @return false|string
 */
function getTextContent($file){

    //	读取文件
    $readFile = fopen($file, "r") or die("无法打开配置文件！");
    if(filesize($file)){
        $content = fread($readFile,filesize($file));
        fclose($readFile);
        return $content;
    }

    return null;
}


/**
 * 加载分区配置文件
 * @param $id
 * @return mixed
 * @throws Exception
 */
function localServerGroupConfigs($id) {
    if(!file_exists(__PATH_INCLUDES_CONFIGS__ . 'server.json')) throw new Exception('严重错误，分区配置文件不存在！');

    $Configs = file_get_contents(__PATH_INCLUDES_CONFIGS__ . 'server.json');
    if(!check_value($Configs)) throw new Exception('分区配置文件为空，请运行安装脚本。');

    $item = json_decode($Configs, true);
    return $item[$id];
}

/**
 * 获取分区代号与服务器名
 * @return array
 */
function getServerGroupList(){
    $server=[];
    global $serverGrouping;
    if(!is_array($serverGrouping)) return $server;

    foreach ($serverGrouping as $item){
        $server[$item['SERVER_GROUP']] = $item['SERVER_NAME'];
    }
    return $server;
}

/**
 * 分区号获取分区名称
 * @param $code
 * @return mixed|string|void
 */
function getGroupNameForServerCode($code){
    global $serverGrouping;
    if(!is_array($serverGrouping)) return '分区错误!';
    foreach ($serverGrouping as $item){
        if ($code==$item['SERVER_GROUP']) {
            return $item['SERVER_NAME'];
        }
    }

    return '<span class="badge badge-pill badge-danger">未知</span>';
}

/**
 * 分组获取分区名称
 * @param $group
 * @return mixed|void
 */
function getGroupNameForGroupID($group){
    global $serverGrouping;
    if(!is_array($serverGrouping)) return '分组号错误!';
    if(isset($serverGrouping[$group])){
        return $serverGrouping[$group]['SERVER_NAME'];
    }
    return '<span class="badge badge-pill badge-danger">未知</span>';
}

/**
 * 从分区号获取分区所属分组号
 * @param $code
 * @return int|void
 */
function getGroupIDForServerCode($code){
    global $serverGrouping;
    for($i=0;$i<(count($serverGrouping));$i++){
        #如果值等于分区代码返回分组
        if($code == $serverGrouping[$i]['SERVER_GROUP']){
            return $i;
        }
    }
    return '分区号错误!';
}

/**
 * 从分区Id获得分区号
 * @param $id
 * @return mixed
 * @throws Exception
 */
function getServerCodeForGroupID($id){
    global $serverGrouping;
    if(!check_value($id)) throw new Exception('[FUNCTION] 请提交有效的分区号。');
    if(!Validator::UnsignedNumber($id)) throw new Exception('[FUNCTION] 非法操作，请稍后再试。');
    if(!array_key_exists((int)$id,$serverGrouping)) throw new Exception('[FUNCTION] 无法识别分区号。');
    return $serverGrouping[$id]['SERVER_GROUP'];
}

/**
 * 验证分区是否存在
 * @param $id
 * @return bool
 */
function serverGroupIDExists($id){
    global $serverGrouping;
    if(!check_value($id)) return false;
    if(!Validator::UnsignedNumber($id)) return false;
    return array_key_exists($id,$serverGrouping);
}

/**
 * 根据数组中的某个键值大小进行排序，仅支持二维数组
 * @param array $array 排序数组
 * @param string $key 键值
 * @param bool $asc 默认正序
 * @return array 排序后数组
 */
function arraySortByKey($array=array(), $key='', $asc = true)
{
    $result = array();
    // 整理出准备排序的数组
    foreach ( $array as $k => &$v ) {
        $values[$k] = isset($v[$key]) ? $v[$key] : '';
    }
    unset($v);
    // 对需要排序键值进行排序
    $asc ? asort($values) : arsort($values);
    // 重新排列原有数组
    foreach ( $values as $k => $v ) {
        $result[$k] = $array[$k];
    }

    return $result;
}

/**
 * 选择器
 * @param $requirement
 * @param $key
 * @return string
 */
function selected($requirement,$key = ''){
    return ($requirement == $key) ? 'selected' :'';
}

/**
 * 根据图片生成Base64
 * @param $images | 路径
 * @param bool $img | 输出形式
 * @return string
 */
function generateBase64($images,$img = true){
    if($fp = fopen($images,"rb", 0))
    {
        $gamBar = fread($fp,filesize($images));
        fclose($fp);
        $base64 = chunk_split(base64_encode($gamBar));
        // 输出
        if($img){
            return '<img src="data:image/jpg/png/gif;base64,'.$base64.'" >';
        }else{
            return $base64;
        }
    }
    return '';
}

/**
 * 获取货币类型
 * @param $PriceType
 * @return mixed|string
 * @throws Exception
 */
function getPriceType($PriceType){
    $creditSystem = new CreditSystem();
    $creditConfigList = $creditSystem->showConfigs();
    if(is_array($creditConfigList)) {
        foreach($creditConfigList as $myCredits) {
            if($PriceType == $myCredits['config_id']){
                return $myCredits['config_title'];
            }
        }
    }
    return '未知';
}

/**
 * post链接提交方式
 * @param $curlPost //post内容
 * @param $url  //链接
 * @return bool|string
 */
function Post($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

/**
 * 函数名：get_gb_to_utf8($value)
 * 作 用：gb2312编码字符串转换成utf8编码
 * @param $value
 * @return false|string
 */
function get_gb_to_utf8($value){
    $value_1= $value;

    $value_2 = @iconv( "gb2312", "utf-8//IGNORE",$value_1);
    $value_3 = @iconv( "utf-8", "gb2312//IGNORE",$value_2);

    if (strlen($value_1) == strlen($value_3)) {
        return $value_2;
    }else{
        return $value_1;
    }
}

/**
 * @param $title
 * @param $message
 * @param string $ip
 * @param int $port
 * @return string|void
 */
function sendMessageGames($title,$message,$ip = '127.0.0.1',$port = 55970)
{
    global $config;
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if (!$socket) return 0;
    $result = socket_connect($socket, $ip, $port);  #Korean OK
//    $result = socket_connect($socket, $ip, 55960);  #emu OK
    if (!$result) return 0;
    $sendMessage = '['.$title.'] '.$message;
    $encode = mb_detect_encoding($sendMessage, ["ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5']);
    $sendMessage = mb_convert_encoding($sendMessage, 'GBK', $encode);
    $serverFiles = strtolower($config['server_files']);
    if($serverFiles == 'muemu'){
        $length = strlen($sendMessage) + 8 + 1 +9; #emu OK
        $init = [0xC1, $length, 0x21, 65535, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]; #emu OK
    }else{
        $length = strlen($sendMessage) + 8 + 1; #Korean OK
        $init = [0xC1, $length, 0xA1, 0, 0x24, 0, 0, 0];  #Korean OK
    }
    // 0xC1,长度,head,
    $str = array_map('ord', str_split($sendMessage));
    $array = array_merge($init, $str);
    array_push($array, 0); #Korean OK
    $new = '';
    foreach ($array as $key => $item) $new.=pack("c", $item);
    $result = socket_write($socket, $new);
    if (!$result) return 0;
    socket_close($socket);
	return 1;
}

/**
 * server sql数据库时间格式
 * @param string $strtotime
 * @return false|string
 */
function logDate($strtotime = ''){
    //To: $strtotime = +7 Days 加七天
    if($strtotime) return date('Y-m-d H:i:s',strtotime($strtotime));
    return date('Y-m-d H:i:s');
}

/**
 * @param int $value
 * @param array $array
 * @param string $key
 * @param bool $class
 * @return array
 */
function getArrayKeyForValue($value = 0, $array = [], $key = '', $class = false){
    $newArray = [];
    if($class){
        global $custom;
        foreach ($custom['character_class'] as $id => $item){
                if($value >= 0   && $value <16  && $id >= 0   && $id <16
                || $value >= 16  && $value <32  && $id >= 16  && $id <32
                || $value >= 32  && $value <48  && $id >= 32  && $id <48
                || $value >= 48  && $value <64  && $id >= 48  && $id <64
                || $value >= 64  && $value <80  && $id >= 64  && $id <80
                || $value >= 80  && $value <96  && $id >= 80  && $id <90
                || $value >= 96  && $value <112 && $id >= 96  && $id <112
                || $value >= 112 && $value <128 && $id >= 112 && $id <128
                || $value >= 144 && $value <160 && $id >= 144 && $id <160
                || $value >= 160 && $value <176 && $id >= 160 && $id <176){
                foreach ($array as $data) {
                    if ($data[$key] == $id) {
                        $newArray[] = $data;
                    }
                }
            }
        }
    }else{
        foreach ($array as $data){
            if ($data[$key] == $value){
                $newArray[] = $data;
            }
        }

    }
    $array = $newArray;
    return $array;
}

/**
 * @return array|mixed
 */
function newsType(){
    return [
        0   =>  '最新新闻',
        1   =>  '新手指南',
        2   =>  '游戏公告',
        3   =>  '维护通知',
        4   =>  '版本升级',
        5   =>  '最新活动'
    ];
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv  是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function IP($type = 0, $adv = true){

    $type = $type ? 1 : 0;
    static $ip = null;
    if (null !== $ip) {
        return $ip[$type];
    }
    //代理
    $httpAgentIp = '';

    if ($httpAgentIp && isset($_SERVER[$httpAgentIp])) {
        $ip = $_SERVER[$httpAgentIp];
    } elseif ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? [$ip, $long] : ['0.0.0.0', 0];
    return $ip[$type];
}

/**
 *
 * @param $string   //需要加密解密的字符串
 * @param $operation    //判断是加密还是解密:E:加密   D:解密
 * @param string $key   加密的钥匙(密匙);
 * @return false|string|string[]
 */
function key_encrypt($string,$operation,$key='')
{
    $key=md5($key);
    $key_length=strlen($key);
    $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
    $string_length=strlen($string);
    $rndkey=$box=array();
    $result='';
    for($i=0;$i<=255;$i++)
    {
        $rndkey[$i]=ord($key[$i%$key_length]);
        $box[$i]=$i;
    }
    for($j=$i=0;$i<256;$i++)
    {
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0;$i<$string_length;$i++)
    {
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if($operation=='D')
    {
        if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
        {
            return substr($result,8);
        }
        else
        {
            return'';
        }
    }
    else
    {
        return str_replace('=','',base64_encode($result));
    }
}

/**
 * 角色类选择框
 * @return array
 */
function classType(){
    global $custom;
    $data = [];
    if(array_key_exists(0,$custom['character_class']))   $data[0]   = '魔法师';
    if(array_key_exists(16,$custom['character_class']))  $data[16]  =  '剑士';
    if(array_key_exists(32,$custom['character_class']))  $data[32]  = '弓箭手';
    if(array_key_exists(48,$custom['character_class']))  $data[48]  = '魔剑士';
    if(array_key_exists(64,$custom['character_class']))  $data[64]  = '圣导师';
    if(array_key_exists(80,$custom['character_class']))  $data[80]  = '召唤术士';
    if(array_key_exists(96,$custom['character_class']))  $data[96]  = '角斗士';
    if(array_key_exists(112,$custom['character_class'])) $data[112] = '梦幻骑士';
    if(array_key_exists(128,$custom['character_class'])) $data[128] = '符文法师';
    if(array_key_exists(144,$custom['character_class'])) $data[144] = '刺客';
    if(array_key_exists(160,$custom['character_class'])) $data[160] = '铳手';
    return $data;
}
