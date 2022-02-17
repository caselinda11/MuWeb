<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'api');

try {
    include('../../includes/Init.php');
    /*防止恶意查询*/
    // if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
    if(!isset($_POST['models'])) throw new Exception("[1]提交信息有错误，请联系在线客服。");
    if($_POST['models'] == 'check'){
        if(!check_value($_POST['servercode'])) throw new Exception("请提交有效的分区代码！");
        if(!check_value($_POST['username'])) throw new Exception("请提交有效用户名。");
        if(!Validator::UnsignedNumber($_POST['servercode'])) throw new Exception("请提交有效的分区代码！");
        if(!Validator::UsernameLength($_POST['username'])) throw new Exception('用户名长度必须为4到10个字符。');
        if(!Validator::AlphaUsername($_POST['username'])) throw new Exception('用户名只能是小写字母或数字。');
        $common = new common();
        if($common->_checkUsernameExists(getGroupIDForServerCode($_POST['servercode']),$_POST['username'])) throw new Exception('账号已存在，请重新输入！');
        exit(json_encode(["code"=> "10000","data"=>"<script type='text/javascript'>$(function() {commonUtil.message('该账号暂无人使用，您可以正常注册。','danger','body');});</script>"]));
    }
    if(!Validator::UnsignedNumber($_POST['group'])) throw new Exception("请提交有效的分区代码！");
    if (!serverGroupIDExists(getGroupIDForServerCode($_POST['group']))) throw new Exception("请提交有效的分区代码！");
    if(!isset($_POST['mobile'])) throw new Exception("[1]提交信息有错误，请联系在线客服。");
    if(!Validator::UnsignedNumber($_POST['mobile'])) throw new Exception("手机格式错误，请重新核对后输入。");
    if(!Validator::is_mobile($_POST['mobile'])) throw new Exception("手机格式错误，请重新核对后输入。");
    if($_POST['models'] == 'register' || $_POST['models'] == 'forgotpassword'|| $_POST['models'] == 'mypassword'){
        $account = new Account();
        $accountPhone = $account->getRegisterCountPhone(getGroupIDForServerCode($_POST['group']),$_POST['mobile']);
        session_regenerate_id();
        $_SESSION['phone_count'] = 10; #限制每个大区手机号注册次数，0为不限制
        if($_POST['models'] == 'register') if($_SESSION['phone_count'] <= $accountPhone) throw new Exception("该手机号已经注册超过上线，不可注册。");

        $code = mt_rand(1000,9999);#随机生成验证码

        $newData = [
            'phone' => $_POST['mobile'],
            'code'  => $code,
        ];
    //sendAliBaBaCloudSms($accessKeyId,$accessSecret,$SignName,$TemplateCode,$phone,$code)
        #模版id
        $tempLateID = ($_POST['models'] == 'register') ? "SMS_186401097" : "SMS_186401097";
       // $data = sms::sendWuHanZhuiTianKeJiSms('userID','account','password','【寻梦奇迹】您正在进行网站操作！您的动态验证码为：'.$newData['code'].'，请在1分钟内完成！如非本人操作，请忽略本短信！',$newData['phone'],$newData['code']);
        //$data = sms::sendTenCentCloudSms("secretID","secretKey","appID","Sign","模版ID",$_POST['models'],$code);
        $data = sms::sendAliBaBaCloudSms("LTAI5tDWp1dzLxdDa7Vbfbxe","bvM7PRuFklbldBDCymK6s6nFkUfoOy","彩虹奇迹特色",$tempLateID,$newData['phone'],$newData['code']);
        if(Validator::UnsignedNumber($data)){
            $_SESSION['phone'] = $newData['phone'];
            $_SESSION['phone_code'] = $newData['code'];
            exit(json_encode($newData));
        }else{
            throw new Exception($data);
        }
    }
}catch (Exception $exception){
    exit(json_encode($exception->getMessage()));
}
