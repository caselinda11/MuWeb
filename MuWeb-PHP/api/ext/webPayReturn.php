

<?php
/**
 * 物品显示 API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
define('access', 'api');

include('../../includes/Init.php');
/*防止恶意查询*/

try{
	
//$trading = new trading();
//$result=$trading->toWebPay("2088821355931843",0.1,"测试网址","localhost:8080/api/ext/webPayReturn.php");
echo("66666666");


}
catch(Exception $e){
	echo "调用失败，". $e->getMessage();
	
}
