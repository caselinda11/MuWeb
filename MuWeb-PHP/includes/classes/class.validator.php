<?php
/**
 * 验证器类函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Validator{

    /**
     * 验证$exclude中是否存在$string
     * @param $string
     * @param string $exclude
     * @return bool
     */
	private static function textHit($string, $exclude=""){
		if(empty($exclude)) return false;
		if(is_array($exclude)){
			foreach($exclude as $text){
				if(strstr($string, $text)) return true;
			}
		}else{
			if(strstr($string, $exclude)) return true;
		}
		return false;
	}

    /**
     * 验证max~min之间的数值
     * @param $integer
     * @param null $max
     * @param int $min
     * @return bool
     */
	private static function numberBetween($integer, $max=null, $min=0){
		if(is_numeric($min) && $integer < $min) return false;
		if(is_numeric($max) && $integer > $max) return false;
		return true;
	}

    /**验证是否是Email类型
     * @param $string
     * @param string $exclude
     * @return bool
     */
	public static function Email($string, $exclude=""){
		if(self::textHit($string, $exclude)) return false;
		return (bool)preg_match("/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i", $string);
	}

    public static function QQEmail($string, $exclude=""){
        if(self::textHit($string, $exclude)) return false;
        return (bool)preg_match("/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([qQ][qQ])+(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i", $string);
    }
    /**
     * 验证是否为url链接类型
     * @param $string
     * @param string $exclude
     * @return bool
     */
	public static function Url($string, $exclude=""){
		if(self::textHit($string, $exclude)) return false;
		return (bool)preg_match("/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i", $string);
	}

    /**
     * 验证是否为IP
     * @param $string
     * @return bool
     */
	public static function Ip($string){
		if(!filter_var($string, FILTER_VALIDATE_IP)) return false;
		return true;
	}

    /**
     * 纯数字
     * @param $integer
     * @param null $max
     * @param int $min
     * @return bool
     */
	public static function Number($integer, $max=null, $min=0){
		if(preg_match("/^\-?\+?[0-9e1-9]+$/",$integer)){
			if(!self::numberBetween($integer, $max, $min)) return false;
			return true;
		}
		return false;
	}

    /**
     * 验证是否位无符号号码
     * @param $integer
     * @return bool
     */
	public static function UnsignedNumber($integer){
		return (bool)preg_match("/^\+?[0-9]+$/",$integer);
	}

    /**
     *
     * @param $string
     * @return bool
     */
	public static function Float($string){
		return (bool)($string==strval(floatval($string)))? true : false;
	}

    /**
     * 验证是否为字母
     * @param $string
     * @return bool
     */
	public static function Alpha($string){
		return (bool)preg_match("/^[a-zA-Z]+$/", $string);	
	}

    /**
     * 验证是否为物品/多个物品
     * @param $code
     * @return bool
     */
    public static function Items($code){
        if(preg_match("/[a-fA-F0-9,&]+/",$code)){
            if(substr($code,0,1) == "&" || substr($code,0,1) == ",") $code = substr($code,1);
            if (strstr($code,"&")){
                $itemArray = explode("&",$code);
            }else{
                $itemArray = explode(",",$code);
            }
            if(is_array($itemArray)){
                foreach ($itemArray as $id=>$data){
                    if(strlen($data) == ITEM_SIZE){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
            return false;
        }
        return false;
    }

    /**
     * 验证是否为字母数字
     * @param $string
     * @return bool
     */
	public static function AlphaNumeric($string){
		return (bool)preg_match("/^[0-9a-zA-Z]+$/", $string);
	}

    /**
     * 验证账号是否为数字+小写字母
     * @param $string
     * @return bool
     */
    public static function AlphaUsername($string){
        return (bool)preg_match("/^[0-9a-z]+$/", $string);
    }

    /**
     * 验证角色名
     * @param $string
     * @return bool
     */
	public static function ChineseCharacter($string){
        if(preg_match("/^[\x80-\xffA-Za-z0-9?~!@#$%^&()_+={}\[\]]+$/", $string)){
            $length = strlen($string);
            if(!self::numberBetween($length, 15, 4)) return false;
            return true;
        }
        return false;
    }


    /**
     * 验证是否为字符
     * @param $string
     * @param array $allowed
     * @return bool
     */
	public static function Chars($string, $allowed=array("a-z")){
		return (bool)preg_match("/^[" . implode("", $allowed) . "]+$/", $string);	
	}

    /**
     * 验证长度
     * @param $string
     * @param null $max
     * @param int $min
     * @return bool
     */
	public static function Length($string, $max=null, $min=0){
		$length = strlen($string);
		if(!self::numberBetween($length, $max, $min)) return false;
		return true;
	}

    /**
     * 验证是否位有效的日期
     * @param $string
     * @return bool
     */
    public static function Date($string){
        $date = date('Y', strtotime($string));
        return ($date == "1970" || $date == '') ? false : true;
    }

    /**
     * 验证账号长度
     * @param $string
     * @return bool
     * @throws Exception
     */
	public static function UsernameLength($string){
		if((strlen($string) < config('username_min_len')) || (strlen($string) > config('username_max_len'))) {
			return false;
		} else {
			return true;
		}
	}


    /**
     * 验证密码长度
     * @param $string
     * @return bool
     * @throws Exception
     */
	public static function PasswordLength($string){
		if((strlen($string) < config('password_min_len')) || (strlen($string) > config('password_max_len'))) {
			return false;
		} else {
			return true;
		}
	}

    /**
     * 验证输入的手机号码
     * @access  public
     * @param   string      $user_mobile      需要验证的手机号码
     * @return bool
     */
    public static function is_mobile($user_mobile){
        $chars = "/^((\(\d{2,3}\))|(\d{3}\-))?1(3|5|6|7|8|9)\d{9}$/";
        if (preg_match($chars, $user_mobile)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 校验身份证号是否合法
     * @param string $num 待校验的身份证号
     * @return bool
     */
    public static function isValid($num)
    {
        //老身份证长度15位，新身份证长度18位
        $length = strlen($num);
        if ($length == 15) { //如果是15位身份证

            //15位身份证没有字母
            if (!is_numeric($num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月（6位）
            $dateNum = substr($num, 6, 6);

        } else if ($length == 18) { //如果是18位身份证

            //基本格式校验
            if (!preg_match('/^\d{17}[0-9xX]$/', $num)) {
                return false;
            }
            // 省市县（6位）
            $areaNum = substr($num, 0, 6);
            // 出生年月日（8位）
            $dateNum = substr($num, 6, 8);

        } else { //假身份证
            return false;
        }

        //验证地区
        if (!self::isAreaCodeValid($areaNum)) {
            return false;
        }

        //验证日期
        if (!self::isDateValid($dateNum)) {
            return false;
        }

        //验证最后一位
        if (!self::isVerifyCodeValid($num)) {
            return false;
        }

        return true;
    }

    /**
     * 省市自治区校验
     * @param string $area 省、直辖市代码
     * @return bool
     */
    private static function isAreaCodeValid($area) {
        $provinceCode = substr($area, 0, 2);

        // 根据GB/T2260—999，省市代码11到65
        if (11 <= $provinceCode && $provinceCode <= 65) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 验证出生日期合法性
     * @param string $date 日期
     * @return bool
     */
    private static function isDateValid($date) {
        if (strlen($date) == 6) { //15位身份证号没有年份，这里拼上年份
            $date = '19'.$date;
        }
        $year  = intval(substr($date, 0, 4));
        $month = intval(substr($date, 4, 2));
        $day   = intval(substr($date, 6, 2));

        //日期基本格式校验
        if (!checkdate($month, $day, $year)) {
            return false;
        }

        //日期格式正确，但是逻辑存在问题(如:年份大于当前年)
        $currYear = date('Y');
        if ($year > $currYear) {
            return false;
        }
        return true;
    }

    /**
     * 验证18位身份证最后一位
     * @param string $num 待校验的身份证号
     * @return bool
     */
    private static function isVerifyCodeValid($num)
    {
        if (strlen($num) == 18) {
            $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
            $tokens = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];

            $checkSum = 0;
            for ($i = 0; $i < 17; $i++) {
                $checkSum += intval($num{$i}) * $factor[$i];
            }

            $mod   = $checkSum % 11;
            $token = $tokens[$mod];

            $lastChar = strtoupper($num{17});

            if ($lastChar != $token) {
                return false;
            }
        }
        return true;
    }

    /**
     * 是否成年
     * @param $IDCard
     * @return int 0 成年，1未成年
     */
    public static function is_adult18($IDCard){
        if(strlen($IDCard)==18){
            $dateNum = substr($IDCard, 6, 4);
        }elseif(strlen($IDCard)==15){
            $dateNum = '19'.substr($IDCard, 6, 2);
        }else{
            return false;
        }
        $newY = date('Y',time());
        if($newY - $dateNum < 18 ) {
            return false;
        }

        return true;
    }

    /**
     * [验证器]获取IP
     * @return array|false|mixed|string
     */
    public static function getIP()
    {
        $ip = '';
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) $ip = getenv('HTTP_CLIENT_IP');
        elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) $ip = getenv('HTTP_X_FORWARDED_FOR');
        elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) $ip = getenv('REMOTE_ADDR');
        elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) $ip = $_SERVER['REMOTE_ADDR'];
        return $ip;
    }

    /**
     * [验证器]筛选IP
     * @param $source
     * @param $arr
     * @param $filterStr
     */
    public static function filter($source, $arr, $filterStr)
    {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $v = implode($v);
            }
            if (preg_match("/" . $filterStr . "/is", $v) == 1) {
                global $ip;
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/f.log", ("Ip:" . $ip . "\tSRC:" . $source . "\tTime:" . date("y-m-d H:i:s") . "\tPage:" . $_SERVER["PHP_SELF"] . "\tType:" . $_SERVER["REQUEST_METHOD"] . "\tParm:" . $k . "\tData:" . $v . "\r\n"), FILE_APPEND);
                $ip = "";
                break;
            }
        }
    }

    /**
     * 检测是否使用手机访问
     * @access public
     * @return bool
     */
    public static function isMobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }


}