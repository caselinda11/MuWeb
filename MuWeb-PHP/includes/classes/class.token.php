<?php
/**
 * 令牌处理程序。
 *  令牌是存储在会话Cookie中并通过表单发送的唯一哈希字符串，
 *  用于防止跨站请求伪造。
 */

class Token
{
    /**
     * 生成会话令牌字符串
     * @param $uid
     * @return string
     */
    public static function generateToken($uid='uid')
    {
        if (isset($_SESSION[$uid.'__token']) && isset($_SESSION['tokentime'])){
            if ((time() - $_SESSION['tokentime']) < 5) {
                return $_SESSION[$uid . '__token'];
            }
        }
        $_SESSION['tokentime'] = time();
        $_SESSION[$uid.'__token'] = base64_encode(openssl_random_pseudo_bytes(32));
        return $_SESSION[$uid.'__token'];
    }

    /**
     * 将令牌字符串与会话令牌进行比较
     * @param string $uid
     * @return boolean
     */
    public static function checkToken($uid='uid')
    {
        if (isset($_SESSION[$uid.'__token']) && (string)$_REQUEST['key'] === (string)$_SESSION[$uid.'__token']) {
            #清空所有令牌
            if(is_array($_SESSION)){
                foreach ($_SESSION as $key=>$value){
                    if(strpos($key,'__token'))
                        unset($_SESSION[$key]);
                }
            }
            unset($_SESSION[$uid.'__token']);
            return true;
        }
        return false;
    }


}