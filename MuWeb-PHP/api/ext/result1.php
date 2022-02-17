  <?php
 
  
 // include('../../templates/Empire/inc/template.functions.php');
   define('access', 'api'); 
   include('../../includes/Init.php');
  error_reporting(E_ALL & ~E_NOTICE);
  
  use Plugin\equipment;
  ?>
  <!DOCTYPE html>
  <html>
  <head>
     <meta charset="utf-8">
     <!--    移动优先-->
     <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
     <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
     <title><?=config('website_title')?></title>
     <meta name="generator" content="XTEAMCMS <?=__X_TEAM_VERSION__?>"/>
     <meta name="author" content="XTEAMCMS"/>
     <meta name="description" content="<?=config('website_meta_keywords')?>"/>
     <meta name="keywords" content="<?=config('website_meta_keywords')?>"/>
     <link rel="shortcut icon" href="<?=__PATH_TEMPLATE__?>favicon.ico"/>
     <!-- DataTables -->
     <link href="<?=__PATH_PUBLIC__; ?>plugins/datatables/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
     
     <link rel="stylesheet" href="<?=__PATH_PUBLIC_CSS__?>bootstrap.min.css" />
     <link rel="stylesheet" href="<?=__PATH_PUBLIC_CSS__?>font-awesome.min.css" />
     <link rel="stylesheet" href="<?=__PATH_TEMPLATE_CSS__?>style.css" />
     <link rel="stylesheet" href="<?=__PATH_PUBLIC_CSS__?>profiles.css" />
     <link rel="stylesheet" href="<?=__PATH_TEMPLATE_CSS__?>override.css" />
     <script>
         const baseUrl = '<?=__BASE_URL__?>';
     </script>
     <script src="<?=__PATH_PUBLIC_JS__?>jquery.min.js"></script><!--2.2.4-->
     <script src="<?=__PATH_PUBLIC_JS__?>bootstrap.bundle.js"></script>
     <!-- Required datatable js -->
     <script src="<?=__PATH_PUBLIC__; ?>plugins/datatables/js/jquery.dataTables.min.js"></script>
	 <script src="<?=__PATH_PUBLIC_JS__?>main.js"></script>

	 
  </head>
  <body>
  
  </body>
  </html>
<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

$result1=null;
$result2=null;


