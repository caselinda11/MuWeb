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
    //  if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
    $path = parse_url($_SERVER['HTTP_REFERER']);
    if(empty($path['path']) || !isset($path['path'])) exit;
    $market = new \Plugin\Market\Market();
    $trading = new trading();
	
	if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
	
	
switch ($path['path']){
    case "/market/character":
        if (isset($_POST['id']) && $_POST['id']) {
            if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
            if (!Validator::UnsignedNumber($_POST['id'])) exit(json_encode(['code' => '20002', 'msg' => '非法请求']));
            $getData = $market->getMarketCharList($_POST['id']);
            if (!is_array($getData)) exit(json_encode(['code' => '20003', 'msg' => '该角色已被购买，请选择其他角色。']));
            if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(['code' => '20002','msg'=> 'Invalid argument']));
            if (!check_value($_POST['modules']) || !isset($_POST['modules'])) exit;
        }
        $CharConfig = $market->loadConfig('char');
        switch ($_POST['modules']){
            case "qrcocde":
                if (!isLoggedIn()) exit(json_encode(["code" => "20000", "msg" => "请先登陆。"]));
                #检测是否交易中
                if($getData[0]['status'] > 0){
                    if(!$getData[0]['buy_date']) exit(json_encode(['code' => '20002', 'msg' => '数据异常，该角色暂时无法购买。']));
                    if($_SESSION['username'] != $getData[0]['buy_username']){
                        if(strtotime(logDate()) - strtotime($getData[0]['buy_date']) <= 60*$market->timeOut) exit(json_encode(['code' => '20003', 'msg' => '该角色正处于交易中或已被购买。']));
                    }
                }

                $trade_no =  getUuid();
                if(!$market->getMarketOutTradeNo($_POST['id'],$_SESSION['username'],$trade_no,1)) exit(json_encode(["code" => "20005", "msg" => "订单号申请失败，请稍后尝试或联系在线客服。"]));
               
				#申请二维码   
				#二维码支付修改成网站支付 
                $url = $trading->toWebPay("[九鼎奇迹]角色购买", $trade_no, $getData[0]['price']);
				if(is_array($url)) exit(json_encode(["code" => $url['code'], "msg" => $url['msg']]));;
			    $content = $url;

                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$content]));
                break;
            case "buy":
                if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
				
				$payStatus=$market->getPayStatus();
				if(!check_value($payStatus) || $payStatus==0){
					$url = $trading->toWebPay("请支付1.00元验证支付宝",  getUuid(), 1,1);
					exit(json_encode(['code' => '20008', 'msg' => '支付未绑定！','data'=>$url]));
			    }
			
  			    #验证是否为同一大区
                if($_SESSION['group'] != $getData[0]['group']) exit(json_encode(['code' => '20002', 'msg' => '大区不符，请选择与您大区相符的角色。']));
                #检测是否交易中
                if($getData[0]['status'] > 0){
                    if(!$getData[0]['buy_date']) exit(json_encode(['code' => '20002', 'msg' => '数据异常，该角色暂时无法购买。']));
                    if($_SESSION['username'] != $getData[0]['buy_username']){
                        if(strtotime(logDate()) - strtotime($getData[0]['buy_date']) <= 60*$market->timeOut) exit(json_encode(['code' => '20003', 'msg' => '该角色正处于交易中或已被购买。']));
                    }
                }
                //如果有密码取密码验证
                if($getData[0]['password']){
                    if(!check_value($_POST['password'])) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('请输入密码!','danger');});</script>"]));
                    if(!Validator::UnsignedNumber($_POST['password'])) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('密码不正确，请重新输入。','danger');});</script>"]));
                    if($_POST['password'] !== $getData[0]['password']) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('密码错误，请重新输入。','danger');});</script>"]));
                }
                #确定标题
                $title =  $getData[0]['name'].'['.$getData[0]['price'].'.00]';

                #判断账号是否有位置存放角色
                $character = new Character();
                #买家数据#取键值
                $buy_accountData = $character->getAccountCharacterNameForAccount($_SESSION['group'],$_SESSION['username']);
                if(!check_value($buy_accountData)) throw new Exception("您还是一个新的账号，请先创建一个角色再来！");
                $buy_accountDataKey = array_search('',$buy_accountData);
                if(!$buy_accountDataKey) throw new Exception("您没有足够的位置储存该角色！");

                $temp = '<div class="modal fade" data-backdrop="static" id="trading-buy" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content" style="border:unset">';

                $temp .='<div class="modal-header">';
                $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">'.$title.'</h5>';
                #onclick="trading('.$_POST['id'].',\'close\')"
                $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                $temp .='</div>';
                $temp .= '<div  id="qr-code-content" class="bg-primary">';

                $temp .= '<div style="height: 247px;" class="d-flex justify-content-center align-items-center"><div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span></div></div>';

                $temp .= '</div>';

                $temp .= '<div id="qr-code-footer" class="modal-footer text-center" style="justify-content:center">';
                $temp .= '<div class="text-danger">付款前请确保您账号中有位置存放新角色</div>';
                $temp .= '</div>';

                $temp .='</div></div></div>';

                $temp .= '<script type="text/javascript">';
                $temp .= "$('#trading-buy').modal('show');";
                $temp .= "$('#trading-buy').on('shown.bs.modal', function () {";
                $temp .= "$('#message').modal('hide');";
                $temp .= "getQrCode(".$_POST['id'].");";
                $temp .= "getResult(".$_POST['id'].");";
                $temp .= "});";
                $temp .= "$('#trading-buy').on('hidden.bs.modal', function () {";
                $temp .= "$('#trading-buy').remove();";
                $temp .= "});";
                $temp .= '</script>';
                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                break;
            case "pass":
                $title = '['.$getData[0]['name'].']交易密码';
                #内容
                $content = '';
                $content .= '<div class="">';
                $content .= '<input type="text" id="password" class="form-control" name="password" autocomplete="off" autofocus="autofocus" placeholder="请输入交易密码">';
                $content .= '<div class="modal-footer" style="justify-content:center">';
                $content .= '<button type="button" class="btn btn-success" onClick="trading('.$_POST['id'].',\'buy\')">确定</button>';
                $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>';
                $content .= '</div>';
                $content .= '</div>';
                $temp = '<div class="modal fade" data-backdrop="static" id="message" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content" style="border:unset">';

                $temp .='<div class="modal-header">';
                $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">'.$title.'</h5>';
                $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                $temp .='</div>';

                $temp .=$content;

                $temp .='</div></div></div>';

                $temp .= '<script type="text/javascript">';
                $temp .= "$('#message').modal('show');";
                $temp .= "$('#message').on('shown.bs.modal', function () {";
                $temp .= "$('#password').focus();"; //聚焦
                $temp .= "});";
                $temp .= "$('#message').on('hidden.bs.modal', function () {";
                $temp .= "$('#message').remove();";
                $temp .= "});";
                $temp .= '</script>';
                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                break;
            case "view":
                $Character = new Character();
                $charData = $Character->getCharacterDataForCharacterName($getData[0]['group'],$getData[0]['name']);
                $ExInvNumber = Connection::Database("Me_MuOnline")->query_fetch_single("SELECT [ExInventory] FROM [Character] WHERE [Name] = ?",["嫩杀杀"]);
                if(!$ExInvNumber || !is_array($ExInvNumber)) $ExInvNumber['ExInventory'] = 0;
                if (!is_array($charData)) exit(json_encode(['code' => '20005', 'msg' => '请求错误，请刷新页面重新访问']));

                #交易次数
                $tradingCount = $market->checkSellChar($getData[0]['name']);
                $tradingCount = $CharConfig['frequency'] - $tradingCount;
                #统率
                global $custom;
                $character_cmd = array_search($charData['Class'],$custom['character_cmd']) ? ' - 统率:['.$charData['Leadership'].']' : "";
                #物品处理
                $item = new \Plugin\equipment();
                $itemData = $item->setEquipmentCode($charData['Inventory']);
                #家族
                if($charData['GenFamily'] == 1){$gensName="多普瑞恩";}elseif($charData['GenFamily'] == 2){$gensName="巴纳尔特";}else{$gensName='未加入';}

                $title = "角色信息[".$getData[0]['name']."]";
                $temp = '<div class="modal fade" id="message" tabindex="-1" style="font-size: 14px;">';
                $temp .='<div class="modal-dialog modal-dialog-centered" style="max-width: 560px;height: 488px;">';
                $temp .='<div class="modal-content" style="border-radius:unset;border: 1px solid #2e2e2e;background: url('.__PATH_PUBLIC_IMG__.'inventory/attribute-bg.png);">';
                $temp .='<div class="modal-header" style="border-bottom: 1px solid #55140f;">';
                $temp .= '<h5 class="modal-title" id="staticBackdropLabel">'.$title.'</h5>';
                $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:#FFF">&times;</span></button>';
                $temp .='</div>';
                $temp .='<div style="overflow:hidden;background-color:transparent;">';
                $temp .='<div style="width:100%;">';
                $temp .='<div style="float:left;width: 279px;overflow: hidden;">';
                $temp .='<table class="table table-bordered text-center" style="font-size: 0.79rem;">';
                $temp.='<tr><td style="padding:2px">'.getPlayerClassAvatar($charData['Class'],1,1,'rounded-circle',70).'</td></tr>';
                $temp.='<tr><td>'.$charData['Name'].' - ['.getGroupNameForServerCode($getData[0]['group']).']</td></tr>';
                $temp.='<tr><td>['.getPlayerClassName($charData['Class']).']</td></tr>';
                $temp.='<tr><td>等级:['.$charData['cLevel'].'] - 大师:['.$charData['mLevel'].']</td></tr>';
                $temp.='<tr><td>状态:['.getPkLevel($charData['PkLevel']).'] - 杀人数['.$charData['PkCount'].']</td></tr>';
                $temp.='<tr><td>家族:['.$gensName.'] - ['.getGensRank($charData['GensContribution']).']</td></tr>';
                $temp.='<tr><td>剩余升级点:['.number_format((int)$charData['LevelUpPoint']).']</td></tr>';
                $temp.='<tr><td>力量:['.$charData['Strength'].'] - 敏捷:['.$charData['Dexterity'].']</td></tr>';
                $temp.='<tr><td>体力:['.$charData['Vitality'].'] - 智力:['.$charData['Energy'].']'.$character_cmd.'</td></tr>';
                $temp.='<tr><td>扩展背包:['.$ExInvNumber['ExInventory'].']个</td></tr>';
                $temp.='<tr><td>金币(Zen):['.number_format((int)$charData['Money']).']</td></tr>';
                $temp.='<tr><td data-toggle="tooltip" data-placement="bottom" title="一旦剩余次数使用完该角色则将无法再通过藏宝阁出售">剩余交易次数:[<span class="text-danger">'.$tradingCount.'</span>]次</td></tr>';
                $temp.='</table>';
                $temp .='</div>';

                $temp .='<div class="InventoryM" style="float:right;width: 279px;overflow: hidden;position: relative;">';
                $temp .='<div style="background: url('.__PATH_PUBLIC_IMG__.'inventory/Inventory.jpg);width: 279px;height: 208px;overflow: hidden;">';
                $temp.= '<div class="data-info" data-info="'.$itemData[0].'" style="background: url('.$item->ItemsUrl($itemData[0]).') no-repeat center center;background-size: contain;width: 55px;height: 80px;position: absolute;top: 63px;left: 2px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[1].'" style="background: url('.$item->ItemsUrl($itemData[1]).') no-repeat center center;background-size: contain;width: 55px;height: 80px;position: absolute;top: 63px;right: 3px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[2].'" style="background: url('.$item->ItemsUrl($itemData[2]).') no-repeat center center;background-size: contain;width: 54px;height: 54px;position: absolute;top: 2px;left: 112px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[3].'" style="background: url('.$item->ItemsUrl($itemData[3]).') no-repeat center center;background-size: contain;width: 54px;height: 80px;position: absolute;top: 63px;left: 112px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[4].'" style="background: url('.$item->ItemsUrl($itemData[4]).') no-repeat center center;background-size: contain;width: 54px;height: 54px;position: absolute;top: 151px;left: 112px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[5].'" style="background: url('.$item->ItemsUrl($itemData[5]).') no-repeat center center;background-size: contain;width: 54px;height: 54px;position: absolute;top: 151px;left: 2px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[6].'" style="background: url('.$item->ItemsUrl($itemData[6]).') no-repeat center center;background-size: contain;width: 54px;height: 54px;position: absolute;top: 151px;right: 3px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[7].'" style="background: url('.$item->ItemsUrl($itemData[7]).') no-repeat center center;background-size: contain;width: 92px;height: 54px;position: absolute;top: 2px;right: 3px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[8].'" style="background: url('.$item->ItemsUrl($itemData[8]).') no-repeat center center;width: 54px;height: 54px;position: absolute;top: 2px;left: 2px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[9].'" style="background: url('.$item->ItemsUrl($itemData[9]).') no-repeat center center;width: 35px;height: 35px;position: absolute;top: 20px;left: 66px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[10].'" style="background: url('.$item->ItemsUrl($itemData[10]).') no-repeat center center;width: 35px;height: 35px;position: absolute;top: 170px;left: 66px;"> </div>';
                $temp.= '<div class="data-info" data-info="'.$itemData[11].'" style="background: url('.$item->ItemsUrl($itemData[11]).') no-repeat center center;width: 35px;height: 35px;position: absolute;top: 170px;right: 68px;"> </div>';

                $temp .='</div>';
                $temp .='<div style="width: 279px;height: 276px;border: rgb(49,49,49) solid 2px;">';
                $temp .= '<ul style="margin: 0;padding: 0;list-style-type: none;">';
                for($i=12;$i<=75;$i++){
                    $temp .= '<li style="display: block;width: 32px;height: 32px;margin-top: 2px;margin-left: 2px;border: rgb(49,49,49) solid 1px;float: left;list-style-type: none;"><div class="data-info" data-info="'.$itemData[$i].'" style="background: url('.$item->ItemsUrl($itemData[$i]).') no-repeat center center;background-size: contain;width: 30px;height: 30px;"> </div></li>';
                }
                $temp .= '</ul>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .= '<script type="text/javascript">';
                $temp .= "$('#message').modal('show');";
                $temp .= "$('#message').on('hidden.bs.modal', function () {";
                $temp .= "$('#message').remove();";
                $temp .= "});";
                $temp .= "$(function() {";
                $temp .= '$("[data-toggle=\'tooltip\']").tooltip();';
                $temp .= "altTooltip.init();";
                $temp .= "});";
                $temp .= '</script>';
                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                break;
            case "sell":    //寄售
            if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
			
			$payStatus=$market->getPayStatus();
					if(!check_value($payStatus) || $payStatus==0){
					$url = $trading->toWebPay("请支付1.00元验证支付宝",  getUuid(), 1,1);
					exit(json_encode(['code' => '20008', 'msg' => '支付未绑定！','data'=>$url]));
			}

            //验证账号是否绑定支付宝
            if(!$trading->checkBindAliPay()){
                $temp .='<div class="modal fade" data-backdrop="static" id="bind-alipay" tabindex="-1">';
                $temp .='<div class="modal-dialog  modal-dialog-centered">';
                $temp .='<div class="modal-content" style="border:unset">';
                $temp .='<div class="modal-header">';
                $temp .= '<h5 class="modal-title" id="staticBackdropLabel">提示</h5>';
                $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $temp .= '<span aria-hidden="true">&times;</span>';
                $temp .= '</button>';
                $temp .='</div>';
                $temp .='<div class="modal-body text-center align-self-center">';
                $temp .= '<div class="alert alert-warning" role="alert">检测到您当前账号未绑定支付宝</div>';
                $temp .='<div class="alert alert-warning" role="alert">点击下面按钮前往支付宝授权绑定。</div>';
                $temp .= $trading->Identification();
                $temp .= '</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .= '<script type="text/javascript">';
                $temp .= "$('#bind-alipay').modal('show');";
                $temp .= "$('#bind-alipay').on('hidden.bs.modal', function () {";
                $temp .= "$('#bind-alipay').remove();";
                $temp .= "});";
                $temp .= '</script>';
                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
            }

            $character = new Character();
            $AccountCharacters = $character->getAccountCharacterNameForAccount($_SESSION["group"],$_SESSION["username"]);
                $temp = '<div class="modal fade" id="trading-sell" tabindex="-1">';
                $temp .='<div class="modal-dialog modal-dialog-centered">';
                $temp .='<div class="modal-content" style="border:unset">';
                $temp .='<div class="modal-header">';
                $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">寄售角色</h5>';
                $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                $temp .= '<span aria-hidden="true">&times;</span>';
                $temp .= '</button>';
                $temp .='</div>';
                    $temp .='<div class="modal-body">';
                        $temp .='<div>';
                    $temp .= '<div class="text-center"><p class="alert alert-primary">一旦售出成功将收取总金额的[<strong>'.$CharConfig['price_rate'].'%</strong>]服务费</p></div>';
                    $temp .='<div class="form-group row justify-content-md-center">';
                        $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">选择角色</label>';
                        $temp .='<div class="col-sm-6">';
                            $temp .='<select name="char_name" id="char_name" class="form-control">';
                                if(is_array($AccountCharacters)) {
                                    foreach ($AccountCharacters as $key => $characterName) {
                                        if (empty($characterName)) continue;
                                        if (!$character->checkCharacterIsBan($_SESSION['group'],$characterName)) continue;
                                        $temp .= '<option value="'. $key.'">'.$characterName.'</option>';
                                    }
                                }else{
                                    $temp .= '<option value="">暂无角色</option>';
                                }
                            $temp .='</select>';
                        $temp .='</div>';
                        $temp .='<div class="col-sm-3 col-form-label"></div>';
                    $temp .='</div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                        $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">交易密码</label>';
                        $temp .='<div class="col-sm-6">';
                        $temp .='<input type="text" name="password" id="password" class="form-control" AUTOCOMPLETE="OFF" placeholder="可不填" />';
                        $temp .='</div>';
                        $temp .='<small class="col-sm-3 form-inline text-muted text-left">*买家购买所需</small>';
                    $temp .='</div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                        $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">寄售价格</label>';
                        $temp .='<div class="col-sm-6">';
                        $temp .='<input type="text" name="price" id="price" class="form-control" AUTOCOMPLETE="OFF" placeholder="单位(元)"/>';
                        $temp .='</div>';
                        $temp .='<small class="col-sm-3 form-inline text-muted text-left"></small>';
                    $temp .='</div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                        $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">联系QQ</label>';
                        $temp .='<div class="col-sm-6">';
                        $temp .='<input type="text" name="tencent" id="tencent" class="form-control" placeholder="显示作用" required/>';
                        $temp .='</div>';
                        $temp .='<small class="col-sm-3 form-inline text-muted text-left"></small>';
                    $temp .='</div>';


                    $temp .='<div class="form-group row justify-content-md-center">';
                    $temp .='<button type="button" id="btn-sell" onclick="market_sell(\'sell-char\')" class="btn btn-success col-sm-4">确定</button>';
                    $temp .='</div>';

                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .='</div>';
                $temp .= '<script type="text/javascript">';
                $temp .= "$('#trading-sell').modal('show');";
                $temp .= "$('#trading-sell').on('hidden.bs.modal', function () {";
                $temp .= "$('#trading-sell').remove();";
                $temp .= "});";

                $temp .= '</script>';

                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                break;
            case "selling":
                if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                if(!check_value($_POST['char'])) exit(json_encode(['code' => '20004', 'msg' => '角色不能为空！']));
                if(!Validator::AlphaNumeric($_POST['char'])) exit(json_encode(['code' => '20004', 'msg' => '角色不能为空！']));
                $password = null;
                if (check_value($_POST['pass'])){
                    if(!Validator::UnsignedNumber($_POST['pass'])) exit(json_encode(['code' => '20004', 'msg' => '[1]非法操作！']));
                    $password = $_POST['pass'];
                }
                if(!check_value($_POST['price']) && !Validator::Number($_POST['price'],5,1)) exit(json_encode(['code' => '20004', 'msg' => '[2]非法操作！']));
                if(!check_value($_POST['tencent']) && !Validator::Number($_POST['tencent'],13,5)) exit(json_encode(['code' => '20004', 'msg' => '[3]非法操作！']));
				
					
                #检查账号是否在线
                $common = new common();
                if($common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) exit(json_encode(['code' => '20005', 'msg' => '检测到您当前账号在线，请先退出游戏。']));
                if (!$market->setCharSell($_POST['char'],$_POST['price'],$password,$_POST['tencent'])) exit(json_encode(['code' => '20004', 'msg' => '[3]非法操作！']));
                $message = '恭喜您，角色寄售成功!';
                $temp.= '<script type="text/javascript">';
                $temp.=  '$(function () {';
                $temp.=  'modal_msg("'.$message.'");';
                $temp.=  '});';
                $temp.=  '</script>';
                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                break;
            case "my-trading":  //寄售中的信息
                if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));

                $temp = '';
                $temp .='<div class="" style="overflow-y:auto">';
                $temp .='<table class="mb-3 table table-striped table-bordered text-center text-dark">';
                    $temp .='<thead>';
                        $temp .='<tr>';
                            $temp .='<td>单号</td>';
                            $temp .='<td>角色</td>';
                            $temp .='<td>交易密码</td>';
                            $temp .='<td>发布日期</td>';
                            $temp .='<td>售价</td>';
                            $temp .='<td>操作</td>';
                        $temp .='</tr>';
                    $temp .='</thead>';
                $temp .='<tbody>';
                    #未交易中
                    $data = $market->getMyMarketCharList($_SESSION['group'],$_SESSION['username']);
                    if(is_array($data)){
                        foreach ($data as $char) {
                            $password = $char['password'] ? $char['password'] : '无';
                            $temp .= '<tr>';
                            $temp .= '<td>'.$char['ID'].'</td>';
                            $temp .= '<td>'.$char['name'].'</td>';
                            $temp .= '<td>'.$password.'</td>';
                            $temp .= '<td>'.date("m-d H:i",$char['date']).'</td>';
                            $temp .= '<td>'.$char['price'].'.00</td>';
                            $temp .= '<td><button type="button" class="btn btn-warning" onclick="trading('.$char['ID'].', \'trading-offshelf\')">下架</button></td>';
                            $temp .= '</tr>';
                        }
                    }else{
                        $temp .= '<tr><td colspan="10">暂无寄售信息</td></tr>';
                    }
                    #交易中的
                    $dataTrading = $market->getMyMarketCharList($_SESSION['group'],$_SESSION['username'],1);
                    if(is_array($dataTrading)){
                        $temp .= '<tr><td colspan="10"><strong>下列角色处于交易中状态无法下架，直至买家取消付款或完成交易。</strong></td></tr>';
                        foreach ($dataTrading as $char) {
                            $password = $char['password'] ? $char['password'] : '无';
                            $temp .= '<tr>';
                            $temp .= '<td>'.$char['ID'].'</td>';
                            $temp .= '<td>'.$char['name'].'</td>';
                            $temp .= '<td>'.$password.'</td>';
                            $temp .= '<td>'.date("m-d H:i",$char['date']).'</td>';
                            $temp .= '<td>'.$char['price'].'.00</td>';
                            $temp .= '<td><button type="button" class="btn btn-secondary" disabled>交易中</button></td>';
                            $temp .= '</tr>';
                        }
                    }
                $temp .='</tbody>';
                $temp .='</table>';
                $temp .='</div>';

                $box = modalBox("我的寄售",$temp,700);

                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$box]));
                break;
            case "trading-offshelf":    //角色下架
                if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                if(!check_value($_POST['id'])) exit(json_encode(['code' => '20002', 'msg' => '非法操作！']));
                if(!Validator::UnsignedNumber($_POST['id'])) exit(json_encode(['code' => '20002', 'msg' => '非法操作！']));
                if(!$market->setCharSellOff($_POST['id'])) exit(json_encode(['code' => '20002', 'msg' => '操作失败，请联系在线客服！']));
                $message = '恭喜您，角色下架成功!';
                $temp.= '<script type="text/javascript">';
                $temp.=  '$(function () {';
                $temp.=  'modal_msg("'.$message.'");';
                $temp.=  'tradingChar.ajax.reload();';
                $temp.=  '});';
                $temp.=  '</script>';
                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                break;
			
					
            case "my-buy-log":
                if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                $data = $market->getMyMarketCharBuyLog($_SESSION['group'],$_SESSION['username']);
                $temp = '';
                $temp .='<div class="">';
                $temp .='<table class="mb-3 table table-striped table-bordered text-center text-dark">';
                $temp .='<thead>';
                $temp .='<tr>';
                $temp .='<td>单号</td>';
                $temp .='<td>角色名</td>';
                $temp .='<td>购买价格</td>';
                $temp .='<td>购买日期</td>';
                $temp .='</tr>';
                $temp .='</thead>';
                $temp .='<tbody>';
                if(is_array($data)){
                    foreach ($data as $char) {
                        $password = $char['password'] ? $char['password'] : '无';
                        $temp .= '<tr>';
                        $temp .= '<td>'.$char['ID'].'</td>';
                        $temp .= '<td>'.$char['name'].'</td>';
                        $temp .= '<td>'.$char['buy_price'].'.00</td>';
                        $temp .= '<td>'.date("m-d H:i",$char['buy_date']).'</td>';
                        $temp .= '</tr>';
                    }
                }else{
                    $temp .= '<tr><td colspan="10">暂无购买记录</td></tr>';
                }
                $temp .='</tbody>';
                $temp .='</table>';
                $temp .='</div>';

                $box = modalBox("购买记录",$temp,700);

                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$box]));
                break;
            case "my-sell-log":
                if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                $data = $market->getMyMarketCharList($_SESSION['group'],$_SESSION['username'],2);
                $temp = '';
                $temp .='<div class="">';
                $temp .='<table class="mb-3 table table-striped table-bordered text-center text-dark">';
                $temp .='<thead>';
                $temp .='<tr>';
                $temp .='<td>单号</td>';
                $temp .='<td>角色名</td>';
                $temp .='<td>发布日期</td>';
                $temp .='<td>售出时间</td>';
                $temp .='<td>售出价格</td>';
                $temp .='</tr>';
                $temp .='</thead>';
                $temp .='<tbody>';
                if(is_array($data)){
                    foreach ($data as $char) {
                        $password = $char['password'] ? $char['password'] : '无';
                        $temp .= '<tr>';
                        $temp .= '<td>'.$char['ID'].'</td>';
                        $temp .= '<td>'.$char['name'].'</td>';
                        $temp .= '<td>'.date("m-d H:i",$char['date']).'</td>';
                        $temp .= '<td>'.$char['price'].'.00</td>';
                        $temp .= '<td>'.date("m-d H:i",$char['buy_date']).'</td>';
                        $temp .= '</tr>';
                    }
                }else{
                    $temp .= '<tr><td colspan="10">暂无销售记录</td></tr>';
                }
                $temp .='</tbody>';
                $temp .='</table>';
                $temp .='</div>';

                $box = modalBox("销售记录",$temp,700);

                exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$box]));
                break;
