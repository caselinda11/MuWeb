

<?php
/**
 * 物品显示 API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
error_reporting(E_ALL & ~E_NOTICE);
define('access', 'api');

include('../includes/Init.php');
/*防止恶意查询*/



	$filepath=__PATH_INCLUDES_CRON__."order.php";
	
	
	
	echo(md5_file($filepath));

	exit();
	
//$trading = new trading();
//$result=$trading->toWebPay("测试网址","2088843341843",0.01);


$printContent='<div class="ItemData Items"><div class="mb-1 SetItemName ExcItemName ">卓越的 大天使之剑 +13</div><div class="ItemDurability">耐久力:[199/220]</div><div class="UseItemReq">剑士 可以使用</div><div class="UseItemReq">魔剑士 可以使用</div><div class="UseItemReq">圣导师 可以使用</div><div class="HarmonyOption">攻击时无视 SD 概率提高 +10</div><div class="skillNameText">[旋风斩]</div><div class=\'text-primary\'>体力 +10</div><div class="LuckyItem">幸运(灵魂宝石之成功率 +25%)</div><div class="LuckyItem">幸运(会心一击率 +5%)</div><div class="ItemOption">追加攻击力 +28</div><div class="ExcItemOption">卓越攻击几率增加 +10%</div><div class="ExcItemOption">攻击力增加 +等级20/20</div><div class="ExcItemOption">攻击力增加 +2%</div><div class="ExcItemOption">攻击(魔法)速度增加 +7</div><div class="ExcItemOption">杀死怪物时所获魔法值增加 +生命值/8</div><div class="ExcItemOption">杀死怪物时所获魔法值增加 +魔法值/8</div></div>';

try{
	
 
 
 $market = new \Plugin\Market\Market();
 
 $market->PrintItemToData("16EFC70000000060000800FFFFFFFFFF");
 // $item=$this->equipment->getItemInfo();
 // echo($market->getItemType("016CC70000000001057000FFFFFFFFFF"));
  
 	
  
  
 // $itemName = $this->equipment->getItemName($item['section'],$item['index'],$item['level']);
				
 
 
}
catch(Exception $e){
	echo "调用失败，". $e->getMessage();
	
}
 
 
		//	PrintItemToData($item_code)		 





