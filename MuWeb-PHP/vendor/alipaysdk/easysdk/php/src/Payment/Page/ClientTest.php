<?php
namespace Alipay\EasySDK\Payment\Page;

use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Test\TestAccount;





class ClientTest 
{
    

    public function testPay($out_trade_no,$total_amount,$subject)
    {
        $create = Factory::payment()->common()->create($subject,
            $out_trade_no, $total_amount, "");
        $result = Factory::payment()->page()->pay($subject, $create->outTradeNo, $total_amount, "localhost:8080/callback");
        //$this->assertEquals(true, strpos($result->body, 'alipay-easysdk-php-') > 0);
        //$this->assertEquals(true, strpos($result->body, 'sign') > 0);
		return $result;
		
    }

    public function testPayWithOptionalNotify()
    {
        $create = Factory::payment()->common()->create("Iphone6 16G",
            microtime(), "88.88", "2088002656718920");
        $result = Factory::payment()->page()
            ->asyncNotify("https://www.test2.com/newCallback")
            ->pay("Iphone6 16G", $create->outTradeNo, "0.10", "https://www.taobao.com");
        $this->assertEquals(true, strpos($result->body, 'alipay-easysdk-php-') > 0);
        $this->assertEquals(true, strpos($result->body, 'sign') > 0);
    }

}