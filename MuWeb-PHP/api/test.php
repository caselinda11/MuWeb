<?php
/**
 * 物品显示 API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'api');

include('../includes/Init.php');
/*防止恶意查询*/
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);

//$item = new \Plugin\equipment();
//$itemDATA = $_POST['item'];
 

echo($_SESSION["character_name"]);
exit();

try{
	//throw new Exception("数据错误，请重新尝试！");
	$trading = new trading();
$result=$trading->toPay("342343243","40414138@qq.com",1,"测试网址");
echo json_encode(array_values($result));
//echo($result);
}
catch(Exception $e){
	echo "调用失败，". $e->getMessage();
	
}

/*
use Alipay\EasySDK\Kernel\Factory;
use Alipay\EasySDK\Kernel\Util\ResponseChecker;
use Alipay\EasySDK\Kernel\Config;

try {
    //2. 发起API调用（以支付能力下的统一收单交易创建接口为例）
    $result = Factory::payment()->common()->create("iPhone6 16G", "20200326235526001", "88.88", "2088002656718920");
    $responseChecker = new ResponseChecker();
    //3. 处理响应或异常
    if ($responseChecker->success($result)) {
        echo "调用成功". PHP_EOL;
    } else {
        echo "调用失败，原因：". $result->msg."，".$result->subMsg.PHP_EOL;
    }
} catch (Exception $e) {
    echo "调用失败，". $e->getMessage(). PHP_EOL;;
}
*/


//$res = $item->printItems($itemDATA);
//echo json_encode($res);
//print_r($res);