//            case "close": //主动撤销订单
//                $data = $market->clearMarketOutTradeNo($_POST['id'],1);
//                $trading->cancel($getData[0]['out_trade_no']); //撤销支付宝订单
//                if($data) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('支付已取消!','warning','body');});</script>"]));
//                break;
            default:
                break;
        }

        //默认返回列表数据
        $list = $market->getMarketCharList();
        $data = [];
        $i = 1;
        if(is_array($list)){
        foreach ($list as $item) {
            #不显示自己寄售的角色
            $myCard = 0;
            if(isLoggedIn() && $_SESSION['group'] == $item['servercode'] && $_SESSION['username'] == $item['username']) $myCard = 1;
            $data[] = [
                "no" => $i,
                "uid" => $item['ID'],
                "avatar" => getPlayerClassAvatar($item['Class'], 1, 1, 'rounded', '35'),
                "class" => getPlayerClassName($item['Class']),
                "name" => $item['name'],
                "cLevel" => (int)$item['cLevel'],
                "mLevel" => (int)$item['mLevel'],
                "price" => "￥" . $item['price'] . ".00",
                "tencent" => $item['tencent'],
                "servercode" => getGroupNameForServerCode($item['servercode']),
                "date" => date("m-d H:i", $item['date']),
                "pass" => ($item['password']) ? 1 : 0,
                "my"   => $myCard,
            ];
            $i++;
        }
        }
        exit(json_encode(["code" => "10000", "msg" => "Success", "data" => $data]));
        break;
    case "/market/item":
            if (isset($_POST['id']) && $_POST['id']) {
                if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                if (!Validator::UnsignedNumber($_POST['id'])) exit(json_encode(['code' => '20002', 'msg' => '非法请求']));
                if (!check_value($_POST['modules']) || !isset($_POST['modules'])) exit;
            }
            $ItemConfig = $market->loadConfig('item');
            switch ($_POST['modules']){
                case "qrcocde":  //先进BUY  再生成qrcocde
				
                    if (!isLoggedIn()) exit(json_encode(["code" => "20000", "msg" => "请先登陆。"]));
					
					
                    $price = 0;
                    $trade_no = getUuid() ;
                    if(is_array($_POST['cart'])){
                        #购物车结算
                        for ($i = 0;$i<count($_POST['cart']);$i++){
                            $map = $i+1;
                            if (!$_POST['cart'][$i] || !Validator::UnsignedNumber($_POST['cart'][$i])) exit(json_encode(['code' => '20003', 'msg' => '第['.$map.']件数据异常，请重新选择物品加入购物。']));
                            $getData = $market->getMarketItemList($_POST['cart'][$i]);
                            if (!is_array($getData)) exit(json_encode(['code' => '20001', 'msg' => "第[".$map."]件物品已被购买，请选择其他物品。"]));
                            if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(['code' => '20002','msg'=> 'Invalid argument']));

                            #检测是否交易中
                            if($getData[0]['status'] > 0){
                                if(!$getData[0]['buy_date']) exit(json_encode(['code' => '20002', 'msg' => "第[".$map."]件数据异常，该物品暂时无法购买。"]));
                                if($_SESSION['username'] != $getData[0]['buy_username']){
                                    if(strtotime(logDate()) - strtotime($getData[0]['buy_date']) <= 60*$market->timeOut) exit(json_encode(['code' => '20003', 'msg' => "第[".$map."]件该物品正处于交易中或已被购买。"]));
                                }
                            }

                            #申请订单号
                            if(!$market->getMarketOutTradeNo($_POST['cart'][$i],$_SESSION['username'],$trade_no,0)) exit(json_encode(["code" => "20005", "msg" => "订单号申请失败，请稍后尝试或联系在线客服。"]));
							
							#更新购买者角色
							$market->update_buy_char($_POST['cart'][$i],$_POST['character_name']);
							
						    $price = $price + $getData[0]['price'];
                        }
                    }else{
                        #单个结算
                        $getData = $market->getMarketItemList($_POST['id']);
						
                        if (!is_array($getData)) exit(json_encode(['code' => '20003', 'msg' => '该物品已被购买，请选择其他物品。']));
                        if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(['code' => '20002','msg'=> 'Invalid argument']));
                    
                        #检测是否交易中
                        if($getData[0]['status'] > 0){
                            if(!$getData[0]['buy_date']) exit(json_encode(['code' => '20002', 'msg' => '数据异常，该物品暂时无法购买。']));
                            if($_SESSION['username'] != $getData[0]['buy_username']){
                                if(strtotime(logDate()) - strtotime($getData[0]['buy_date']) <= 60*$market->timeOut) exit(json_encode(['code' => '20003', 'msg' => '该物品正处于交易中或已被购买。']));
                            }
                        }
						
                        #申请订单号
                        if(!$market->getMarketOutTradeNo($_POST['id'],$_SESSION['username'],$trade_no)) exit(json_encode(["code" => "20004", "msg" =>  "订单号申请失败，请稍后尝试或联系在线客服。"]));
                        $price = $getData[0]['price'];
                    }
					

                    #申请二维码
					#修改成网站支付
                   $url = $trading->toWebPay("[九鼎奇迹]角色购买", $trade_no, $price);
                   if(is_array($url)) exit(json_encode(["code" => $url['code'], "msg" => $url['msg']]));
                   $content = $url;


                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$content]));
                    break;
                case "pass":
                    $getData = $market->getMarketItemList($_POST['id']);
                    if (!is_array($getData)) exit(json_encode(['code' => '20003', 'msg' => '该物品已被购买，请选择其他物品。']));
                    if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(['code' => '20002','msg'=> 'Invalid argument']));

                    $title = '['.$getData[0]['item_name'].']交易密码';
                    #内容
                    $content = '';
                    $content .= '<div class="">';
                    $content .= '<input type="text" id="password" class="form-control" name="password" autocomplete="off" autofocus="autofocus" placeholder="请输入交易密码">';
                    $content .= '<div class="modal-footer" style="justify-content:center">';
                    $content .= '<button type="button" class="btn btn-success" onClick="trading('.$_POST['id'].',\'buy\')">确定</button>';
                    $content .= '<button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>';
                    $content .= '</div>';
                    $content .= '</div>';
                    $temp = '<div class="modal fade" data-backdrop="static" id="trading-result" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content" style="border:unset">';

                    $temp .='<div class="modal-header">';
                    $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">'.$title.'</h5>';
                    $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $temp .='</div>';

                    $temp .=$content;

                    $temp .='</div></div></div>';

                    $temp .= '<script type="text/javascript">';
                    $temp .= "$('#trading-result').modal('show');";
                    $temp .= "$('#trading-result').on('shown.bs.modal', function () {";
                    $temp .= "$('#password').focus();";
                    $temp .= "});";
                    $temp .= "$('#trading-result').on('hidden.bs.modal', function () {";
                    $temp .= "$('#trading-result').remove();";
                    $temp .= "});";
                    $temp .= '</script>';
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
                case "buy":
                    if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                    $getData = $market->getMarketItemList($_POST['id']);
					
					
					$payStatus=$market->getPayStatus();
					if(!check_value($payStatus) || $payStatus==0){
					   $url = $trading->toWebPay("请支付1.00元验证支付宝",  getUuid(), 1,1);
					   exit(json_encode(['code' => '20008', 'msg' => '支付未绑定！','data'=>$url]));
					}
                    #验证是否为同一大区
                    if(getServerCodeForGroupID($_SESSION['group']) != $getData[0]['servercode']) exit(json_encode(['code' => '20002', 'msg' => '大区不符，请选择与您大区相符的物品。']));

                    if (!is_array($getData)) exit(json_encode(['code' => '20003', 'msg' => '该物品正处于交易中或已被购买。']));
                    if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(['code' => '20002','msg'=> 'Invalid argument']));

                    if($getData[0]['status'] > 0){
                        if(!$getData[0]['buy_date']) exit(json_encode(['code' => '20002', 'msg' => '数据异常，该物品暂时无法购买。']));
                        if($_SESSION['username'] != $getData[0]['buy_username']){
                            if(strtotime(logDate()) - strtotime($getData[0]['buy_date']) <= 60*$market->timeOut) exit(json_encode(['code' => '20003', 'msg' => '该物品正处于交易中或已被购买。']));
                        }
                    }
                    
					
                    //如果有密码取密码验证
                    if($getData[0]['password']){
                        if(!check_value($_POST['password'])) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('请输入密码!','danger');});</script>"]));
                        if(!Validator::UnsignedNumber($_POST['password'])) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('密码不正确,请重新输入!','danger');});</script>"]));
                        if($_POST['password'] !== $getData[0]['password']) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('密码错误,请重新输入!','danger');});</script>"]));
                    }

					 
                    #检测仓库是否正常
                    $warehouse = new \Plugin\Market\warehouse($_SESSION['group'],$_SESSION['username']);

                    #确定标题
                    $title = $getData[0]['item_name'].'['.$getData[0]['price'].'.00]';

                    $temp = '<div class="modal fade" data-backdrop="static" id="trading-buy" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content" style="border:unset">';

                    $temp .='<div class="modal-header">';
                    $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">'.$title.'</h5>';
                    #onclick="trading('.$_POST['id'].',\'close\')"
                    $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $temp .='</div>';

                    $temp .= '<div  id="qr-code-content" class="bg-primary">';

                    $temp .= '<div style="height: 247px;" class="d-flex justify-content-center align-items-center"><div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span></div></div>';

                    $temp .= '</div>';
                    $temp .= '<div id="qr-code-footer" class="modal-footer text-center" style="justify-content:center">';
                    $temp .= '<div class="text-danger">请确保您仓库中有足够位置存放该物品</div>';
                    $temp .= '</div>';
                    $temp .='</div></div></div>';

                    $temp .= '<script type="text/javascript">';
                    $temp .= "$('#trading-buy').modal('show');";
                    $temp .= "$('#trading-buy').on('shown.bs.modal', function () {";
                    $temp .= "$('#message').modal('hide');";
                    $temp .= "getQrCode(".$_POST['id'].");";
                    $temp .= "getResult(".$_POST['id'].");";
                    $temp .= "});";
                    $temp .= "$('#trading-buy').on('hidden.bs.modal', function () {";
                    $temp .= "$('#trading-buy').remove();";
                    $temp .= "});";
                    $temp .= '</script>';
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
			   //返回角色列表
			   case "getChar":
			       $character = new Character();
                   $AccountCharacters = $character->getAccountCharacterNameForAccount($_SESSION["group"],$_SESSION["username"]);
				   $retlist=array();
					
					
					if(is_array($AccountCharacters)) {
                                    foreach ($AccountCharacters as $key => $characterName) {
                                        if (empty($characterName)) continue;
                                        if (!$character->checkCharacterIsBan($_SESSION['group'],$characterName)) continue;
										$retlist[count($retlist)]=array("key"=>$key,"value"=>$characterName);
									}
                     }else{
                                    $retlist[count($retlist)]=array("key"=>"","value"=>"暂无角色");
					 }
					 
					exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>array_values($retlist)]));
                    break;
			   case "saveChar":
                    
					$_SESSION["character_name"]=$_POST["character_name"];
					if(!check_value($_SESSION["character_name"])) {
					exit(json_encode(["code"=>"10001","msg"=>"Success","data"=>$_SESSION["character_name"]]));
					}else{
					json_encode(["code"=>"10000","msg"=>"False","data"=>$_SESSION["character_name"]]);
					}
					
					
                    break;	
					
				//返回服务器信息
				case "serverInfo":
                    global $serverGrouping;
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$serverGrouping]));
                    break;
				case "itemNameGroup":
				
					$drop = new \Plugin\drop();
					$tConfig = $drop->loadConfig();
					if(!$tConfig['active']) exit(json_encode(['code' => '20001','msg'=> 'none',"data" => []]));
					//连接本地的 Redis 服务
					$redis = new Redis();
					$redis->connect($tConfig['redis_ip'], $tConfig['redis_port']);
					
					if($tConfig['redis_pass']) $redis->auth($tConfig['redis_pass']);#密码验证
					#获取列表
					$liststr = $redis->get('itemList');
					$arList = $redis->keys("*");
					if(!$liststr){
						$list=$market->getMarketItemGroup();
						$redis->set('itemList',json_encode($list));
						$redis->expire('itemList',3600);
						exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$list]));
					}else{
						exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>json_decode($liststr)]));
						
					}
					break;	
					
				case "serial":
                    $temp = '';
                    $temp = '<div data-toggle="tooltip" data-placement="right" title="该道具['.$_POST['id'].']由玩家 [name] 于 [date] 从 [map][X][Y] 获得">'.$_POST['id'].'</div>';
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
                case "sell":
                    if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
					
					$payStatus=$market->getPayStatus();
					if(!check_value($payStatus) || $payStatus==0){
					$url = $trading->toWebPay("请支付1.00元验证支付宝",  getUuid(), 1,1);
					exit(json_encode(['code' => '20008', 'msg' => '支付未绑定！','data'=>$url]));
					}
                    //验证账号是否绑定支付宝
                    if(!$trading->checkBindAliPay()){
                        $temp .='<div class="modal fade" data-backdrop="static" id="bind-alipay" tabindex="-1">';
                        $temp .='<div class="modal-dialog  modal-dialog-centered">';
                        $temp .='<div class="modal-content" style="border:unset">';
                        $temp .='<div class="modal-header">';
                        $temp .= '<h5 class="modal-title" id="staticBackdropLabel">提示</h5>';
                        $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                        $temp .= '<span aria-hidden="true">&times;</span>';
                        $temp .= '</button>';
                        $temp .='</div>';
                        $temp .='<div class="modal-body text-center align-self-center">';
                        $temp .= '<div class="alert alert-warning" role="alert">检测到您当前账号未绑定支付宝</div>';
                        $temp .='<div class="alert alert-warning" role="alert">点击下面按钮前往支付宝授权绑定。</div>';
                        $temp .= $trading->Identification();
                        $temp .= '</div>';
                        $temp .='</div>';
                        $temp .='</div>';
                        $temp .='</div>';
                        $temp .= '<script type="text/javascript">';
                        $temp .= "$('#bind-alipay').modal('show');";
                        $temp .= "$('#bind-alipay').on('hidden.bs.modal', function () {";
                        $temp .= "$('#bind-alipay').remove();";
                        $temp .= "});";
                        $temp .= '</script>';
                        exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    }
                    $warehouse = new \Plugin\Market\warehouse($_SESSION['group']);
                    $item = $warehouse->_getItemLength();
                    $temp = '<div class="modal fade" id="trading-sell" tabindex="-1">';
                        $temp .='<div class="modal-dialog modal-sm modal-dialog-centered" style="width: 300px">';
                            $temp .='<div class="modal-content" style="border:unset;background: url('.__PATH_PUBLIC_IMG__.'warehouse/warehouse.png) no-repeat;width: 300px;height:590px; margin: 0 auto;">';

                            $temp .='<div class="modal-header" style="border-bottom:unset;margin-top:20px;padding: 10px 25px 10px 110px;">';
                                        $temp .= '<div class="modal-title" id="staticBackdropLabel" style="overflow:hidden;font-size:18px;color:#FFA500;">默认仓库</div>';
                                        $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                                        $temp .= '<span aria-hidden="true">&times;</span>';
                                        $temp .= '</button>';
                                $temp .='</div>';

                                $temp .='<div class="modal-body">';
                                    $temp .='<div class="text-center row justify-content-md-center" style="">';

                                        $temp .='<div class="" style="">';
                                            $temp.= '<div style="width: 224px;height: 420px;margin-top: 28px;position: relative;">';
                                            if(is_array($item)) {
                                                for ($i = 0; $i < 120; $i++) {
                                                    if (isset($item[$i]['url'])) {
                                                        $temp.='<div style="position: absolute;top:'.($item[$i]['y'] * 28).'px;left:'.($item[$i]['x'] * 28).'px;">';
                                                        $temp.='<div style="position: absolute;background: url('. __PATH_PUBLIC_IMG__ .'warehouse/VA.jpg) repeat;width:'.($item[$i]['width'] * 28).'px;height:'.($item[$i]['height'] * 28).'px;">';
                                                        $temp.='<div id="item-'.$i.'" onclick="trading(\''.$i.'\',\'item-select\')"><div class="data-info" data-info="'.$item[$i]['code'].'" style="width:'.($item[$i]['width'] * 28).'px;height:'.($item[$i]['height'] * 28).'px;background: url('. $item[$i]['url'].') center;background-size: cover;"></div></div>';
                                                        $temp.='</div>';
                                                        $temp.='</div>';
                                                    }
                                                }
                                            }
                                            $temp.= '</div>';
                                        $temp.= '</div>';

                                    $temp .='</div>';

                                    $temp .='<div class="text-center text-danger" style="margin-top: 16px;font-size: 13px">点击物品可以进行寄售操作</div>';
                                $temp .='</div>';

                            $temp .='</div>';
                        $temp .='</div>';
                    $temp .='</div>';

                    $temp .= '<script type="text/javascript">';
                    $temp .= "$('#trading-sell').modal('show');";
                    $temp .= "$('#trading-sell').on('hidden.bs.modal', function () {";
                    $temp .= "$('#trading-sell').remove();";
                    $temp .= "});";
                    $temp .= "$(function() {";
                    $temp .= '$("[data-toggle=\'tooltip\']").tooltip();';
                    $temp .= "altTooltip.init();";
                    $temp .= "});";
                    $temp .= '</script>';

                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
                case "item-select":
                    $warehouse = new \Plugin\Market\warehouse($_SESSION['group']);
                    $data = $warehouse->getWarehouseForMap($_POST['id']);
                    if(!is_array($data))  exit(json_encode(['code' => '20002', 'msg' => '物品获取失败，请联系在线客服。']));
                    $character = new Character();

                    $AccountCharacters = $character->getAccountCharacterNameForAccount($_SESSION["group"],$_SESSION["username"]);
                    $temp = '<div class="modal fade" id="trading-select" tabindex="-1">';
                    $temp .='<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:500px">';
                    $temp .='<div class="modal-content" style="border:unset;">';
                    $temp .='<div class="modal-header">';
                    $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">物品寄售</h5>';
                    $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
                    $temp .= '<span aria-hidden="true">&times;</span>';
                    $temp .= '</button>';
                    $temp .='</div>';

                    $temp .='<div class="modal-body">';
                    $temp .='<div>';
                    $temp .= '<div class="text-center"><p class="alert alert-primary">一旦售出成功将收取总金额的[<strong>'.$ItemConfig['price_rate'].'%</strong>]服务费</p></div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                    $temp .='<label for="item" class="col-sm-3 col-form-label text-right">寄售物品</label>';
                    $temp .='<div class="col-sm-6">';
                    $temp .='<select name="item" id="item" class="form-control">';
                    $temp .= '<option value="'. $_POST['id'].'">'.$data['name'].'</option>';
                    $temp .='</select>';
                    $temp .='</div>';
                    $temp .='<div class="col-sm-3 col-form-label"></div>';
                    $temp .='</div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                    $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">选择角色</label>';
                    $temp .='<div class="col-sm-6">';
                        $temp .='<select name="char_name" id="char_name" class="form-control">';
                        if(is_array($AccountCharacters)) {
                            foreach ($AccountCharacters as $key => $characterName) {
                                if (empty($characterName)) continue;
                                if (!$character->checkCharacterIsBan($_SESSION['group'],$characterName)) continue;
                                $temp .= '<option value="'. $key.'">'.$characterName.'</option>';
                            }
                        }else{
                            $temp .= '<option value="">暂无角色</option>';
                        }

                        $temp .='</select>';
                    $temp .='</div>';
                    $temp .='<small class="col-sm-3 form-inline text-muted text-left">*显示作用</small>';
                    $temp .='</div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                    $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">交易密码</label>';
                    $temp .='<div class="col-sm-6">';
                    $temp .='<input type="text" name="password" id="password" class="form-control" AUTOCOMPLETE="OFF" placeholder="可不填" />';
                    $temp .='</div>';
                    $temp .='<small class="col-sm-3 form-inline text-muted text-left">*买家购买所需</small>';
                    $temp .='</div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                    $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">寄售价格</label>';
                    $temp .='<div class="col-sm-6">';
                    $temp .='<input type="text" name="price" id="price" class="form-control" AUTOCOMPLETE="OFF" placeholder="单位(元)"/>';
                    $temp .='</div>';
                    $temp .='<small class="col-sm-3 form-inline text-muted text-left"></small>';
                    $temp .='</div>';

                    $temp .='<div class="form-group row justify-content-md-center">';
                    $temp .='<label for="char_name" class="col-sm-3 col-form-label text-right">联系QQ</label>';
                    $temp .='<div class="col-sm-6">';
                    $temp .='<input type="text" name="tencent" id="tencent" class="form-control" placeholder="显示作用" required/>';
                    $temp .='</div>';
                    $temp .='<small class="col-sm-3 form-inline text-muted text-left"></small>';
                    $temp .='</div>';


                    $temp .='<div class="form-group row justify-content-md-center">';
                    $temp .='<button type="button" id="btn-sell" onclick="market_sell(\'sell-item\')" class="btn btn-success col-sm-4">确定</button>';
                    $temp .='</div>';

                    $temp .='</div>';

                    $temp .='</div>';
                    $temp .='</div>';
                    $temp .='</div>';

                    $temp .= '<script type="text/javascript">';
                    $temp .= "$('#trading-select').modal('show');";
                    $temp .= "$('#trading-select').on('hidden.bs.modal', function () {";
                    $temp .= "$('#trading-select').remove();";
                    $temp .= "});";
                    $temp .= '</script>';
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
                case "selling":
                    if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                    if(!check_value($_POST['char'])) exit(json_encode(['code' => '20004', 'msg' => '角色不能为空！']));
                    if(!Validator::AlphaNumeric($_POST['char'])) exit(json_encode(['code' => '20004', 'msg' => '角色不能为空！']));
                    $password = null;
                    if (check_value($_POST['pass'])){
                        if(!Validator::UnsignedNumber($_POST['pass'])) exit(json_encode(['code' => '20004', 'msg' => '[1] 非法操作！']));
                        $password = $_POST['pass'];
                    }
                    if(!check_value($_POST['price']) && !Validator::Number($_POST['price'],5,1)) exit(json_encode(['code' => '20004', 'msg' => '[2]非法操作！']));
                    if(!check_value($_POST['tencent']) && !Validator::Number($_POST['tencent'],13,5)) exit(json_encode(['code' => '20004', 'msg' => '[3]非法操作！']));
                    #检查账号是否在线
					
					
					
					
                    $common = new common();
                    if($common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) exit(json_encode(['code' => '20005', 'msg' => '检测到您当前账号在线，请先退出游戏。']));

                    $warehouse = new \Plugin\Market\warehouse($_SESSION['group']);
                    if(!is_array($warehouse->warehouseList)) exit(json_encode(['code' => '20004', 'msg' => '[1]非法操作！']));
                    if(!array_key_exists($_POST['item'],$warehouse->warehouseList)) exit(json_encode(['code' => '20004', 'msg' => '[1]非法操作！']));

                    if (!$market->setItemSell($_POST['char'],$warehouse->warehouseList[$_POST['item']],$password,$_POST['tencent'],$_POST['price'])) exit(json_encode(['code' => '20004', 'msg' => '[3]非法操作！']));
                    $message = '恭喜您，角色寄售成功!';
                    $temp.= '<script type="text/javascript">';
                    $temp.=  '$(function () {';
                    $temp.=  'modal_msg("'.$message.'");';
                    $temp.=  '});';
                    $temp.=  '</script>';
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
                case "my-trading":
                    if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));

                    $temp = '';
                    $temp .='<div class="" style="overflow-y:auto">';
                    $temp .='<table class="mb-3 table table-striped table-bordered text-center text-dark">';
                    $temp .='<thead>';
                    $temp .='<tr>';
                    $temp .='<td>单号</td>';
                    $temp .='<td>物品</td>';
                    $temp .='<td>物品名称</td>';
                    $temp .='<td>物品编号</td>';
                    $temp .='<td>类型</td>';
                    $temp .='<td>交易密码</td>';
                    $temp .='<td>发布日期</td>';
                    $temp .='<td>售价</td>';
                    $temp .='<td>操作</td>';
                    $temp .='</tr>';
                    $temp .='</thead>';
                    $temp .='<tbody>';
                    #未交易中
                    $data1 = $market->getMyMarketItemList($_SESSION['group'],$_SESSION['username']);
                    if(is_array($data1)){
                        $equipment = new \Plugin\equipment();
                        foreach ($data1 as $char) {
                            $item = $equipment->convertItem($char['item_code']);
                            $itemName = $equipment->getItemName($item['section'],$item['index'],$item['level']);
                            $img = $equipment->ItemsUrl($char['item_code']);
                            $password = $char['password'] ? $char['password'] : '无';
                            $temp .= '<tr>';
                            $temp .= '<td>'.$char['ID'].'</td>';
                            $temp .= '<td><div class="data-info" data-info="'.$char['item_code'].'"><img src="'.$img.'" alt="" width="35" height="35"/></div></td>';
                            $temp .= '<td>'.$itemName.'</td>';
                            $temp .= '<td>'.($item['serial1']).'</td>';
                            $temp .= '<td>'.$char['item_type'].'</td>';
                            $temp .= '<td>'.$password.'</td>';
                            $temp .= '<td>'.date("m-d H:i",$char['date']).'</td>';
                            $temp .= '<td>'.$char['price'].'.00</td>';
                            $temp .= '<td><button type="button" class="btn btn-warning" onclick="trading('.$char['ID'].', \'trading-offshelf\')">下架</button></td>';
                            $temp .= '</tr>';
                        }
                    }else{
                        $temp .= '<tr><td colspan="10">暂无寄售数据</td></tr>';
                    }
                    #交易中
                    $data2 = $market->getMyMarketItemList($_SESSION['group'],$_SESSION['username'],1);
                    if(is_array($data2)){
                        $temp .= '<tr><td colspan="10"><strong>下列物品处于交易中状态无法下架，直至买家取消付款或完成交易。</strong></td></tr>';
                        $equipment = new \Plugin\equipment();
                        foreach ($data2 as $char) {
                            $item = $equipment->convertItem($char['item_code']);
                            $itemName = $equipment->getItemName($item['section'],$item['index'],$item['level']);
                            $img = $equipment->ItemsUrl($char['item_code']);
                            $password = $char['password'] ? $char['password'] : '无';
                            $temp .= '<tr>';
                            $temp .= '<td>'.$char['ID'].'</td>';
                            $temp .= '<td><div class="data-info" data-info="'.$char['item_code'].'"><img src="'.$img.'" alt="" width="35" height="35"/></div></td>';
                            $temp .= '<td>'.$itemName.'</td>';
                            $temp .= '<td>'.($item['serial1']).'</td>';
                            $temp .= '<td>'.$char['item_type'].'</td>';
                            $temp .= '<td>'.$password.'</td>';
                            $temp .= '<td>'.date("m-d H:i",$char['date']).'</td>';
                            $temp .= '<td>'.$char['price'].'.00</td>';
                            $temp .= '<td><button type="button" class="btn btn-secondary" disabled>交易中</button></td>';
                            $temp .= '</tr>';
                        }
                    }
                    $temp .='</tbody>';
                    $temp .='</table>';
                    $temp .='</div>';

                    $box = modalBox("我的寄售",$temp,1000,'altTooltip.init();');

                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$box]));
                    break;
                case "trading-offshelf":    //角色下架
                    if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                    $getData = $market->getMarketItemList($_POST['id']);
                    if (!is_array($getData)) exit(json_encode(['code' => '20003', 'msg' => '该物品正处于交易中或已被购买。']));
                    if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(['code' => '20002','msg'=> 'Invalid argument']));
                    if(!$market->setItemSellOff($_POST['id'])) exit(json_encode(['code' => '20002', 'msg' => '操作失败，请联系在线客服！']));
                    $message = '恭喜您，物品下架成功!';
                    $temp.= '<script type="text/javascript">';
                    $temp.=  '$(function () {';
                    $temp.=  'modal_msg("'.$message.'");';
                    $temp.=  'tradingItem.ajax.reload();';
                    $temp.=  '});';
                    $temp.=  '</script>';
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
                case "my-buy-log":
                    if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                    $data = $market->getMyMarketItemBuyLog($_SESSION['group'],$_SESSION['username']);
                    $temp = '';
                    $temp .='<div class="">';
                    $temp .='<table class="mb-3 table table-striped table-bordered text-center text-dark">';
                    $temp .='<thead>';
                    $temp .='<tr>';
                    $temp .='<td>单号</td>';
                    $temp .='<td>物品</td>';
                    $temp .='<td>物品名称</td>';
                    $temp .='<td>物品编号</td>';
                    $temp .='<td>类型</td>';
                    $temp .='<td>购买日期</td>';
                    $temp .='<td>购买价格</td>';
                    $temp .='</tr>';
                    $temp .='</thead>';
                    $temp .='<tbody>';
                    if(is_array($data)){
                        $equipment = new \Plugin\equipment();
                        foreach ($data as $char) {
                            $item = $equipment->convertItem($char['item_code']);
                            $itemName = $equipment->getItemName($item['section'],$item['index'],$item['level']);
                            $img = $equipment->ItemsUrl($char['item_code']);
                            $password = $char['password'] ? $char['password'] : '无';
                            $temp .= '<tr>';
                            $temp .= '<td>'.$char['ID'].'</td>';
                            $temp .= '<td><img src="'.$img.'" alt="" width="35" height="35"/></td>';
                            $temp .= '<td>'.$itemName.'</td>';
                            $temp .= '<td>'.($item['serial1']).'</td>';
                            $temp .= '<td>'.$char['item_type'].'</td>';
                            $temp .= '<td>'.date("m-d H:i",$char['buy_date']).'</td>';
                            $temp .= '<td>'.$char['price'].'.00</td>';
                            $temp .= '</tr>';
                        }
                    }else{
                        $temp .= '<tr><td colspan="10">暂无购买记录</td></tr>';
                    }
                    $temp .='</tbody>';
                    $temp .='</table>';
                    $temp .='</div>';

                    $box = modalBox("购买记录",$temp,800);

                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$box]));
                    break;
                case "my-sell-log":
                    if (!isLoggedIn()) exit(json_encode(['code' => '20002', 'msg' => '请先登陆！']));
                    $data = $market->getMyMarketItemList($_SESSION['group'],$_SESSION['username'],2);
                    $temp = '';
                    $temp .='<div class="">';
                    $temp .='<table class="mb-3 table table-striped table-bordered text-center text-dark">';
                    $temp .='<thead>';
                    $temp .='<tr>';
                    $temp .='<td>单号</td>';
                    $temp .='<td>物品</td>';
                    $temp .='<td>物品名称</td>';
                    $temp .='<td>物品编号</td>';
                    $temp .='<td>类型</td>';
                    $temp .='<td>售出日期</td>';
                    $temp .='<td>售出价格</td>';
                    $temp .='</tr>';
                    $temp .='</thead>';
                    $temp .='<tbody>';
                    if(is_array($data)){
                        $equipment = new \Plugin\equipment();
                        foreach ($data as $char) {
                            $item = $equipment->convertItem($char['item_code']);
                            $itemName = $equipment->getItemName($item['section'],$item['index'],$item['level']);
                            $img = $equipment->ItemsUrl($char['item_code']);
                            $password = $char['password'] ? $char['password'] : '无';
                            $temp .= '<tr>';
                            $temp .= '<td>'.$char['ID'].'</td>';
                            $temp .= '<td><img src="'.$img.'" alt="" width="35" height="35"/></td>';
                            $temp .= '<td>'.$itemName.'</td>';
                            $temp .= '<td>'.($item['serial1']).'</td>';
                            $temp .= '<td>'.$char['item_type'].'</td>';
                            $temp .= '<td>'.date("m-d H:i",$char['buy_date']).'</td>';
                            $temp .= '<td>'.$char['price'].'.00</td>';
                            $temp .= '</tr>';
                        }
                    }else{
                        $temp .= '<tr><td colspan="10">暂无销售记录</td></tr>';
                    }
                    $temp .='</tbody>';
                    $temp .='</table>';
                    $temp .='</div>';

                    $box = modalBox("销售记录",$temp,800);

                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$box]));
                    break;
                case "cart-clear":
                    if($_POST['id']) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('数据异常，请重新操作。','danger');});</script>"]));
                    if(!is_array($_POST['cart'])) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('数据异常，请重新操作。','danger');});</script>"]));
                   /*
				   if(count($_POST['cart']) < 2) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('使用购物车购买道具数量必须2件起。','danger');});</script>"]));
					*/
                    $price = 0;
                    for ($i=0;$i<count($_POST['cart']);$i++){
                        $map = $i+1;
                        $getData = $market->getMarketItemList($_POST['cart'][$i]);
                        #验证是否有密码，如果有就是非法提交
                        if($getData[0]['password']) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('请勿非法操作，已记录您的信息。','danger');});</script>"]));
                        #验证是否为同一大区
                        if(getServerCodeForGroupID($_SESSION['group']) != $getData[0]['servercode']) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('您所购买的第[".($map)."]道具与您的大区不符。','danger');});</script>"]));
                        if (!is_array($getData)) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('您所购买的第[".$map."]件物品已被购买，请选择其他道具。','danger');});</script>"]));
                        if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('Invalid argument','danger');});</script>"]));
                        #检测是否交易中
                        if($getData[0]['status'] > 0){
                            if(!$getData[0]['buy_date']) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('您所购买的第[".$map."]件物品数据异常，该物品暂时无法购买。','danger');});</script>"]));
                            if(getServerCodeForGroupID($_SESSION['group']) != $getData[0]['servercode'] || $_SESSION['username'] != $getData[0]['buy_username']){
                                if(strtotime(logDate()) - strtotime($getData[0]['buy_date']) <= 60*$market->timeOut) exit(json_encode(["code" => "10000", "msg" => "error" , "data" => "<script type='text/javascript'>$(function() {commonUtil.message('您所购买的第[".$map."]件物品正处于交易中或已被购买。','danger');});</script>"]));
                            }
                        }
                        $price = $price + $getData[0]['price'];
                    }
                    #检测仓库是否正常
                    $warehouse = new \Plugin\Market\warehouse($_SESSION['group'],$_SESSION['username']);

                    $temp = '<div class="modal fade" data-backdrop="static" id="trading-buy" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content" style="border:unset">';

                    $temp .='<div class="modal-header">';
                    $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">结算(共'.count($_POST["cart"]).'件)['.$price.'.00]</h5>';
                    $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $temp .='</div>';

                    $temp .= '<div  id="qr-code-content" class="bg-primary">';

                    $temp .= '<div style="height: 247px;" class="d-flex justify-content-center align-items-center"><div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span></div></div>';

                    $temp .= '</div>';
                    $temp .= '<div id="qr-code-footer" class="modal-footer text-center" style="justify-content:center">';
                    $temp .= '<div class="text-danger">购买多件道具请清空仓库顶部[8*8]位置</div>';
                    $temp .= '</div>';
                    $temp .='</div></div></div>';

                    $temp .= '<script type="text/javascript">';
                    $temp .= "$('#trading-buy').modal('show');";
                    $temp .= "$('#trading-buy').on('shown.bs.modal', function () {";
                        $temp .= "$('#cart').modal('hide');";
                        $temp .= "getQrCode(0,".json_encode($_POST['cart']).");";
                        $temp .= "getResult(0,".json_encode($_POST['cart']).");";
                    $temp .= "});";
                    $temp .= "$('#trading-buy').on('hidden.bs.modal', function () {";
                        $temp .= "$('#trading-buy').remove();";
                    $temp .= "});";
                    $temp .= '</script>';
                    exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$temp]));
                    break;
