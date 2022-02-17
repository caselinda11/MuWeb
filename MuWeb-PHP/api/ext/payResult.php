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
	
	$data = $trading->query($out_trade_no);
	
	
	                    if (!is_array($data)) exit;
						if ("10000" != $data['code']) throw new Exception(json_encode($data)); //当值不为10000时表示用户未扫码
						switch ($data['msg']){
	                        case "TRADE_SUCCESS": //交易成功
	                            #减去手续费（小数点四舍五入）
	                            $muonline = Connection::Database("MuOnline",$_SESSION['group']);
	                            #操作数据库
	                            try{
	                                $muonline->beginTransaction();
	                              
									$muonline->query("update memb_info   set payStatus=1 where memb___id=?",[$_SESSION['username']]);
									 
								    #交易完成 写一份日志
	                                @error_log('['.date("h:i:s").'][交易完成] SERVER:['.getServerCodeForGroupID($_SESSION['group']).'] ACCOUNT:['.$_SESSION['username'].'] ID:['.$id.'] TRADE:['.$out_trade_no.'] ALIPAY:['.$data['tradeNo'].'] PRICE:['.$data['buyerPayAmount'].'] 交易完成!'."\r\n", 3, $errorCharFile);
	                                $muonline->commit();
	                               // exit(json_encode(["code"=>"10000","msg"=>"Success","data" => "恭喜您，购买成功。[".$getData[0]['name']."]已发放至您的账号中"]));
								   exit(jsAlt("恭喜您，支付成功",'success'));
								   
								 
	                            }catch (Exception $e){
	                                $trading->cancel($out_trade_no); //撤销退款
	                                $muonline->rollBack();
	                                #发放物品错误写一份日志
	                                @error_log('['.date("h:i:s").'][交易失败] SERVER:['.getServerCodeForGroupID($_SESSION['group']).'] ACCOUNT:['.$_SESSION['username'].'] TRADE:['.$out_trade_no.'] ALIPAY:['.$data['tradeNo'].'] '.$e->getMessage()."\r\n", 3, $errorCharFile);
	                              //  exit(json_encode(["code"=>"10000","msg"=>"Success","data" => "<div class='text-center'>角色发放失败，付款金额将由原路返回。<br>失败原因:".$e->getMessage()."，<br>如有疑问请联系在线客服。</div>"]));
								   exit(jsAlt("支付失败，付款金额将由原路返回。<br>失败原因:".$e->getMessage()."，<br>如有疑问请联系在线客服。",'danger'));
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
	
	
}catch (Exception $e){
	echo($e->getMessage());
    exit(jsAlt($e->getMessage(),'danger'));
}
function jsAlt($msg,$type = "warning",$body = ".modal"){ 
	$location="<script type=\"text/javascript\">setTimeout(function(){window.location.href=sessionStorage.getItem(\"dir\");}, 3000)</script>";
	return '<script type="text/javascript"> $(document).ready(function() {modal_msg("'.$msg.'");});</script>'.$location;
	
}

?>
