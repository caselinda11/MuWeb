<?php

/**
 * 极验行为式验证库文件
 *
 * @author Tanxu
 */
class GeeTestLib
{
    const GT_SDK_VERSION = 'php_3.0.0';

    public static $connectTimeout = 1;
    public static $socketTimeout  = 1;

    private $response;
    private $captcha_id,$private_key,$domain;

    public function __construct($captcha_id, $private_key) {
        $this->captcha_id  = $captcha_id;
        $this->private_key = $private_key;
        $this->domain = "http://api.geetest.com";
    }

    /**
     * 判断极验服务器是否down机
     * @param $param
     * @param int $new_captcha
     * @return int
     */
    public function pre_process($param, $new_captcha=1) {
        $data = [
            'gt'=>$this->captcha_id,
            'new_captcha'=>$new_captcha
        ];
        $data = array_merge($data,$param);
        $query = http_build_query($data);
        $url = $this->domain . "/register.php?" . $query;
        $challenge = $this->send_request($url);
        if (strlen($challenge) != 32) {
            $this->failback_process();
            return 0;
        }
        $this->success_process($challenge);
        return 1;
    }

    /**
     * 成功回调
     * @param $challenge
     */
    private function success_process($challenge) {
        $challenge      = md5($challenge . $this->private_key);
        $result         = [
            'success'       => 1,
            'gt'            => $this->captcha_id,
            'challenge'     => $challenge,
            'new_captcha'   => 1
        ];
        $this->response = $result;
    }

    /**
     * 失败回调
     */
    private function failback_process() {
        $rnd1           = md5(rand(0, 100));
        $rnd2           = md5(rand(0, 100));
        $challenge      = $rnd1 . substr($rnd2, 0, 2);
        $result         = [
            'success'       =>  0,
            'gt'            =>  $this->captcha_id,
            'challenge'     =>  $challenge,
            'new_captcha'   =>  1
        ];
        $this->response = $result;
    }

    /**
     * @return mixed
     */
    public function get_response_str() {
        return json_encode($this->response);
    }

    /**
     * 返回数组方便扩展
     * @return mixed
     */
    public function get_response() {
        return $this->response;
    }

    /**
     * 正常模式获取验证结果
     * @param string $challenge
     * @param string $validate
     * @param string $seccode
     * @param array $param
     * @return int
     */
    public function success_validate($challenge, $validate, $seccode, $param, $json_format=1) {
        if (!$this->check_validate($challenge, $validate)) {
            return 0;
        }
        $query = [
            "seccode"       =>  $seccode,
            "timestamp"     =>  time(),
            "challenge"     =>  $challenge,
            "captchaid"     =>  $this->captcha_id,
            "json_format"   =>  $json_format,
            "sdk"           =>  self::GT_SDK_VERSION
        ];
        $query = array_merge($query,$param);
        $url          = $this->domain . "/validate.php";
        $codevalidate = $this->post_request($url, $query);
        $obj = json_decode($codevalidate,true);
        if ($obj === false){
            return 0;
        }
        if ($obj['seccode'] == md5($seccode)) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 宕机模式获取验证结果
     *
     * @param $challenge
     * @param $validate
     * @param $seccode
     * @return int
     */
    public function fail_validate($challenge, $validate, $seccode) {
        if(md5($challenge) == $validate){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * @param $challenge
     * @param $validate
     * @return bool
     */
    private function check_validate($challenge, $validate) {
        if (strlen($validate) != 32) {
            return false;
        }
        if (md5($this->private_key . 'geetest' . $challenge) != $validate) {
            return false;
        }

        return true;
    }

    /**
     * GET 请求
     *
     * @param $url
     * @return mixed|string
     */
    private function send_request($url) {

        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$socketTimeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            $curl_errno = curl_errno($ch);
            curl_close($ch);
            if ($curl_errno >0) {
                return 0;
            }else{
                return $data;
            }
        } else {
            $opts    = [
                'http' => [
                    'method'  => "GET",
                    'timeout' => self::$connectTimeout + self::$socketTimeout,
                ]
            ];
            $context = stream_context_create($opts);
            $data    = @file_get_contents($url, false, $context);
            if($data){
                return $data;
            }else{
                return 0;
            }
        }
    }

    /**
     * @param $url
     * @param string $postData
     * @return bool|false|string
     */
    private function post_request($url, $postData = '') {
        if (!$postData) {
            return false;
        }

        $data = http_build_query($postData);
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, self::$socketTimeout);

            //不可能执行到的代码
            if (!$postData) {
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            } else {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            }
            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                $err = sprintf("curl[%s] error[%s]", $url, curl_errno($ch) . ':' . curl_error($ch));
                $this->triggerError($err);
            }

            curl_close($ch);
        } else {
            if ($postData) {
                $opts    = [
                    'http' => [
                        'method'  => 'POST',
                        'header'  => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . strlen($data) . "\r\n",
                        'content' => $data,
                        'timeout' => self::$connectTimeout + self::$socketTimeout
                    ]
                ];
                $context = stream_context_create($opts);
                $data    = file_get_contents($url, false, $context);
            }
        }

        return $data;
    }



    /**
     * @param $err
     */
    private function triggerError($err) {
        trigger_error($err);
    }

    /**
     * 首次验证 (前端验证)
     * @param $ip
     * @param $userId
     * @return mixed
     */
    public function StartCaptchaServlet($ip,$userId){
        session_status() != PHP_SESSION_ACTIVE && session_start();
        $data = [
            "user_id"       => $userId, # 网站用户id
            "client_type"   => Validator::isMobile() ? 'h5' : 'web', #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address"    => "$ip"   # 请在此处传输用户请求验证时所携带的IP
        ];

        $status = $this->pre_process($data, 1);
        $_SESSION['gtserver'] = $status;
        $_SESSION['user_id'] = $data['user_id'];
        return $this->get_response_str();
    }

    /**
     * 二次验证（后端验证）
     * @param $ip
     * @param $userId
     * @return bool
     */
    public function VerifyLoginServlet($ip,$userId){
        session_status() != PHP_SESSION_ACTIVE && session_start();

        $data = [
            "user_id" => $userId, # 网站用户id
            "client_type" => Validator::isMobile() ? 'h5' : 'web', #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
            "ip_address" => "$ip" # 请在此处传输用户请求验证时所携带的IP
        ];

        if ($_SESSION['gtserver'] == 1) {   //服务器正常
            $result = $this->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
            if ($result) {
                return true;
            } else{
                return false;
            }
        }else{  //服务器宕机,走failBack模式
            if ($this->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
                return true;
            }else{
                return false;
            }
        }
    }
}