//                case "close":
//                    $getData = $market->getMarketItemList($_POST['id']);
//                    if(!is_array($getData)) exit(json_encode(['code' => '20003', 'msg' => '该物品已被购买，请选择其他道具。']));
//                    if(!Validator::UnsignedNumber($getData[0]['price'])) exit(json_encode(['code' => '20002','msg'=> 'Invalid argument']));
//
//                    $data = $market->clearMarketOutTradeNo($_POST['id']);
//                    $trading->cancel($getData[0]['out_trade_no']); //撤销支付宝订单
//                    if($data) exit(json_encode(["code" => "10000", "msg" => "Success","data" => "<script type='text/javascript'>$(function() {commonUtil.message('支付已取消!','warning','body');});</script>"]));
//                    break;
                default:
                    break;
            }

		
		
		
			
		
		//,$level='',$ext='',$lucky='',$set='',$skill='',$pos=''
		
        //默认返回列表数据
        $list = $market->getMarketItemList();
		
		
		
		
		
        $data = [];
        $e = new Plugin\equipment();
        $i = 1;
        if(is_array($list)){
			 foreach ($list as $key=>$item) {
                #不显示自己寄售的角色
                $myCard = 0;
                if(isLoggedIn() && $_SESSION['group'] == $item['servercode'] && $_SESSION['username'] == $item['username']) $myCard = 1;
                $equipment = new \Plugin\equipment();
                $items = $equipment->convertItem($item['item_code']);
                if($equipment->IsJewel){ $htmlName = "<div class='Custom3'>".$item['item_name']."</div>";}
                else if($equipment->IsSocket){ $htmlName = "<div class='SocketItemName'>".$item['item_name']."</div>";}
                else if($equipment->IsSet){ $htmlName = "<div class='MasteryBonusText'>".$item['item_name']."</div>";}
                else if($items['section'] == 12 && ($items['index'] >= 60 || $items['index'] <= 129)){ $htmlName = "<div class='SocketOption'>".$item['item_name']."</div>";} //萤石
                else if($equipment->IsExc){ $htmlName = "<div class='ExcItemName'>".$item['item_name']."</div>";}
                else if($items['lucky']){ $htmlName = "<div class='LuckyItem'>".$item['item_name']."</div>";}
                else{$htmlName = "<div class='text-white'>".$item['item_name']."</div>";}
               /*
			   $data[] = [
                    "no"        => $i,
                    "uid"       => $item['ID'],
                    "name"      => $item['name'],
                    "item_img"  => $e->ItemsUrl($item['item_code']),
                    "item_code" => $item['item_code'],
                    "item_type" => $market->getItemCategory($item['item_type']),
                    "item_name" => $item['item_name'],
                    "html_item_name" => $htmlName,
                    "price"     => "￥" . $item['price'] . ".00",
                    "tencent"   => $item['tencent'],
                    "servercode" => getGroupNameForServerCode($item['servercode']),
                    "date"      => date("m-d H:i", $item['date']),
                    "serial"    => $item['serial'],
                    "pass"      => ($item['password']) ? 1 : 0,
                    "my"        =>  $myCard,
                ];
				*/
				   $list[$key]["no"]  =$i;
				   $list[$key]["uid"]=$item['ID'];
					 
                    $list[$key]["item_img"]=$e->ItemsUrl($item['item_code']);
                   // "item_img"  => $e->ItemsUrl($item['item_code']),
				    $list[$key]["item_type"]=$market->getItemCategory($item['item_type']);
				    $list[$key]["html_item_name"]=$htmlName;
                    $list[$key]["price"]="￥" . $item['price'] . ".00";
                    $list[$key]["servercode"]=getGroupNameForServerCode($item['servercode']);
                    $list[$key]["date"]=date("m-d H:i", $item['date']);
					
                    $list[$key]["pass"]=($item['password']) ? 1 : 0;
                    $list[$key]["my"]=$myCard;
                    
				
				
                $i++;
            }
        }
        exit(json_encode(["code" => "10000", "msg" => "Success", "data" => array_values($list)]));
        break;
    default:
        exit(json_encode(['code' => '20001','msg'=> 'Invalid argument','data'=>[]]));
        break;
}

}catch (Exception $e){
    exit(json_encode(["code" => "40004", "msg" => $e->getMessage()]));
}

function modalBox($title,$content,$width = 500,$script = ''){
    $uid = uniqid();
    $temp = '<div class="modal fade" id="trading-'.$uid.'" tabindex="-1">';
    $temp .='<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:'.$width.'px">';
    $temp .='<div class="modal-content" style="border:unset;">';
    $temp .='<div class="modal-header">';
    $temp .= '<h5 class="modal-title" id="staticBackdropLabel" style="overflow:hidden">'.$title.'</h5>';
    $temp .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
    $temp .= '<span aria-hidden="true">&times;</span>';
    $temp .= '</button>';
    $temp .='</div>';

    $temp .=$content;
    $temp .='</div>';
    $temp .='</div>';
    $temp .='</div>';

    $temp .= '<script type="text/javascript">';
    $temp .= "$('.modal').modal('hide');";
    $temp .= "$('#trading-".$uid."').modal('show');";
    $temp .= "$('#trading-".$uid."').on('hidden.bs.modal', function () {";
    $temp .= "$('#trading-".$uid."').remove();";
    $temp .= "});";
    $temp .= "$(function() {";
    $temp .= '$("[data-toggle=\'tooltip\']").tooltip();';
    $temp .= $script;
    $temp .= "});";
    $temp .= '</script>';
    return $temp;
}
?>