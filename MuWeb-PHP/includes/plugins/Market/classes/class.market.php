<?php
/**
 * 交易市场类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin\Market;

use Exception;
use Plugin\equipment;
use trading;
use Validator;
use Connection;
use Character;
use common;

class Market{
    /************************ 插件必备函数 ************************/
	private $_modulesPath = 'modules';
    /************************ 插件必备函数 ************************/
    private $serverFiles;
    private $_MarketMenu;
    private $WAREHOUSE_LENGTH = 7680;
    private $NULL;
    private $equipment,$character;
    private $Web,$common;
    private $_ItemMarketConfig,$_CharMarketConfig;
    public $itemType= [
            1 =>'武器',
            2 =>'防具/装备',
            3 =>'翅膀',
            4 =>'首饰',
            5 =>'材料',
            6 =>'宝石',
            7 =>'荧石',
            8 =>'宠物/坐骑',
            0 =>'其他',
    ];

    public $timeOut = "2"; //订单超时时间

    /**
     * 构造函数
     * Market constructor.
     * @throws Exception
     */
	public function __construct()
    {
        #服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        #仓库BUFF长度
        if($this->serverFiles =="igcn") $this->WAREHOUSE_LENGTH = $this->WAREHOUSE_LENGTH*2;
        #空物品值
        $this->NULL = str_pad("F",ITEM_SIZE,"F");

        $this->_ItemMarketConfig = $this->loadConfig('item');
        $this->_CharMarketConfig = $this->loadConfig('char');

        $this->equipment = new equipment();
        $this->character = new Character();
        $this->common = new common();
        $this->Web = Connection::Database('Web');

        #导航菜单
        $this->_MarketMenu = [
            #名称,#地址,#状态,#[服务器类型]
            ["角色市场",'character',$this->_CharMarketConfig['active']],
            ["物品市场",'item',$this->_ItemMarketConfig['active']],
        ];

    }

    /**
     * 寄售角色
     * @param $nameID
     * @param $price
     * @param $password
     * @param $tencent
     * @return true
     * @throws Exception
     */
    public function setCharSell($nameID,$price,$password,$tencent)
    {
        if(!check_value($_SESSION['group'])) throw new Exception("[1]发布失败，请重新登陆！");
        if(!check_value($_SESSION['username'])) throw new Exception("[2]发布失败，请重新登陆！");
        if(!check_value($nameID)) throw new Exception("[3]发布失败，请重新登陆！");
        if(!Validator::AlphaNumeric($nameID)) throw new Exception("[4]发布失败，请重新登陆！");
        if(check_value($password)){
            if(!Validator::UnsignedNumber($password)) throw new Exception("[5]发布失败，请重新登陆！");
        }
        if(!check_value($tencent)) throw new Exception("[6]发布失败，请重新登陆！");
        if(!Validator::UnsignedNumber($tencent)) throw new Exception("[7]发布失败，请重新登陆！");
        if(!check_value($price)) throw new Exception("[8]发布失败，请重新登陆！");

        $AccountCharacter = $this->character->getAccountCharacterNameForAccount($_SESSION["group"],$_SESSION["username"]);
        if(!isset($AccountCharacter[$nameID]) || empty($AccountCharacter[$nameID])) throw new Exception("角色识别失败,请联系在线客服.");
        $name = $AccountCharacter[$nameID];
        if($price < $this->_CharMarketConfig['min_price'] || $price > $this->_CharMarketConfig['max_price']) throw new Exception("发布失败，售价必须在[".$this->_CharMarketConfig['min_price']."~".$this->_CharMarketConfig['max_price'].']之间!');

        if($this->checkSellChar($name) >= $this->_CharMarketConfig['frequency']) throw new Exception("该角色已经超过出售次数，无法再次出售!");
        if(!$this->character->checkCharacterIsBan($_SESSION['group'],$name)) throw new Exception("该角色暂时无法寄售！");

        $muonline = Connection::Database("MuOnline",$_SESSION['group']);

        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [Character] SET [CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[1,$_SESSION['username'],$name]);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_CHAR] ([servercode],[username],[name],[price],[status],[password],[tencent],[date]) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",[getServerCodeForGroupID($_SESSION['group']), $_SESSION['username'], $name, $price, 0, $password, $tencent, logDate()]);
            $muonline->commit();
            $this->Web->commit();
            return true;
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 寄售物品
     * @param $nameID
     * @param $itemCode
     * @param $password
     * @param $tencent
     * @param $price
     * @return string
     * @throws Exception
     */
    public function setItemSell($nameID,$itemCode,$password,$tencent,$price)
    {
        if(!check_value($itemCode)) throw new Exception("没有检测到您的物品信息!");
        if($this->excludeItem($itemCode)) throw new Exception("该物品禁止寄售,请您选择其他物品!");
        if(!check_value($nameID)) throw new Exception("[3]发布失败，请重新登陆！");
        if(!Validator::AlphaNumeric($nameID)) throw new Exception("[4]发布失败，请重新登陆！");
        if(check_value($password)){
            if(!Validator::UnsignedNumber($password)) throw new Exception("[5]发布失败，请重新登陆！");
        }
        if(!check_value($tencent)) throw new Exception("[6]发布失败，请重新登陆！");
        if(!Validator::UnsignedNumber($tencent)) throw new Exception("[7]发布失败，请重新登陆！");
        if(!check_value($price)) throw new Exception("[8]发布失败，请重新登陆！");
        if(!Validator::UnsignedNumber($price)) throw new Exception("[7]发布失败，请重新登陆！");
        if($price < $this->_ItemMarketConfig['min_price'] || $price > $this->_ItemMarketConfig['max_price'])  throw new Exception('寄售的价格必须在['.$this->_ItemMarketConfig['min_price'].'~'.$this->_ItemMarketConfig['max_price'].']之间！');

        if ($this->common->checkUserOnline($_SESSION['group'], $_SESSION['username'])) throw new Exception("您的账号当前游戏在线，请先断开连接。");

        #获取卖家角色名
        $AccountCharacter = $this->character->getAccountCharacterNameForAccount($_SESSION["group"],$_SESSION["username"]);
        if(!isset($AccountCharacter[$nameID]) || empty($AccountCharacter[$nameID])) throw new Exception("角色识别失败,请联系在线客服.");
        $char_name = $AccountCharacter[$nameID];

        #-----------------------------------------------
        #定义物品类型
        $item = $this->equipment->convertItem($itemCode);
        if(!is_array($item))  throw new Exception("[7]发布失败，物品类型获取失败，请联系在线客服。");
        $itemType = $this->equipment->itemType($item['section'],$item['section'],$item['type'],$this->equipment->itemOption['Slot']);
        #-----------------------------------------------
        #验证是否会员道具
        if($this->_ItemMarketConfig['vip_items']){
            if ($item['vip']) throw new Exception("提交出错，会员道具禁止出售。");
        }
        #验证是否为宝石
        #-----------------------------------------------
        if($this->_ItemMarketConfig['jewel']) {
            if(!$this->equipment->checkItemIsJewel($item['type'])) throw new Exception("该物品禁止寄售，仅支持出售宝石系列产品。");
        }
        #-----------------------------------------------

        #仓库中移除该物品
        $warehouse = new warehouse($_SESSION['group']);
        $warehouseData = ($warehouse->warehouseRemoveItem($itemCode));
        $muonline = Connection::Database("MuOnline",$_SESSION['group']);
        # 启动事务
        try{
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$warehouseData, $_SESSION['username']]);
            $this->Web->query("INSERT INTO [X_TEAM_MARKET_ITEM] ([servercode],[username],[name],[item_code],[item_type],[price],[status],[password],[tencent],[date]) VALUES (?,?,?,?,?,?,?,?,?,?)",[getServerCodeForGroupID($_SESSION['group']),$_SESSION['username'],$char_name,$itemCode,$itemType[0],$price,0,$password,$tencent,logDate()]);
            $muonline->commit();
            $this->Web->commit();
            return true;
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 角色下架
     * @param $id
     * @return int
     * @throws Exception
     */
    public function setCharSellOff($id)
    {
        if(!check_value($_SESSION['group'])) throw new Exception("数据错误，请重新登陆！");
        if(!check_value($_SESSION['username'])) throw new Exception("数据错误，请重新登陆！");
        if(!check_value($id)) throw new Exception("数据错误，请重新登陆！");
        if(!Validator::UnsignedNumber($id)) throw new Exception("数据错误，请重新尝试！");
        $data = $this->getMarketCharList($id);
        if(!is_array($data))  throw new Exception("操作失败，请联系在线客服！");
        $muonline = Connection::Database("MuOnline",$_SESSION['group']);
        try{
            $muonline->beginTransaction();
           
			$this->Web->beginTransaction();
			
            $muonline->query("UPDATE [Character] SET [CtlCode] = ? WHERE [AccountID] = ? AND [Name] = ?",[0,$_SESSION['username'],$data[0]['name']]);
            $this->Web->query("DELETE FROM [X_TEAM_MARKET_CHAR] WHERE [ID] = ? AND [username] = ? AND [servercode] = ?",[$id,$_SESSION['username'],getServerCodeForGroupID($_SESSION['group'])]);
            $muonline->commit();
            $this->Web->commit();
            return 1;
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 物品下架
     * @param $id
     * @return string
     * @throws Exception
     */
    public function setItemSellOff($id)
    {
        if(!check_value($_SESSION['group'])) throw new Exception("[1]数据错误，请重新登陆！");
        if(!check_value($_SESSION['username'])) throw new Exception("[2]数据错误，请重新登陆！");
        if(!check_value($id)) throw new Exception("[3]数据错误，请重新尝试！");
        if(!Validator::UnsignedNumber($id)) throw new Exception("[4]数据错误，请重新尝试！");
        $data = $this->getMarketItemList($id);
        if (!preg_match('/[a-fA-F0-9]/',$data[0]['item_code'])) throw new Exception("操作失败,物品识别错误!");
        if(!is_array($data)) throw new Exception("您所选的物品已经下架!");
        #组装仓库代码
        $warehouse = new warehouse($_SESSION['group']);
        $newWarehouse = $warehouse->warehouseAddItem($data[0]['item_code']);
        $muonline = Connection::Database("MuOnline",$_SESSION['group']);

        try {
            $muonline->beginTransaction();
            $this->Web->beginTransaction();
            $muonline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$newWarehouse,$_SESSION['username']]);
            $this->Web->query("DELETE FROM [X_TEAM_MARKET_ITEM] WHERE [ID] = ? AND [username] = ? AND [servercode] = ?",[$id,$_SESSION['username'],getServerCodeForGroupID($_SESSION['group'])]);
            $muonline->commit();
            $this->Web->commit();
            return 1;
        }catch (Exception $exception){
            $muonline->rollBack();
            $this->Web->rollBack();
                throw new Exception($exception->getMessage());
        }
    }

	
	
	/**
     * 检查是否支付1元 检查用户状态
     * @param int $id
     * @return array|bool|null
     * @throws Exception
	*/
    public function getPayStatus()
    {
		$muonline = Connection::Database("MuOnline",$_SESSION['group']);
		try {
            $data=$muonline->query_fetch("select payStatus  from  [MEMB_INFO]  WHERE [memb___id] = ?",[$_SESSION['username']]);
            if(!is_array($data)) return null;
			return $data[0]['payStatus'];
			
        }catch (Exception $exception){
            $muonline->rollBack();
            throw new Exception($exception->getMessage());
        }
		
    }
	/**
     * 获取市场售卖列表
     * @param int $id
     * @return array|bool|null
     * @throws Exception
     */
    public function getMarketCharList($id = 0)
    {
        if (!Validator::UnsignedNumber($id)) return null;
        $where = $id ? " AND [ID] = ".$id : "";
        $query = "SELECT  top 10000 [ID],[servercode],[username],[name],[price],[status],[password],[tencent],[out_trade_no],[other],[date],[buy_username],[buy_price],[buy_alipay],[buy_wechat],[buy_date] FROM [X_TEAM_MARKET_CHAR] WHERE [status] < 2".$where." order by date desc";
        $data = $this->Web->query_fetch($query);
        if(!is_array($data)) return null;
        foreach ($data AS $key=>$item){
            $newData = $this->character->getCharacterDataForCharacterName(getGroupIDForServerCode($item['servercode']),$item['name']);
            if(!is_array($newData)) continue;
            $char_data[$key] = [
                'ID'            => $item['ID'],
                'username'      => $item['username'],
                'servercode'    => $item['servercode'],
                'group'         => getGroupIDForServerCode($item['servercode']),
                'name'          => $item['name'],
                'price'         => $item['price'],
                'date'          => $item['date'],
                'tencent'       => $item['tencent'],
                'password'      => $item['password'],
                'out_trade_no'  => $item['out_trade_no'],
                'cLevel'        => $newData['cLevel'],
                'mLevel'        => $newData['mLevel'],
                'Class'         => $newData['Class'],
                'status'        => $item['status'],
                'buy_username'  => $item['buy_username'],
                'buy_price'     => $item['buy_price'],
                'buy_date'      => $item['buy_date'],
            ];
        }
        if(empty($char_data)) return null;
        //从发布时间排序
        return arraySortByKey($char_data,"date",0);
    }
	
	/**
     * 返回装备名称
     * @param int $id
     * @return array|null
     * @throws Exception
     */
	 public function getMarketItemGroup()
    {
		 $query = "select top 16  itemname_ From X_TEAM_MARKET_ITEM_detail  where exists(select *from X_TEAM_MARKET_ITEM where X_TEAM_MARKET_ITEM.item_code=X_TEAM_MARKET_ITEM_detail.itemcode and  X_TEAM_MARKET_ITEM.[status] < 2)  group by itemname_ order by COUNT(*) desc ";
        $data = $this->Web->query_fetch($query);
        if(!is_array($data)) return null;
        return $data;
    }

	public function unicodeDecode($unicode_str){
		$json = '{"str":"'.$unicode_str.'"}';
		$arr = json_decode($json,true);
		if(empty($arr)) return '';
		return $arr['str'];
	}

	
	public function PrintItemToData($item_code){
		
		 try {
			     $this->Web->beginTransaction();
				  
				 $equipment_ = new equipment();
				 $printContent=$equipment_->printItems($item_code);
				 $item=$equipment_->getItemInfo();
				 $itemName = $equipment_->getItemName($item['section'],$item['index'],$item['level']);
				 
				 
				 $level_="";
				 $equment0="";
				 $equment1="";
				 $equment2="";
				 $equment3="";
				 $equment4="";
				 $equment5="";
				 $equment6="";
				 $equment7="";
				 $equment8="";
				 $equment9="";
				 $equment10="";
				 $equment11="";
				
				 $ext_="";
				 $section=$item['section'];
				 
				 
				
				 
				 
				 
				 if(!empty($printContent)){
					
					
					
					 if(strpos($printContent,$itemName)!== false){
						   $level_=getPrintKeyValue($printContent,$itemName);
					  }
					   
					  if(strpos($printContent,'追加防御力')!== false){
						  $ext_=getPrintKeyValue($printContent,'追加防御力');
					  }
					  
					  if(strpos($printContent,'追加攻击力')!== false){
						  $ext_=getPrintKeyValue($printContent,'追加攻击力');
					  }
					  
					  if(strpos($printContent,'生命值增加')!== false){
						  $equment0=getPrintKeyValue($printContent,'生命值增加');
					  }
					 
					  if(strpos($printContent,'魔法值增加')!== false){
						  $equment1=getPrintKeyValue($printContent,'魔法值增加');
					  }
					  
					  if(strpos($printContent,'伤害减少')!== false){
						  $equment2=getPrintKeyValue($printContent,'伤害减少');
					  }
					  
					  if(strpos($printContent,'伤害反射')!== false){
						  $equment3=getPrintKeyValue($printContent,'伤害反射');
					  }
					  
					  
					  if(strpos($printContent,'防御成功率增加')!== false){
						  $equment4=getPrintKeyValue($printContent,'防御成功率增加');
					  }
					  if(strpos($printContent,'所获金')!== false){
						  $equment5=getPrintKeyValue($printContent,'所获金');
					  }
					  if(strpos($printContent,'卓越攻击几率')!== false){
						  $equment6=getPrintKeyValue($printContent,'卓越攻击几率');
					  }
					  
					  if(strpos($printContent,'攻击力增加')!== false){
						    $begin=strpos($printContent,'攻击力增加');
							$end=strpos($printContent,'</div>',$begin);
							$array[]=substr($printContent,$begin,$end-$begin);
							
							$begin=strpos($printContent,'攻击力增加',$end);
							if($begin!== false){
								$end=strpos($printContent,'</div>',$begin);
								$array[]=substr($printContent,$begin,$end-$begin);
							}
							foreach($array as $value){
								
								if(strpos($value,'等级')!== false ){
									 $equment7=getPrintKeyNum($value,'攻击力增加');
								}else{
									$equment8=getPrintKeyNum($value,'攻击力增加');
								}
							}
						
							
							 
					 }
					  
					 if(strpos($printContent,'攻击(魔法)速度')!== false){
						 $equment9=getPrintKeyValue($printContent,'攻击(魔法)速度');
					 }
					  
					 if(strpos($printContent,'杀死怪物时增加 +生命值')!== false){
						  $equment10="1";
					 }
					 if(strpos($printContent,'杀死怪物时增加 +魔法值')!== false){
						  $equment11="1";
					 }
					 if(strpos($printContent,'杀死怪物时所获魔法值增加 +生命值')!== false){
						  $equment10="1";
					 }
					 if(strpos($printContent,'杀死怪物时所获魔法值增加 +魔法值')!== false){
						  $equment11="1";
					 }
					 
					   
				}
			     $insertsql="insert into x_team_market_item_detail(itemcode,level_,ext_,lucky_,skill_,set_,pos_,exOption_,socket_,itemname_,equment0,equment1,equment2,equment3,equment4,equment5,equment6,equment7,equment8,equment9,equment10,equment11,serial,section)values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
				 $itemcode_=$item_code;
				 $lucky_=$item['lucky'];
				 $skill_=$item['skill'];
				 $set_="0";
				 
				if(strpos($printContent,'套装物品属性信息')!== false){
					 $set_="1";;
				}
				$socket_="0";
				if(strpos($printContent,'镶宝[1]')!== false){
				    $socket_="1";
				}	
				if(strpos($printContent,'镶宝[2]')!== false){
				    $socket_="2";
				}
				if(strpos($printContent,'镶宝[3]')!== false){
				    $socket_="3";
				}
				if(strpos($printContent,'镶宝[4]')!== false){
				    $socket_="4";
				}	
				if(strpos($printContent,'镶宝[5]')!== false){
				    $socket_="5";
				}
	 			 
				 $pos_="";
				 $exOption_=$item['option'];
				 $itemname_=$itemName;
				 $findRecord= $this->Web->query_fetch("select *From x_team_market_item_detail where itemcode='".$itemcode_."'");
				 if(!is_array($findRecord)){
				 $this->Web->query($insertsql,[$itemcode_,$level_,$ext_,$lucky_,$skill_,$set_,$pos_,$exOption_,$socket_,$itemname_,$equment0,$equment1,$equment2,$equment3,$equment4,$equment5,$equment6,$equment7,$equment8,$equment9,$equment10,$equment11,$item['serial1'],$item['section']]);
				 }
				$this->Web->commit();
		 }
		 catch (Exception $exception){
					 $this->Web->rollBack();
                     throw new Exception($exception->getMessage());
         }
				
		
		
	}
    /**
     * 获取市场售卖列表
     * @param int $id
     * @return array|null
     * @throws Exception
     */
    public function getMarketItemList($id = 0)
    {
       if (!Validator::UnsignedNumber($id)) return null;
		$list = $this->Web->query_fetch("select *From X_TEAM_MARKET_ITEM where  not exists(select *From X_TEAM_MARKET_ITEM_DETAIL where X_TEAM_MARKET_ITEM_DETAIL.itemcode=X_TEAM_MARKET_ITEM.item_code)");
		if(is_array($list)){
			   foreach ($list as $key=>$datum){
				    $this->PrintItemToData($datum['item_code']);
			    }
		}
		
		
		$where = $id ? " AND a.[ID] = ".$id : "";
		if(check_value($_GET['itemName'])) {
			 $where= $where." and b.itemname_ like '%".$_GET['itemName']."%'"; 
		}
		//等级
		if(check_value($_GET['level'])) {
			 $where= $where." and b.level_='".$_GET['level']."'"; 
		}
		//追加
		if(check_value($_GET['ext'])) {
			 $ext=$_GET['ext']*4;
			 $where= $where." and b.ext_='".$ext."'"; 
		}
		//幸运
		if(check_value($_GET['lucky'])  &&  $_GET['lucky']=='1') {
			 $where= $where." and b.lucky_='1' " ;
		}
		//套装
		if(check_value($_GET['set'])  &&  $_GET['set']=='1') {
			 $where= $where." and b.set_='1' " ;
		}
		//技能
		if(check_value($_GET['skill'])  &&  $_GET['skill']=='1') {
			 $where= $where." and b.skill_='1' " ;
		}
		//部位
		if(check_value($_GET['pos']) ) {
			 $where= $where." and b.section='".$_GET['pos']."'" ;
		}
		//分区
		if(check_value($_GET['servercode']) ) {
			 $where= $where." and a.servercode=".$_GET['servercode'] ;
		}
		
		if((check_value($_GET['socket0']) && $_GET['socket0']=='1') || (check_value($_GET['socket1']) && $_GET['socket1']=='1') || (check_value($_GET['socket2']) && $_GET['socket2']=='1') || (check_value($_GET['socket3'])  && $_GET['socket3']=='1') || (check_value($_GET['socket4']) &&  $_GET['socket4']=='1')){
			 $where= $where." and (b.socket_='a' ";
			 if(check_value($_GET['socket0']) && $_GET['socket0']=='1'){
				 $where= $where." or b.socket_='1' "; 
			 }
			if(check_value($_GET['socket1']) && $_GET['socket1']=='1'){
				 $where= $where." or b.socket_='2' "; 
			 }
			 if(check_value($_GET['socket2']) && $_GET['socket2']=='1'){
				 $where= $where." or b.socket_='3' "; 
			 }
			 if(check_value($_GET['socket3']) && $_GET['socket3']=='1'){
				 $where= $where." or b.socket_='4' "; 
			 }
			 if(check_value($_GET['socket4'])  && $_GET['socket4']=='1'){
				 $where= $where." or b.socket_='5' "; 
			 }
			 $where=$where.")";
		}
		if(check_value($_GET['equment0']) && (int)$_GET['equment0']>=1) {
			 $where= $where." and cast(isnull(b.equment0,'-1') as int)>=".$_GET['equment0']; 
		}
		if(check_value($_GET['equment1'])  && (int)$_GET['equment1']>=1) {
			 $where= $where." and cast(isnull(b.equment1,'-1') as int)>=".$_GET['equment1']; 
		}
		if(check_value($_GET['equment2'])  && (int)$_GET['equment2']>=1) {
			 $where= $where." and cast(isnull(b.equment2,'-1') as int)>=".$_GET['equment2']; 
		}
		if(check_value($_GET['equment3'])  && (int)$_GET['equment3']>=1) {
			 $where= $where." and cast(isnull(b.equment3,'-1') as int)>=".$_GET['equment3']; 
		}
		if(check_value($_GET['equment4']) && (int)$_GET['equment4']>=1) {
			 $where= $where." and cast(isnull(b.equment4,'-1') as int)>=".$_GET['equment4']; 
		}
		if(check_value($_GET['equment5'])  && (int)$_GET['equment5']>=1) {
			 $where= $where." and cast(isnull(b.equment5,'-1') as int)>=".$_GET['equment5']; 
		}
		if(check_value($_GET['equment6'])  && ((int)$_GET['equment6'])>=1) {
			 $where= $where." and cast(isnull(b.equment6,'-1') as int)>=".$_GET['equment6']; 
		}
		if(check_value($_GET['equment7'])  && (int)$_GET['equment7']>=1) {
			 $where= $where." and cast(isnull(b.equment7,'-1') as int)>=".$_GET['equment7']; 
		}
		if(check_value($_GET['equment8'])  && (int)$_GET['equment8']>=1) {
			 $where= $where." and cast(isnull(b.equment8,'-1') as int)>=".$_GET['equment8']; 
		}
		if(check_value($_GET['equment9'])  && (int)$_GET['equment9']>=1) {
			 $where= $where." and cast(isnull(b.equment9,'-1') as int)>=".$_GET['equment9']; 
		}
		if(check_value($_GET['equment10'])  && (int)$_GET['equment10']>=1) {
			 $where= $where." and cast(isnull(b.equment10,'-1') as int)>=".$_GET['equment10']; 
		}
		if(check_value($_GET['equment11'])  && (int)$_GET['equment11']>=1) {
			 $where= $where." and cast(isnull(b.equment11,'-1') as int)>=".$_GET['equment11']; 
		}
		$query="SELECT top 10000 a.[ID],a.[servercode],a.[username],a.[name],a.[item_code],a.[item_type],a.[price],a.[flag],a.[status],a.[password],a.[tencent],a.[other],a.[date],a.[out_trade_no],a.[buy_username],a.[buy_price],a.[buy_alipay],a.[buy_wechat],a.[buy_date],b.[itemcode],b.level_,b.ext_,b.lucky_,b.skill_,b.set_,b.pos_,b.exOption_,b.socket_,b.[itemname_],b.[serial] FROM [X_TEAM_MARKET_ITEM] a left outer join X_TEAM_MARKET_ITEM_detail b on a.[item_code]=b.itemcode  WHERE    a.[status] < 2  ".$where."  order by date   desc";
		
		$data = $this->Web->query_fetch($query);
		if(!is_array($data)) return null;
        foreach ($data as $key=>$datum){
            $data[$key]['item_name'] =  $datum['itemname_'];
			//$data[$key]['serial'] = $datum['serial1'];
        }
        //根据时间降序
        return arraySortByKey($data,'date',0);
    }

    /**
     * 获取用户寄售中的角色列表
     * @param $group
     * @param $username
     * @param int $status //0=未售出,1=销售中,2=已售出
     * @return array|bool|null
     * @throws Exception
     */
    public function getMyMarketCharList($group,$username,$status = 0)
    {
        if(!check_value($group)) return null;
        if(!check_value($username)) return null;
        #先把过期支付中的清空
        $query1 = "SELECT [ID],[servercode],[username],[name],[price],[status],[password],[tencent],[out_trade_no],[other],[date],[buy_username],[buy_price],[buy_alipay],[buy_wechat],[buy_date] FROM [X_TEAM_MARKET_CHAR] WHERE [username] = ? AND [servercode] = ? AND [status] = ?";
        $data1 = $this->Web->query_fetch($query1,[$username,getServerCodeForGroupID($group),$status]);
        if(is_array($data1)){
            foreach ($data1 as $item){
                #判断是否大于2分钟,如果大于指定时间关闭订单.
                if($item['buy_date'] && strtotime(logDate()) - strtotime($item['buy_date']) > 60*$this->timeOut){
                    $this->clearMarketOutTradeNo($item['ID'],1);
                }
            }
        }
        $query = "SELECT [ID],[servercode],[username],[name],[price],[status],[password],[tencent],[out_trade_no],[other],[date],[buy_username],[buy_price],[buy_alipay],[buy_wechat],[buy_date] FROM [X_TEAM_MARKET_CHAR] WHERE [username] = ? AND [servercode] = ? AND [status] = ?";
        $data = $this->Web->query_fetch($query,[$username,getServerCodeForGroupID($group),$status]);
        if(!is_array($data)) return null;
        return $data;
    }

    /**
     * 获取用户寄售中的物品列表
     * @param $group
     * @param $username
     * @param int $status //0=未售出,1=交易中,2=已售出
     * @return array|bool|null
     * @throws Exception
     */
    public function getMyMarketItemList($group,$username,$status = 0)
    {
        if(!check_value($group)) return null;
        if(!check_value($username)) return null;
		
        #获取在售角色
        $query1 = "SELECT [ID],[servercode],[username],[name],[item_code],[item_type],[price],[flag],[status],[password],[tencent],[other],[date],[out_trade_no],[buy_username],[buy_price],[buy_alipay],[buy_wechat],[buy_date] FROM [X_TEAM_MARKET_ITEM] WHERE [username] = ? AND [servercode] = ? AND [status] = 1";
        $data1 = $this->Web->query_fetch($query1,[$username,getServerCodeForGroupID($group),$status]);
        if(is_array($data1)){
            foreach ($data1 as $item){
                #判断是否大于2分钟,如果大于指定时间关闭订单.
                if($item['buy_date'] && strtotime(logDate()) - strtotime($item['buy_date']) > 60*$this->timeOut){
                    $this->clearMarketOutTradeNo($item['ID']);
                }
            }
        }
        $query = "SELECT [ID],[servercode],[username],[name],[item_code],[item_type],[price],[flag],[status],[password],[tencent],[other],[date],[out_trade_no],[buy_username],[buy_price],[buy_alipay],[buy_wechat],[buy_date] FROM [X_TEAM_MARKET_ITEM] WHERE [username] = ? AND [servercode] = ? AND [status] = ?";
        $data = $this->Web->query_fetch($query,[$username,getServerCodeForGroupID($group),$status]);
        if(!is_array($data)) return null;
        return $data;
    }

    /**
     * 获取用户寄售中的角色列表
     * @param $group
     * @param $username
     * @return array|bool|null
     * @throws Exception
     */
    public function getMyMarketCharBuyLog($group,$username)
    {
        if(!check_value($group)) return null;
        if(!check_value($username)) return null;
        $query = "SELECT [ID],[servercode],[username],[name],[price],[status],[password],[tencent],[out_trade_no],[other],[date],[buy_username],[buy_price],[buy_alipay],[buy_wechat],[buy_date] FROM [X_TEAM_MARKET_CHAR] WHERE [buy_username] = ? AND [servercode] = ? AND [status] = ?";
        $data = $this->Web->query_fetch($query,[$username,getServerCodeForGroupID($group),2]);
        if(!is_array($data)) return null;
        return $data;
    }

    /**
     * 获取用户寄售中的角色列表
     * @param $group
     * @param $username
     * @return array|bool|null
     * @throws Exception
     */
    public function getMyMarketItemBuyLog($group,$username)
    {
        if(!check_value($group)) return null;
        if(!check_value($username)) return null;
        $query = "SELECT [ID],[servercode],[username],[name],[item_code],[item_type],[price],[flag],[status],[password],[tencent],[other],[date],[out_trade_no],[buy_username],[buy_price],[buy_alipay],[buy_wechat],[buy_date] FROM [X_TEAM_MARKET_ITEM] WHERE [buy_username] = ? AND [servercode] = ? AND [status] = ?";
        $data = $this->Web->query_fetch($query,[$username,getServerCodeForGroupID($group),2]);
        if(!is_array($data)) return null;
        return $data;
    }

    /**
     * 识别物品类型
     * @param $itemType
     * @return mixed
     */
    public function getItemCategory($itemType)
    {
        if(!array_key_exists($itemType,$this->itemType)) return $this->itemType[0];
        return $this->itemType[$itemType];
    }

    /**
     * 排除物品
     * @param $itemCode
     * @return bool
     * @throws Exception
     */
    private function excludeItem($itemCode){
        $itemConfig = $this->loadConfig('item');
        if(!check_value($itemConfig['exclude'])) return false;

        $excludedItem = explode(",", (string)$itemConfig['exclude']);
        if(!is_array($excludedItem)) return false;
        $itemType = $this->getItemType($itemCode);
        foreach($excludedItem as $itemExcludeCode) {
            if ($itemType == $itemExcludeCode) return true;
        }
        return false;
    }

    /**
     * 获取物品总编码
     * @param $ItemData
     * @return int|mixed|void
     */
    public function getItemType($ItemData){
        if($ItemData == ($this->NULL)) return;
        if(empty($ItemData)) return;
        $dataInv = str_split($ItemData, 2); //划切2
        $i = 0;
        while($i<=16){//放进数组
            $item[$i] = hexdec($dataInv[$i]); //16进10
            $i++;
        }
        if(empty($item)) return;

        #解算
        $m_type = $item[0];
        $m_type |= ($item[9] & 0xF0) << 5;
        $m_type |= ($item[7] & 0x80) << 1;		//算总类
        return $m_type;
    }

    /**
     * 统计有几条数据
     * @param $name
     * @return mixed|void
     * @throws Exception
     */
    public function checkSellChar($name)
    {
        if(!check_value($_SESSION['group'])) return;
        if(!check_value($name)) return;
        $result = $this->Web->query_fetch_single("SELECT COUNT(*) as result FROM [X_TEAM_MARKET_CHAR] where [name] = ? AND [servercode] = ?",[$name,getServerCodeForGroupID($_SESSION['group'])]);
        if(!is_array($result)) return;
        return $result['result'];
    }

    /**
     * 申请订单号
     * @param $id
     * @param $username
     * @param $out_trade_no
     * @param int $modules //0是物品市场,1是角色市场
     * @return string
     * @throws Exception
     */
    public function getMarketOutTradeNo($id,$username,$out_trade_no,$modules = 0)
    {
        if(!check_value($id)) throw new Exception("[TRADE] 数据错误，请重新提交！");
        if(!check_value($username)) throw new Exception("[TRADE] 数据错误，请重新提交！");
        $TABLE = $modules ? "X_TEAM_MARKET_CHAR" : "X_TEAM_MARKET_ITEM";
        $data = $this->Web->query("UPDATE [".$TABLE."] SET [status] = ?, [buy_username] = ?, [out_trade_no] = ?, [buy_date] = ? WHERE [ID] = ?",[1,$username,$out_trade_no,logDate(),$id]);
        if($data) return $out_trade_no;
        return null;
    }
	
	
	//更新购买者角色
	 public function update_buy_char($id,$buy_char)
    {
        if(!check_value($id)) throw new Exception("[TRADE] 数据错误，请重新提交！");
        $data = $this->Web->query("UPDATE [X_TEAM_MARKET_ITEM] SET [buy_char] = ?  WHERE [ID] = ?",[$buy_char,$id]);
        return true;
    }
	
	
	/**
	 * 正在购买的
	 */
	 
	public function queryMarketInBuy($type=1,$out_trade_no){
		 if(!check_value($out_trade_no)) throw new Exception("[TRADE] 数据错误，请重新提交！");
		 $result=null;
		 if($type==2){
		     $result = $this->Web->query_fetch("SELECT * FROM [X_TEAM_MARKET_CHAR] where status =1 AND [out_trade_no] = ?",[$out_trade_no]);
		 }else{
		    $result = $this->Web->query_fetch("SELECT * FROM [X_TEAM_MARKET_ITEM] where status =1 AND [out_trade_no] = ?",[$out_trade_no]); 
		 } 
		 return  $result;
	}



    /**
     * 清理订单号
     * @param $id
     * @param $modules //0是物品市场,1是角色市场
     * @return int
     * @throws Exception
     */
    public function clearMarketOutTradeNo($id,$modules = 0)
    {
        if(!check_value($id)) throw new Exception("[TRADE] 数据错误，请重新提交！");
        $TABLE = $modules ? "X_TEAM_MARKET_CHAR" : "X_TEAM_MARKET_ITEM";
        $data = $this->Web->query("UPDATE [".$TABLE."] SET [status] = ?, [buy_username] = ?, [out_trade_no] = ? WHERE [ID] = ?",[0,null,null,$id]);
        if($data) return 1;
        return 0;
    }

    /**
     * 查询是否已经申请订单号
     * @param $id
     * @param $username
     * @param $modules //0是物品市场,1是角色市场
     * @return int      // 返回1表有人使用
     * @throws Exception
     */
    public function checkMarketOutTradeNo($id, $username, $modules = 0)
    {
        if(!check_value($id)) throw new Exception("[TRADE] 数据错误，请重新提交！");
        if(!check_value($username)) throw new Exception("[TRADE] 数据错误，请重新提交！");
        $TABLE = $modules ? "X_TEAM_MARKET_CHAR" : "X_TEAM_MARKET_ITEM";
        $data = $this->Web->query_fetch_single("SELECT [servercode],[status],[out_trade_no],[buy_username],[buy_date] FROM [".$TABLE."] WHERE [ID] = ?",[$id]);
        if(!is_array($data)) throw new Exception("[OUT TRADE] 数据错误，请重新提交！");
        if(1 == $data['status']){

            #如果申请人是同一个人则跳过
            if(getServerCodeForGroupID($_SESSION['group']) == $data['servercode'] && $username == $data['buy_username']){
                return 0;
            }

            #判断是否大于2分钟,如果大于指定时间关闭订单.
            if($data['buy_date']){
                if(strtotime(logDate()) - strtotime($data['buy_date']) > 60*$this->timeOut){
                    $this->clearMarketOutTradeNo($id, $modules);
                }
            }

            return 1;
        }

        return 0;
    }

    /************************ 插件必备函数 ************************/
    /**
     * 导航栏
     */
    public function MarketMenu() {
        echo '<div class="card">';
            echo '<div class="row mb-3">';
                echo '<div class="btn-group col-md-12" role="group">';
                foreach($this->_MarketMenu as $menuData) {
                    if(is_array($menuData[3])) {
                        if(!in_array($this->serverFiles, $menuData[3])) continue;
                    }
                    if($menuData[2]) {
                        if($_REQUEST['subpage'] == $menuData[1]) {
                            echo '<a href="'.__PLUGIN_MARKET_HOME__.$menuData[1].'" class="btn btn-outline-dark active">';
                            echo $menuData[0];
                            echo '</a>';
                        } else {
                            echo '<a href="'.__PLUGIN_MARKET_HOME__.$menuData[1].'" class="btn btn-outline-dark">';
                            echo $menuData[0];
                            echo '</a>';
                        }
                    }
                }
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    /************************ 插件必备函数 ************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载交易市场插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载交易市场插件模块。');
		if(!@include_once(__PATH_PLUGIN_MARKET_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new Exception('无法加载交易市场插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_PLUGIN_MARKET_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

    /**
     * 加载配置文件
     * @param $fileName
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($fileName='config')
    {
        $xmlPath = __PATH_PLUGIN_MARKET_ROOT__.$fileName.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('交易市场配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载交易市场插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }

    /************************ 插件必备函数 ************************/
}
?>