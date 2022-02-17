<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

// 导入对应产品模块的client
use TencentCloud\Sms\V20210111\SmsClient;
// 导入要请求接口对应的Request类
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Credential;
// 导入可选配置类
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;

//aliyun
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class sms
{
    /**
     * 腾讯云短信SMS
     * @param $secretId
     * @param $secretKey
     * @param $SmsSdkAppId
     * @param $Sign
     * @param $TemplateId
     * @param $phone
     * @param $code
     * @return int|mixed
     */
    public static function sendTenCentCloudSms($secretId,$secretKey,$SmsSdkAppId,$Sign,$TemplateId,$phone,$code)
    {
        try {
            /* 必要步骤：
             * 实例化一个认证对象，入参需要传入腾讯云账户密钥对 secretId 和 secretKey
             * 本示例采用从环境变量读取的方式，需要预先在环境变量中设置这两个值
             * 您也可以直接在代码中写入密钥对，但需谨防泄露，不要将代码复制、上传或者分享给他人
             * CAM 密钥查询：https://console.cloud.tencent.com/cam/capi
             */
            $cred = new Credential($secretId, $secretKey);
            //$cred = new Credential(getenv("TENCENTCLOUD_SECRET_ID"), getenv("TENCENTCLOUD_SECRET_KEY"));
            // 实例化一个 http 选项，可选，无特殊需求时可以跳过
            $httpProfile = new HttpProfile();
            // 配置代理
            // $httpProfile->setProxy("https://ip:port");
            $httpProfile->setReqMethod("GET");  // POST 请求（默认为 POST 请求）
            $httpProfile->setReqTimeout(30);    // 请求超时时间，单位为秒（默认60秒）
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");  // 指定接入地域域名（默认就近接入）
            // 实例化一个 client 选项，可选，无特殊需求时可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setSignMethod("TC3-HMAC-SHA256");  // 指定签名算法（默认为 HmacSHA256）
            $clientProfile->setHttpProfile($httpProfile);
            // 实例化 SMS 的 client 对象，clientProfile 是可选的
            $client = new SmsClient($cred, "ap-guangzhou", $clientProfile);
            // 实例化一个 sms 发送短信请求对象，每个接口都会对应一个 request 对象。
            $req = new SendSmsRequest();
            /* 填充请求参数，这里 request 对象的成员变量即对应接口的入参
            * 您可以通过官网接口文档或跳转到 request 对象的定义处查看请求参数的定义
            * 基本类型的设置:
              * 帮助链接：
              * 短信控制台：https://console.cloud.tencent.com/smsv2
              * sms helper：https://cloud.tencent.com/document/product/382/3773 */
            /* 短信应用 ID: 在 [短信控制台] 添加应用后生成的实际 SDKAppID，例如1400006666 */
            $req->SmsSdkAppId = $SmsSdkAppId;
            /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名，可登录 [短信控制台] 查看签名信息 */
            $req->SignName = $Sign;
            /* 短信码号扩展号: 默认未开通，如需开通请联系 [sms helper] */
            $req->ExtendCode = "";
            /* 下发手机号码，采用 e.164 标准，+[国家或地区码][手机号]
            * 例如+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号，最多不要超过200个手机号*/
            $req->PhoneNumberSet = array("+86".$phone);
            /* 国际/港澳台短信 senderid: 国内短信填空，默认未开通，如需开通请联系 [sms helper] */
            $req->SenderId = "";
            /* 用户的 session 内容: 可以携带用户侧 ID 等上下文信息，server 会原样返回 */
            $req->SessionContext = "send";
            /* 模板 ID: 必须填写已审核通过的模板 ID。可登录 [短信控制台] 查看模板 ID */
            $req->TemplateId = $TemplateId;
            /* 模板参数: 若无模板参数，则设置为空*/
            $req->TemplateParamSet = array($code);
            // 通过 client 对象调用 SendSms 方法发起请求。注意请求方法名与请求对象是对应的
            $resp = $client->SendSms($req);
            // 输出 JSON 格式的字符串回包
            $newData = json_decode(json_encode($resp),true);
            // 可以取出单个值，您可以通过官网接口文档或跳转到 response 对象的定义处查看返回字段的定义
            //print_r($resp->TotalCount);
        }catch(TencentCloudSDKException $e) {
            debug($e->getErrorCode());
        }
        if(empty($newData)) return 0;
        if($newData['SendStatusSet'][0]['Code'] == "Ok"){
            return json_encode($code);
        }else{
            return "信息服务商讯息错误：[".$newData['SendStatusSet'][0]['Message']."]。";
        }
    }

    /**
     * 武汉追天短信SMS
     * @param $userID   //企业ID
     * @param $account  //发送的用户账号
     * @param $password //发送的用户密码
     * @param $content  //全部被叫号码 (多个,间隔)
     * @param $phone    //发送的内容
     * @param $code     //验证码
     * @return mixed|SimpleXMLElement
     */
    public static function sendWuHanZhuiTianKeJiSms($userID,$account,$password,$content,$phone,$code)
    {
        $data = [
            'userid'    => $userID,                  #企业ID
            'account'   => $account,                 #发送的用户账号
            'password'  => $password,                #发送的用户密码
            'mobile'    => $phone,                   #全部被叫号码 (多个,间隔)
            'content'   => $content,                 #发送的内容
            'sendTime'  => '',                      #定时发送时间(为空表示立即发送，定时发送格式2010-10-24 09:08:10)
            'action'    => 'send',                  #发送任务命令(设置为固定的:send)
            'extno'     => '',                      #扩展子号(NULL)
        ];

        $getData = Post($data,"http://122.114.79.52:6688/sms.aspx");
        $newData = simplexml_load_string($getData);
        $newData = json_decode(json_encode($newData),true);
        if($newData['returnstatus'] == 'Success'){
            return json_encode($code);
        }else{
            return "信息服务商讯息错误：[".$newData['message']."]。";
        }
    }

    /**
     * 阿里云短信SMS
     * @param $accessKeyId
     * @param $accessSecret
     * @param $SignName
     * @param $TemplateCode
     * @param $phone
     * @param $code
     * @return false|mixed|string
     * @throws ClientException
     */
    public static function sendAliBaBaCloudSms($accessKeyId,$accessSecret,$SignName,$TemplateCode,$phone,$code)
    {
        AlibabaCloud::accessKeyClient($accessKeyId, $accessSecret)
            ->regionId('cn-hangzhou')
            ->asDefaultClient();
        try {
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => "cn-hangzhou",
                        'PhoneNumbers' => $phone,
                        'SignName' => $SignName,
                        'TemplateCode' => $TemplateCode,
                        'TemplateParam'  => "{code:".$code."}",
                    ],
                ])
                ->request();
            $newData = $result->toArray();
            if(isset($newData['Code']) && $newData['Code'] == "OK"){
                return json_encode($code);
            }else{
                return $newData['Message'];
            }

        } catch (ClientException $e) {
            debug( $e->getErrorMessage() . PHP_EOL);
        } catch (ServerException $e) {
            debug( $e->getErrorMessage() . PHP_EOL);
        }
        return null;
    }

    // 消息处理机制
    private static function MessageHandle($str)
    {
        $result = "";
        switch ($str){
            case "":
                break;
            default:
                break;
        }
        return $result;
    }
}