<?php
/**
 * 交易市场[支付宝]
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use Alipay\EasySDK\Kernel\Config;


class trading
{
    private $timeOut = "2m";    //每笔订单有效时间(单位d/h/m/s),取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。

    public $outTradeNo;

    public $appID = "2021003108642068"; //您的appID

    private $Web;

    private $errorLog = __ROOT_DIR__.'alipay/log/alipay.log';

    private $url = 'http://cbg.mucck.com/market/bindalipay';//回调地址
	
	private $retunUrl='http://cbg.mucck.com/api/ext/result.php';//商城购买回调
	
	private $retunUrl1='http://cbg.mucck.com/api/ext/payResult.php';//1.00元验证支付宝回调
	
	
    /**
     * 初始化构造函数
     * trading constructor.
     * @throws Exception
     */
    public function __construct()
    {
        //1. 设置参数（全局只需设置一次）
        Factory::setOptions($this->init());
        $this->Web = Connection::Database("Web");
    }

    public function token($code)
    {
        try {
            $result = Factory::Base()->OAuth()->getToken($code);
            if($result->code){
                throw new Exception("调用失败，原因：". $result->msg."，".$result->subMsg);
            }
            if($result->userId){
                return $result->userId;
            }
        } catch (Exception $e) {
            message('error',$e->getMessage());
        }
        return null;
    }


    /**
     * 绑定支付宝
     * @param $aliPayID
     * @return bool
     * @throws Exception
     */
    public function bindAliPay($aliPayID)
    {
        if(!check_value($_SESSION['group'])) throw new Exception("[1]出错了，请您重新输入！");
        if(!check_value($_SESSION['username'])) throw new Exception("[2]出错了，请您重新输入！");
        if(!check_value($aliPayID)) throw new Exception("[3]出错了，请您重新输入！");
        if(!Validator::UnsignedNumber($aliPayID)) throw new Exception("[4]出错了，请您重新输入！");
        $data = $this->Web->query("UPDATE [".X_TEAM_ACCOUNT."] SET [alipay] = ? WHERE [Account] = ? AND [servercode] = ?",[$aliPayID,$_SESSION['username'],getServerCodeForGroupID($_SESSION['group'])]);
        if (!$data) return false;
       // redirect(1,$_SERVER['REQUEST_URI']);
        return true;
    }

    /**
     * 检测账号是否已经绑定支付宝
     * @throws Exception
     */
    public function checkBindAliPay()
    {
        $data = $this->Web->query_fetch_single("SELECT [alipay] FROM [".X_TEAM_ACCOUNT."] WHERE [Account] = ? AND [servercode] = ?",[$_SESSION['username'],getServerCodeForGroupID($_SESSION['group'])]);
        if(!is_array($data)) throw new Exception("账号异常，您的账号非网站注册。");
        if(!$data['alipay'] || empty($data['alipay'])) return 0;
        return 1;
    }


    /**
     * 绑定支付宝按钮
     * @return string
     */
    public function Identification()
    {
        $temp = '';
        $app_id = $this->appID;
        $scope = "auth_base";    //授权方式(auth_user,auth_base)
        $redirect_uri = urlencode($this->url);
        $state = Token::generateToken('alipay-bind');
        $url = "https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id=".$app_id."&scope=".$scope."&redirect_uri=".$redirect_uri."&state=".$state;
        $new = 'target="_blank"';
        $temp.= '<a href="'.$url.'" class="btn btn-primary" ><img src="https://i.alipayobjects.com/common/favicon/favicon.ico" alt=""/> 一键绑定支付宝</a>';
        return $temp;
    }

    /**
     * 二维码生成
     * @param $url
     * @param string $render
     * @param int $width
     * @param int $height
     * @return string
     */
    public function qrCode($url,$render = 'canvas',$width = 180,$height = 180)
    {
        $id = "qrcode-".mt_rand(100,999);
        $temp = '<style>canvas{border: 2px solid #FFF;}</style><div id="'.$id.'"></div>';
        $temp .= '<script type="text/javascript">';
        $temp .= '$("#'.$id.'").empty();';
        $temp .= '$("#'.$id.'").qrcode({';
        $temp .= 'render:"'.$render.'",';
        $temp .= 'width:'.$width.',';
        $temp .= 'height:'.$height.',';
        $temp .= 'text:"'.$url.'"';
        $temp .= '});';
        $temp .= '</script>';
        return $temp;
    }
	
	
	
   /**
	* 电脑网站支付接口
	*/
  
    public function toWebPay($subject,$out_trade_no,$total_amount,$rettype=0)
    {
		try {
			       
					$retUrl=$this->retunUrl;
					if($rettype==1){
					  $retUrl=$this->retunUrl1;	
					}
			   
	                $result = Factory::payment()
	                       ->Page()
	                       ->Pay($subject, $out_trade_no, $total_amount,$retUrl);
					  
					$responseChecker = new ResponseChecker();
	                   //3. 处理响应或异常
				     if ($responseChecker->success($result)) {
					   return $result->body;
					   
				     } else {
					   return [
						   'code' => $result->code,
						   'url'  => '',
						   'msg'  => "调用失败，原因：". $result->msg."，".$result->subMsg,
					   ];
				    }
	               } catch (Exception $e) {
	                   message('error',$e->getMessage());
	               }
	               return null;
				   
	}
   
   
   
    /**
         * 单笔转账接口
         * @param $order_number //订单号
         * @param $pay_no  //转账账号
         * @param $amount  //转账金额
         * @param $title   //备注
         * @return array
         */
        public function toPay($order_number,$pay_no,$amount,$title)
        {
            try {
    
                /** 例：生成收款方信息 **/
                $payeeInfo = array(
                    /** 账户类型,ALIPAY_USER_ID:支付宝会员ID;ALIPAY_LOGON_ID:支付宝登陆账户 **/
                    "identity_type" => "ALIPAY_USER_ID",
                    /** 收款支付宝ID或者账户 **/
                    "identity" => $pay_no,
                    /** 真实姓名,identity_type=ALIPAY_LOGON_ID时，本字段必填 **/
    //                "name" => $pay_name
                );
                /** 非biz_content参数集合 **/
                $textParams = array(
    
                );
    
                /** 构造biz_content业务参数集合 **/
                $bizParams = array(
                    /** 商家侧唯一订单号，由商家自定义。对于不同转账请求，商家需保证该订单号在自身系统唯一 **/
                    "out_biz_no" => $order_number,
                    /** 订单总金额，单位为元,单笔转账最低金额0.1 **/
                    "trans_amount" => $amount,
                    /** 业务产品码，转账固定为TRANS_ACCOUNT_NO_PWD **/
                    "product_code" => "TRANS_ACCOUNT_NO_PWD",
                    /** 收款方信息 **/
                    "payee_info" => $payeeInfo ,
                    /** 转账业务的标题，用于在支付宝用户的账单里显示 **/
                    "order_title" => $title ,
                    /** 业务场景码，转账固定为DIRECT_TRANSFER **/
                    "biz_scene" => "DIRECT_TRANSFER"
                );
                /**  发起API调用  **/
                $result = Factory::Util()->Generic()
                    /** 入参顺序为:
                     * OpenAPI -> 接口调用名称
                     * textParams -> 非biz_content下的参数集合,例如app_auth_token等
                     * bizParams -> biz_content下的业务参数集合
                     * **/
                    ->execute("alipay.fund.trans.uni.transfer",null,$bizParams);
                $responseChecker = new ResponseChecker();
                //3. 处理响应或异常
                if ($responseChecker->success($result)) {
                    @error_log('['.date("h:i:s").'][转账付款][成功] SERVER:['.getServerCodeForGroupID($_SESSION['group']).'] ['.$title.'] TRADE:['.$order_number.'] ALIPAY-ID:['.$pay_no.']['.$amount.'] 支付成功。'."\r\n", 3, $this->errorLog);
                    return [
                        'code' => $result->code,
                        'msg'  => $result->msg,
                    ];
                } else {
                    return [
                        'code' => $result->code,
                        'msg'  => "调用失败，原因：". $result->msg."，".$result->subMsg,
                    ];
                }
                /** 获取接口调用结果 **/
    
            } catch (Exception $e) {
                echo "调用失败，". $e->getMessage(). PHP_EOL;;
            }
            return [];
        }

    /**
     * alipay.trade.query(统一收单线下交易查询)
     * @param $outTradeNo
     * @return null
     */
    public function query($outTradeNo)
    {
        try {
            $result = Factory::payment()->common()->query($outTradeNo);
            if("10000" == $result->code){
                return [
                    'code'          => $result->code,
                    'msg'           => $result->tradeStatus,
                    'totalAmount'   => $result->totalAmount,       //交易金额
                    'tradeNo'       => $result->tradeNo,           //支付宝订单号
                    'outTradeNo'    => $result->outTradeNo,        //申请的订单号
                    'sendPayDate'   => $result->sendPayDate,       //付款时间
                    'buyerUserId'   => $result->buyerUserId,       //购买人支付宝ID
                    "buyerPayAmount"=> $result->buyerPayAmount     //付款金额
                ];
            }else{
                return [
                    'code' => $result->code,
                    'msg' => $result->msg.$result->subMsg
                ];
            }

        } catch (Exception $e) {
            message('error',$e->getMessage());
        }
        return null;
    }

    /**
     * alipay.trade.cancel(统一收单交易撤销接口)
     * @param $outTradeNo
     * @return null
     */
    public function cancel($outTradeNo)
    {
        try {
            $result = Factory::payment()->common()->cancel($outTradeNo);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                @error_log('['.date("h:i:s").'][订单关闭][成功] SERVER:['.getServerCodeForGroupID($_SESSION['group']).'] ACCOUNT:['.$_SESSION['username'].'] TRADE:['.$outTradeNo.'] 未付款交易超时关闭或支付完成后全额退款。'."\r\n", 3, $this->errorLog);
                return true;
            } else {
                @error_log('['.date("h:i:s").'][订单关闭][失败] SERVER:['.getServerCodeForGroupID($_SESSION['group']).'] ACCOUNT:['.$_SESSION['username'].'] TRADE:['.$outTradeNo.'] ERROR:'.$result->msg.'-'.$result->subMsg."\r\n", 3, $this->errorLog);
                return false;
            }
        } catch (Exception $e) {
            message('error',$e->getMessage());
        }
        return null;
    }

    /**
     * alipay.trade.close(统一收单交易关闭接口)
     * @param $outTradeNo
     * @return null
     */
    public function close($outTradeNo)
    {
        try {
            $result = Factory::payment()
                ->common()
                ->close($outTradeNo);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            message('error',$e->getMessage());
        }
        return null;
    }

    /**
     * alipay.trade.precreate(统一收单线下交易预创建)
     * @param $subject
     * @param $outTradeNo
     * @param $totalAmount
     * @return array
     */
    public function preCreate($subject,$outTradeNo,$totalAmount)
    {
        try {
            $optionalArgs = [
                "timeout_express" => $this->timeOut,    //支付超时参数（相对超时参数）：在订单创建后开始生效，超时未支付订单将关闭。
//                "time_expire" => $this->timeOut,        //绝对超时参数：用户支付订单的最晚时间。接口请求和用户支付都不可超过time_expire时间。
                "body" => $subject
            ];

            $result = Factory::payment()
                ->FaceToFace()
                ->batchOptional($optionalArgs)
                ->precreate($subject, $outTradeNo, $totalAmount);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                return [
                    'code' => $result->code,
                    'url'  => $result->qrCode,
                    'msg'  => $result->msg,
                ];
            } else {
                return [
                    'code' => $result->code,
                    'url'  => '',
                    'msg'  => "调用失败，原因：". $result->msg."，".$result->subMsg,
                ];
            }
        } catch (Exception $e) {
            message('error',$e->getMessage());
        }
        return null;
    }
	
	
	
	
	

    /**
     * alipay.trade.refund(统一收单交易退款接口)
     * @param $outTradeNo   //退款订单号
     * @param $refundAmount //退款金额
     * @return null
     */
    public function refund($outTradeNo, $refundAmount)
    {
        try {

            $result = Factory::payment()->common()->refund($outTradeNo, $refundAmount);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            message('error',$e->getMessage());
        }
        return null;
    }

    /**
     * alipay.trade.fastpay.refund.query(统一收单交易退款查询)
     * @param $outTradeNo //交易创建时传入的商户订单号
     * @param $outRequestNo //请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
     * @return bool|null
     */
    public function queryRefund($outTradeNo,$outRequestNo)
    {
        try {
            $result = Factory::payment()->common()->refund($outTradeNo, $outRequestNo);
            $responseChecker = new ResponseChecker();
            //3. 处理响应或异常
            if ($responseChecker->success($result)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            message('error',$e->getMessage());
        }
        return null;
    }

    /**
     * 支付宝验签初始化
     * @throws Exception
     */
    public function init()
    {
        $options = new Config();
        $options->protocol = 'https';
        $options->gatewayHost = 'openapi.alipay.com';
        $options->signType = 'RSA2';
        $options->appId = $this->appID;

        // 为避免私钥随源码泄露，推荐从文件中读取私钥字符串而不是写入源码中
        $options->merchantPrivateKey = file_get_contents(__ROOT_DIR__.'alipay/crt1/merchantPrivateKey.txt'); //<-- 请填写您的应用私钥，例如：MIIEvQIBADANB ... ... -->

        $options->alipayCertPath = __ROOT_DIR__.'alipay/crt1/'.'alipayCertPublicKey_RSA2.crt';//'<-- 请填写您的支付宝公钥证书文件路径，例如：/foo/alipayCertPublicKey_RSA2.crt -->';
        $options->alipayRootCertPath = __ROOT_DIR__.'alipay/crt1/'.'alipayRootCert.crt';//'<-- 请填写您的支付宝根证书文件路径，例如：/foo/alipayRootCert.crt" -->';
        $options->merchantCertPath = __ROOT_DIR__.'alipay/crt1/'.'appCertPublicKey_2021003108642068.crt'; //'<-- 请填写您的应用公钥证书文件路径，例如：/foo/appCertPublicKey_2019051064521003.crt -->';

        //注：如果采用非证书模式，则无需赋值上面的三个证书路径，改为赋值如下的支付宝公钥字符串即可
        // $options->alipayPublicKey = '<-- 请填写您的支付宝公钥，例如：MIIBIjANBg... -->';

        //可设置异步通知接收服务地址（可选）
//        $options->notifyUrl = __BASE_URL__."market/bindalipay";

        //可设置AES密钥，调用AES加解密相关接口时需要（可选）
        //$options->encryptKey = "<-- 请填写您的AES密钥，例如：aa4BtZ4tspm2wnXLb1ThQA== -->";

        return $options;
    }

}
?>