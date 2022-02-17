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
$item = new \Plugin\equipment();
$itemDATA = $_POST['item'];

try{
$res = $item->printItems($itemDATA);
print_r($res);
}
catch(Exception $ex){
	
	print_r($ex->getMessage());
}
//echo json_encode($res);