try {
    // if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit;
    if (!isLoggedIn()) exit;
    if ( !check_value($_GET['out_trade_no'])) exit;
	
	$market = new \Plugin\Market\Market();
	
    $trading = new trading();
	$out_trade_no=$_GET['out_trade_no'];
	$result1=$market->queryMarketInBuy(1,$out_trade_no);
	$result2=$market->queryMarketInBuy(2,$out_trade_no);
	if(!is_array($result1) &&  !is_array($result2)) exit;
	
	
	if(is_array($result2)){//如果是角色
	
	                    $id=$result2[0]['ID'];
						$getData = $market->getMarketCharList($id);
	                    if(!is_array($getData)) exit(jsAlt('订单获取失败，请联系在线客服。','danger'));
	                    $data = $trading->query($getData[0]['out_trade_no']);
	                    if (!is_array($data)) exit;
	                    if ("10000" != $data['code']) exit; //当值不为10000时表示用户未扫码
	                    $CharConfig = $market->loadConfig('char');
	                    switch ($data['msg']){
	                        case "TRADE_SUCCESS": //交易成功
	                            #减去手续费（小数点四舍五入）
	                           // $price = ($getData[0]['price'] - round($CharConfig['price_rate'] * $getData[0]['price'] / 100,2));
								
								$price=$getData[0]['price'] ;
	                            $muonline = Connection::Database("MuOnline",$_SESSION['group']);
	                            $web = Connection::Database("Web");
								
	    
	                            #先生成一个订单号 用于给卖家付款
	                            $PayNo = 'TOPAY'.date('YmdHis').rand(1000,9999);
	    
	                            #操作数据库
	                            try{
	                                $muonline->beginTransaction();
	                                $web->beginTransaction();
	                                if($getData[0]['status'] != 1) throw new Exception("该角色已经售出或已下架。");
	                               // if ($data['buyerPayAmount'] != $data['totalAmount']) throw new Exception("付款金额与角色金额不符。");
	                                $character = new character();
	                                #卖家数据#取键值
	                                $sell_accountData = $character->getAccountCharacterNameForAccount($_SESSION['group'],$getData[0]['username']);
	                                if(!check_value($sell_accountData))  throw new Exception("无法获取卖家[AccountCharacter]信息。");
	                                $sell_accountDataKey = array_search($getData[0]['name'],$sell_accountData);
	                                if(!$sell_accountDataKey) throw new Exception("该角色已经下架，请选择其他角色。");
	                                #买家数据#取键值
	                                $buy_accountData = $character->getAccountCharacterNameForAccount($_SESSION['group'],$_SESSION['username']);
	                                if(!check_value($buy_accountData)) throw new Exception("买家账号还是一个新号。");
	                                $buy_accountDataKey = array_search('',$buy_accountData);
	                                if(!$buy_accountDataKey) throw new Exception("买家没有足够的位置储存新角色。");
	    
	                                $muonline->query("UPDATE [AccountCharacter] SET ".$sell_accountDataKey." = ? WHERE Id = ?",[null,$getData[0]['username']]);
	                                $muonline->query("UPDATE [AccountCharacter] SET ".$buy_accountDataKey." = ? WHERE Id = ?",[$getData[0]['name'],$_SESSION['username']]);
	                                $muonline->query("UPDATE [Character] SET [AccountID] = ?,[CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[$_SESSION['username'],0,$getData[0]['username'],$getData[0]['name']]);
	                                $web->query("UPDATE [X_TEAM_MARKET_CHAR] SET [status] = ?,[buy_username] = ?,[buy_price] = ?,[buy_alipay] = ?,[pay_out_trade_no] = ?,[buy_date] = ? WHERE [ID] = ? AND [name] = ?",[2,$_SESSION['username'],$price,$data['buyerUserId'],$PayNo,logDate(),$getData[0]['ID'],$getData[0]['name']]);
	    
	                                #所有都完成以后给卖家付款
	                                $SellAccountID = $web->query_fetch_single("SELECT [alipay] FROM [".X_TEAM_ACCOUNT."] WHERE [servercode] = ? AND [Account] = ?",[getServerCodeForGroupID($_SESSION['group']),$getData[0]['username']]);
	                                if (!is_array($SellAccountID)) throw new Exception("卖家账号信息异常。");
	                                if (!$SellAccountID['alipay']) throw new Exception("卖家支付宝ID为空。");
									
									$price_totran=$price - round($CharConfig['price_rate'] * $price/ 100,2);
									
									
	                                $result = $trading->toPay($PayNo,$SellAccountID['alipay'],round($price_totran,2),"[九鼎奇迹]角色售卖成功");
	                                if("10000" != $result['code']) throw new Exception($result['msg']);
	    
	                                #交易完成 写一份日志
	                                @error_log('['.date("h:i:s").'][交易完成] SERVER:['.getServerCodeForGroupID($_SESSION['group']).'] ACCOUNT:['.$_SESSION['username'].'] ID:['.$id.'] TRADE:['.$getData[0]['out_trade_no'].'] ALIPAY:['.$data['tradeNo'].'] PRICE:['.$data['buyerPayAmount'].'] 交易完成!'."\r\n", 3, $errorCharFile);
	                                $muonline->commit();
	                                $web->commit();
	                               // exit(json_encode(["code"=>"10000","msg"=>"Success","data" => "恭喜您，购买成功。[".$getData[0]['name']."]已发放至您的账号中"]));
								  exit(jsAlt("恭喜您，购买成功。[".$getData[0]['name']."]已发放至您的账号中",'success',1));
								   
								 
	                            }catch (Exception $e){
	                                $trading->cancel($getData[0]['out_trade_no']); //撤销退款
	                                $market->clearMarketOutTradeNo($id); //清理二维码
	                                $muonline->rollBack();
	                                $web->rollBack();
	                                #发放物品错误写一份日志
	                                @error_log('['.date("h:i:s").'][交易失败] SERVER:['.getServerCodeForGroupID($_SESSION['group']).'] ACCOUNT:['.$_SESSION['username'].'] TRADE:['.$getData[0]['out_trade_no'].'] ALIPAY:['.$data['tradeNo'].'] '.$e->getMessage()."\r\n", 3, $errorCharFile);
	                              //  exit(json_encode(["code"=>"10000","msg"=>"Success","data" => "<div class='text-center'>角色发放失败，付款金额将由原路返回。<br>失败原因:".$e->getMessage()."，<br>如有疑问请联系在线客服。</div>"]));
								   exit(jsAlt("角色发放失败，付款金额将由原路返回。<br>失败原因:".$e->getMessage()."，<br>如有疑问请联系在线客服。",'danger',1));
	                            }
	                            break;
	                        case "TRADE_CLOSED": //未付款交易超时关闭或支付完成后全额退款
	    //                        $market->clearMarketOutTradeNo($id,1);
	    //                        $trading->cancel($getData[0]['out_trade_no']);
	                            exit;
	                            break;
	                        default:
	                            exit;
	                            break;
	                    }
		
		
	}
	else  //如果是物品
	{
		$ItemConfig = $market->loadConfig('item');
		if(count($result1)>1 ){
			$cart=array();
			foreach ($result1 as $item){
			   array_push($cart,$item["ID"]); 
			}
			
			for ($i=0;$i<count($cart);$i++){
				if(!check_value($cart[$i]))exit(jsAlt('['.$map.']数据异常，请联系在线客服。', 'danger'));
				if(!Validator::UnsignedNumber($cart[$i])) exit(jsAlt('['.$map.']数据异常，请联系在线客服。', 'danger'));
				$map = $i+1;
				$getData = $market->getMarketItemList($cart[$i]);
				$noData[$i] = $getData[0];
				if (!is_array($getData)) exit(jsAlt('['.$map.']订单获取失败，请联系在线客服。'.$cart[$i], 'danger'));
				$out_trade_no = $getData[0]['out_trade_no']; //同一个订单号
				#减去手续费（小数点四舍五入）
				//$price[$i] = ($getData[0]['price'] - round($ItemConfig['price_rate'] * $getData[0]['price'] / 100,2));
				$price[$i] =$getData[0]['price'] ;
			}
			
			$data = $trading->query($out_trade_no);
			if (!is_array($data)) exit; //查询订单状态
			
			if ("10000" != $data['code']) exit; //当值不为10000时表示用户未扫码
			
			if ($data['msg']=="TRADE_SUCCESS") {
			//if (1==1) {	
				 	$muonline = Connection::Database("MuOnline", $_SESSION['group']);
					$web = Connection::Database("Web");
					#先生成一个订单号 用于给卖家付款
					$PayNo = 'TOPAY' . date('YmdHis') . rand(1000, 9999);
					#操作数据库
					try {
						$muonline->beginTransaction();
						$web->beginTransaction();
						$itemName = "";
					//	if ($data['buyerPayAmount'] != $data['totalAmount']) throw new Exception("付款金额与物品金额不符。");
						
						for($i=0;$i<count($cart);$i++){
							$map = $i+1;
							if ($noData[$i]['status'] != 1) throw new Exception("[".$map."]该道具已经售出或已下架。");
		
							//发送到仓库
							if ($config['sendType']==3) {
							    $warehouse = new \Plugin\Market\warehouse($_SESSION['group']);
								$newWarehouse = $warehouse->warehouseAddItem($noData[$i]['item_code']);
			                 	$muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?", [$newWarehouse, $_SESSION['username']]);
							}
							
							//TL
							if ($config['sendType']==2) {
							    $gminventory = new \Plugin\Market\gm_inventory($_SESSION['group']);
								
								
							    $newgminventory = $gminventory->warehouseAddItem($noData[$i]['item_code']);
								
								
								$muonline->query("UPDATE [gminventory] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?", [$newgminventory, $_SESSION['username']]);
							}
							
							
							//EG
							if ($config['sendType']==1) {
								
								
								  if(!check_value($_SESSION["character_name"]))exit(jsAlt('请先选择角色。', 'danger'));
									$equipment_ = new equipment();
									
									//发送到储物箱
									$itemCode =  $equipment_->convertItem($noData[$i]['item_code']);
									$StorageBox = [
									'UserGuid'      => $_SESSION['userid'],
									'CharacterName' => $_SESSION["character_name"],
									'Type'          => 1,
									'ItemCode'      => $itemCode['type'],
									'ItemData'      => $noData[$i]['item_code'],
									'ValueType'     => '-1',
									'ValueCnt'      => 0,
									'CustomData'    => 0,
									'GetDate'       => logDate(),
									'ExpireDate'    => logDate('+7 day'),
									'UsedInfo'      => 1, # 0已接收,1未接收
									//物品信息
									'index'                 =>  $itemCode['index'],#小编吗
									'level'                 =>  $itemCode['level'],#等级
									'skill'                 =>  $itemCode['skill'],#技能
									'lucky'                 =>  $itemCode['lucky'],#幸运
									'option'                =>  $itemCode['option'],#追加
									'durability'            =>  $itemCode['durability'],#耐久
									'section'               =>  $itemCode['section'],#大编码
									'setOption'             =>  $itemCode['setOption'],#套装
									'newOption'             =>  $itemCode['newOption'],#卓越
									'itemOptionEx'          =>  $itemCode['itemOptionEx'],#380
									'jewelOfHarmonyOption'  =>  $itemCode['jewelOfHarmonyOption'],#再生强化属性
									'socketOption1'         =>  $itemCode['socketOption'][1],#镶嵌[0-4]
									'socketOption2'         =>  $itemCode['socketOption'][2],#镶嵌[0-4]
									'socketOption3'         =>  $itemCode['socketOption'][3],#镶嵌[0-4]
									'socketOption4'         =>  $itemCode['socketOption'][4],#镶嵌[0-4]
									'socketOption5'         =>  $itemCode['socketOption'][5],#镶嵌[0-4]
									'socketBonusPotion'     =>  $itemCode['socketBonusPotion'],#荧光
									'periodItemOption'      =>  $itemCode['periodItemOption'],#时限
									];
									if (empty($StorageBox))  throw new Exception('[10] - '.'出错了，请您重新输入！');

									 $query = "INSERT INTO [T_StorageBox] ([UserGuid],[CharacterName],[Type],[ItemCode],[ItemData],[ValueType],[ValueCnt],[CustomData],[GetDate],[ExpireDate],[UsedInfo]) VALUES (".$StorageBox['UserGuid'].", '".$StorageBox['CharacterName']."', ".$StorageBox['Type'].", '".$StorageBox['ItemCode']."', '".$StorageBox['ItemData']."', '".$StorageBox['ValueType']."', ".$StorageBox['ValueCnt'].", ".$StorageBox['CustomData'].", '".$StorageBox['GetDate']."', '".$StorageBox['ExpireDate']."', ".$StorageBox['UsedInfo'].")";
									$muonline->query($query);
								
							}
							$web->query("UPDATE [X_TEAM_MARKET_ITEM] SET [status] = ?,[buy_username] = ?,[buy_price] = ?,[buy_alipay] = ?,[pay_out_trade_no] = ?,[buy_date] = ? WHERE [ID] = ?", [2, $_SESSION['username'], $price[$i], $data['buyerUserId'], $PayNo, logDate(), $noData[$i]['ID']]);
		
							#所有都完成以后给卖家付款
							$SellAccountID = $web->query_fetch_single("SELECT [alipay] FROM [" . X_TEAM_ACCOUNT . "] WHERE [servercode] = ? AND [Account] = ?", [getServerCodeForGroupID($_SESSION['group']), $noData[$i]['username']]);
							if (!is_array($SellAccountID)) throw new Exception("[".$map."]卖家账号信息异常。");
							if (!$SellAccountID['alipay']) throw new Exception("[".$map."]卖家支付宝ID为空。");
							
						}
						
						
						#将合计金额转账给卖家
						$pricesum=0;
						for($i=0;$i<count($price);$i++){
							$pricesum=$pricesum+$price[$i];
						}
						$price_totran=$pricesum - round($ItemConfig['price_rate'] * $pricesum/ 100,2);
						
						$result = $trading->toPay($PayNo, $SellAccountID['alipay'], round($price_totran,2), "[九鼎奇迹]物品售卖成功");
						if ("10000" != $result['code']) throw new Exception($result['msg']);
							#交易完成 写一份日志
						@error_log('[' . date("h:i:s") . '][交易完成][购物车] SERVER:[' . getServerCodeForGroupID($_SESSION['group']) . '] ACCOUNT:[' . $_SESSION['username'] . '] TRADE:[' . $noData[0]['out_trade_no'] . '] ALIPAY:[' . $data['tradeNo'] . '] PRICE:[' . $price[$i] . '] 交易完成!' . "\r\n", 3, $errorItemFile);
						
						foreach ($noData as $item1){
			             $itemName .="[" . $item1['item_name'] . "]";
			            }
						
						
						$muonline->commit();
						$web->commit();
					//	exit(json_encode(["code" => "10000", "msg" => "Success", "data" => "恭喜您，购买成功！<br>".$itemName."道具已发放至您的仓库中"]));
					    exit(jsAlt("恭喜您，购买成功！<br>".$itemName."道具已发放至您的仓库中", 'success'));
					
					
					} catch (Exception $e) {
						for($i=0;$i<count($cart);$i++) {
							$trading->cancel($noData[$i]['out_trade_no']); //撤销退款
							$market->clearMarketOutTradeNo($cart[$i]);
							#发放物品错误写一份日志
							@error_log('[' . date("h:i:s") . '][交易失败] SERVER:[' . getServerCodeForGroupID($_SESSION['group']) . '] ACCOUNT:[' . $_SESSION['username'] . '] TRADE:[' . $noData[$i]['out_trade_no'] . '] ALIPAY:[' . $data['tradeNo'] . '] ' . $e->getMessage() . "\r\n", 3, $errorItemFile);
						}
						$muonline->rollBack();
						$web->rollBack();
						//exit(json_encode(["code" => "10000", "msg" => "Success", "data" => "<div class='text-center'>物品发放失败，付款金额将由原路返回。<br>失败原因:" . $e->getMessage() . "，<br>如有疑问请联系在线客服。</div>"]));
						exit(jsAlt("物品发放失败，付款金额将由原路返回。<br>失败原因:" . $e->getMessage() . "，<br>如有疑问请联系在线客服。", 'danger'));
					}
					
			}
		}else {
			
			$id=$result1[0]['ID'];
			
			$getData = $market->getMarketItemList($id);
			
			
			
			if (!is_array($getData)) exit(jsAlt('订单获取失败，请联系在线客服。', 'danger'));
			if (!is_array($getData[0])) exit(jsAlt('订单获取失败，请联系在线客服。', 'danger'));
			#减去手续费（小数点四舍五入）
			//$price = ($getData[0]['price'] - round($ItemConfig['price_rate'] * $getData[0]['price'] / 100,2));
			$price =$getData[0]['price'] ;
			$data = $trading->query($getData[0]['out_trade_no']);
			if (!is_array($data)) exit; //查询订单状态
			if ("10000" != $data['code']) exit; //当值不为10000时表示用户未扫码
			
			switch ($data['msg']) {
				case "TRADE_SUCCESS": //交易成功
					$muonline = Connection::Database("MuOnline", $_SESSION['group']);
					$web = Connection::Database("Web");
					#先生成一个订单号 用于给卖家付款
					$PayNo = 'TOPAY' . date('YmdHis') . rand(1000, 9999);
					#操作数据库
					try {
						$muonline->beginTransaction();
						$web->beginTransaction();
						if ($getData[0]['status'] != 1) throw new Exception("该道具已经售出或已下架。");
						//if ($data['buyerPayAmount'] != $data['totalAmount']) throw new Exception("付款金额与物品金额不符。".$data['buyerPayAmount'].":".$data['totalAmount']);
		
						if ($config['sendType']==3) {
						
						$warehouse = new \Plugin\Market\warehouse($_SESSION['group']);
						$newWarehouse = $warehouse->warehouseAddItem($getData[0]['item_code']);
		
						$muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?", [$newWarehouse, $_SESSION['username']]);
						}
						
						if ($config['sendType']==2) {
						$gminventory = new \Plugin\Market\gm_inventory($_SESSION['group']);
						
						$newgminventory = $gminventory->warehouseAddItem($getData[0]['item_code']);
						
						$newgminventory =substr($newgminventory,0,1920);
						
						$muonline->query("UPDATE [gminventory] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?", [$newgminventory, $_SESSION['username']]);
						}
						
						if ($config['sendType']==1) {
					    
						if(!check_value($_SESSION["character_name"]))exit(jsAlt('请先选择角色。', 'danger'));
							
						$equipment_ = new equipment();
						
						//发送到储物箱
						$itemCode =  $equipment_->convertItem($getData[0]['item_code']);
						$StorageBox = [
						'UserGuid'      => $_SESSION['userid'],
						'CharacterName' => $_SESSION["character_name"],
						'Type'          => 1,
						'ItemCode'      => $itemCode['type'],
						'ItemData'      => $getData[0]['item_code'],
						'ValueType'     => '-1',
						'ValueCnt'      => 0,
						'CustomData'    => 0,
						'GetDate'       => logDate(),
						'ExpireDate'    => logDate('+7 day'),
						'UsedInfo'      => 1, # 0已接收,1未接收
						//物品信息
						'index'                 =>  $itemCode['index'],#小编吗
						'level'                 =>  $itemCode['level'],#等级
						'skill'                 =>  $itemCode['skill'],#技能
						'lucky'                 =>  $itemCode['lucky'],#幸运
						'option'                =>  $itemCode['option'],#追加
						'durability'            =>  $itemCode['durability'],#耐久
						'section'               =>  $itemCode['section'],#大编码
						'setOption'             =>  $itemCode['setOption'],#套装
						'newOption'             =>  $itemCode['newOption'],#卓越
						'itemOptionEx'          =>  $itemCode['itemOptionEx'],#380
						'jewelOfHarmonyOption'  =>  $itemCode['jewelOfHarmonyOption'],#再生强化属性
						'socketOption1'         =>  $itemCode['socketOption'][1],#镶嵌[0-4]
						'socketOption2'         =>  $itemCode['socketOption'][2],#镶嵌[0-4]
						'socketOption3'         =>  $itemCode['socketOption'][3],#镶嵌[0-4]
						'socketOption4'         =>  $itemCode['socketOption'][4],#镶嵌[0-4]
						'socketOption5'         =>  $itemCode['socketOption'][5],#镶嵌[0-4]
						'socketBonusPotion'     =>  $itemCode['socketBonusPotion'],#荧光
						'periodItemOption'      =>  $itemCode['periodItemOption'],#时限
						];
						if (empty($StorageBox))  throw new Exception('[10] - '.'出错了，请您重新输入！');

						 $query = "INSERT INTO [T_StorageBox] ([UserGuid],[CharacterName],[Type],[ItemCode],[ItemData],[ValueType],[ValueCnt],[CustomData],[GetDate],[ExpireDate],[UsedInfo]) VALUES (".$StorageBox['UserGuid'].", '".$StorageBox['CharacterName']."', ".$StorageBox['Type'].", '".$StorageBox['ItemCode']."', '".$StorageBox['ItemData']."', '".$StorageBox['ValueType']."', ".$StorageBox['ValueCnt'].", ".$StorageBox['CustomData'].", '".$StorageBox['GetDate']."', '".$StorageBox['ExpireDate']."', ".$StorageBox['UsedInfo'].")";
						 $muonline->query($query);
							
						}	
					
 					    $web->query("UPDATE [X_TEAM_MARKET_ITEM] SET [status] = ?,[buy_username] = ?,[buy_price] = ?,[buy_alipay] = ?,[pay_out_trade_no] = ?,[buy_date] = ? WHERE [ID] = ?", [2, $_SESSION['username'], $price, $data['buyerUserId'], $PayNo, logDate(), $getData[0]['ID']]);
						
						#所有都完成以后给卖家付款
						$SellAccountID = $web->query_fetch_single("SELECT [alipay] FROM [" . X_TEAM_ACCOUNT . "] WHERE [servercode] = ? AND [Account] = ?", [getServerCodeForGroupID($_SESSION['group']), $getData[0]['username']]);
						if (!is_array($SellAccountID)) throw new Exception("卖家账号信息异常。");
						if (!$SellAccountID['alipay']) throw new Exception("卖家支付宝ID为空。");
						
						echo($SellAccountID['alipay']."---");
						
						$price_totran=$price - round($ItemConfig['price_rate'] * $price/ 100,2);
						
						$result = $trading->toPay($PayNo, $SellAccountID['alipay'], round($price_totran,2), "[九鼎奇迹]物品售卖成功");
						if ("10000" != $result['code']) throw new Exception($result['msg']);
		
						#交易完成 写一份日志
						@error_log('[' . date("h:i:s") . '][交易完成] SERVER:[' . getServerCodeForGroupID($_SESSION['group']) . '] ACCOUNT:[' . $_SESSION['username'] . '] ID:[' . $id . '] TRADE:[' . $getData[0]['out_trade_no'] . '] ALIPAY:[' . $data['tradeNo'] . '] PRICE:[' . $data['buyerPayAmount'] . '] 交易完成!' . "\r\n", 3, $errorItemFile);
						$muonline->commit();
						$web->commit();
						
						exit(jsAlt("恭喜您，购买成功。[" . $getData[0]['item_name'] . "]已发放至您的仓库中", 'success'));
						//exit(json_encode(["code" => "10000", "msg" => "Success", "data" => "恭喜您，购买成功。[" . $getData[0]['item_name'] . "]已发放至您的仓库中"]));
					} catch (Exception $e) {
						$trading->cancel($getData[0]['out_trade_no']); //撤销退款
						$market->clearMarketOutTradeNo($id);
						$muonline->rollBack();
						$web->rollBack();
						#发放物品错误写一份日志
						@error_log('[' . date("h:i:s") . '][交易失败] SERVER:[' . getServerCodeForGroupID($_SESSION['group']) . '] ACCOUNT:[' . $_SESSION['username'] . '] TRADE:[' . $getData[0]['out_trade_no'] . '] ALIPAY:[' . $data['tradeNo'] . '] ' . $e->getMessage() . "\r\n", 3, $errorItemFile);
						//exit(json_encode(["code" => "10000", "msg" => "Success", "data" => "<div class='text-center'>物品发放失败，付款金额将由原路返回。<br>失败原因:" . $e->getMessage() . "，<br>如有疑问请联系在线客服。</div>"]));
						
						exit(jsAlt("物品发放失败，付款金额将由原路返回。<br>失败原因:" . $e->getMessage() . "，<br>如有疑问请联系在线客服。", 'danger'));
					}
					break;
				default:
					exit;
					break;
			}
		}
	
	}
}catch (Exception $e){
	echo($e->getMessage());
    exit(jsAlt($e->getMessage(),'danger'));
}
function jsAlt($msg,$type = "warning",$body = ".modal",$dir=0){ 
	$reurl=__BASE_URL__."market/item";
	if($dir==1){
		$reurl=__BASE_URL__."market/character";
	}
	$location="";
	$location="<script type=\"text/javascript\">setTimeout(function(){window.location.href='".$reurl."';}, 3000)</script>";
	
    return '<script type="text/javascript"> $(document).ready(function() {modal_msg("'.$msg.'");});</script>'.$location;
	
}

?>
