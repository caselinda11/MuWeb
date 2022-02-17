<?php
/**
 * 显示装备属性插件
 *
 *          #define DBI_TYPE			0
 *          #define DBI_OPTION_DATA		1
 *          #define DBI_DUR			    2
 *          #define DBI_SERIAL1		    3
 *          #define DBI_SERIAL2			4
 *          #define DBI_SERIAL3		    5
 *          #define DBI_SERIAL4			6
 *          #define DBI_NOPTION_DATA	7
 *          #define DBI_SOPTION_DATA	8
 *          #define DBI_OPTION380_DATA	9
 *          #define DBI_JOH_DATA		10
 *          #define DBI_SOCKET_1		11
 *          #define DBI_SOCKET_2		12
 *          #define DBI_SOCKET_3		13
 *          #define DBI_SOCKET_4		14
 *          #define DBI_SOCKET_5		15
 *          #define DBI_SERIAL5			16
 *          #define DBI_SERIAL6		    17
 *          #define DBI_SERIAL7		    18
 *          #define DBI_SERIAL8			19
 *
 *          #define MAX_ITEM_INFO		12
 *          #define MAX_DBITEM_INFO		32
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use SimpleXMLElement;
use Validator;

class equipment{
    private $config,$version = 0,$serverFiles;
    private $_type;
    private $Excellent; #未使用
    private $itemListFile                   = __PATH_ITEMS_FILE__.'ItemList.xml';
    private $itemLevelToopFile              = __PATH_ITEMS_FILE__.'ItemLevelTooltip.xml';
    private $itemSkillListFile              = __PATH_ITEMS_FILE__.'SkillList.xml';
    private $itemAddOptionFile              = __PATH_ITEMS_FILE__.'ItemAddOption.xml';
    private $itemOptionTextFile             = __PATH_ITEMS_FILE__.'ItemOptionText.xml';
    private $itemSetTypeFile                = __PATH_ITEMS_FILE__.'ItemSetType.xml';
    private $itemSetOptionFile              = __PATH_ITEMS_FILE__.'ItemSetOption.xml';
    private $itemHarmonyOptionFile          = __PATH_ITEMS_FILE__.'HarmonyItem_Option.xml';
    private $itemSetOptionTextFile          = __PATH_ITEMS_FILE__.'ItemSetOptionText.xml';
    private $itemExcellentOptionsFile       = __PATH_ITEMS_FILE__.'ExcellentOptions.xml';
    private $itemSocketOptionFile           = __PATH_ITEMS_FILE__.'SocketOption.xml';
    private $itemEarringAttributeFile       = __PATH_ITEMS_FILE__.'EarringAttribute.xml';
    private $item4thWingAttributeFile       = __PATH_ITEMS_FILE__.'PentagramWingAttribute.xml';
    private $item4thWingGradeOptionFile     = __PATH_ITEMS_FILE__.'ItemGradeOption.xml';
    private $itemMasteryBonusOptionsFile    = __PATH_ITEMS_FILE__.'MasteryBonusOptions.xml';
    private $itemTooNameFile                = __PATH_ITEMS_FILE__.'TooName.xml';
    private $serial1 = '00000000',$serial2 = 'FFFFFFFF';
    public $itemOption,$info;  #物品详细信息
    public $exe_count = 0; #统计卓越数
    public $IsSet      = false;     #套装
	public $IsSet_      =false;
    public $IsEarring  = false;     #耳环
    public $IsExc = false;          #卓越
    public $IsNotItems     = false;     #不是装备类物品
    public $IsWing     = false;     #翅膀
    public $Is4thWing  = false;     #四代
    public $IsSocket   = false;     #镶嵌
    public $IsExtItem  = false;     #时限
    public $IsPentagram = false;    #卷轴
    public $Is380Item   = false;    #380
	public $socketCount=0;
    private $IsVIP       = false;    #会员道具
    private $file,$itemInfo;
    
	
    private $levelName2 = false;     #二次等级
    public $IsJewel = false;
    /**
     * 构造函数
     * equipment constructor.
     * @throws Exception
     */
    public function __construct()
    {
        #服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        $this->config = $this->loadConfig();
        $this->version = 1;
    }
	
	/**
	  获得itemInfo;
	**/
	public function getItemInfo(){
		return $this->itemInfo;
	}
	/**
	  获得所有信息
	**/
	public function getALLInfo(){
		
		
	}
	public function getConfig(){
		
		return $this->config;
	}
    /**
     * 物品字节转换
     * @param $ItemData
     * @return array|string|void
     * @throws Exception
     */
    public function convertItem($ItemData){
        if (!$this->IsItem($ItemData)) return '物品解析失败!';

        $dataInv = str_split($ItemData, 2); //划切2
        $this->serial1 = substr($ItemData,6,8);  //识别码 1
        if(substr($this->serial1,0,2) == 'FF') $this->IsVIP = true; #会员道具
        if($this->serverFiles === 'igcn') $this->serial2 = substr($ItemData, 32, 8); //识别码 2
        //0C002F0E0303FC28009000FFFFFFFFFF
        for($i=0;$i<=16;$i++){//放进数组
            $buf[$i] = hexdec($dataInv[$i]); //16进10
        }
        if(empty($buf)) return;
        #解算
        $m_type = $buf[0];
        $m_type |= ($buf[9] & 0xF0) << 5;
        $m_type |= ($buf[7] & 0x80) << 1;		//算总类
        $m_Section = ($buf[9] & 0xF0) <<5;
        $m_Section = $m_Section / 512;			//分类
        $m_index = $buf[0] % 256;
        $m_index |= ($buf[7] & 0x80) * 2;		//序号
        $m_level = ($buf[1]/8) & 15;  	        //等级
        $m_skill = (($buf[1]) >> 7) & 0x01;	    //技能
        $m_lucky = (($buf[1]) >> 2) & 0x01; 	//幸运
//        $m_Option = ($buf[1]) & 0x03;
//        $m_Option |=($buf[7] & 0x40) >> 4;
        $m_Option = ($buf[1] & 3)+(($buf[7] & 64)/16);//追加
        $m_Durability = $buf[2];				//耐久
        $m_NewOption = ($buf[7] & 63); 		//卓越
        $m_SetOption = $buf[8] & 15; 				//套装
        $m_ItemOptionEx = ($buf[9] & 8)*16; //380属性
        $m_PeriodItemOption = ($buf[9] & 6) >>1; //时限物品
//        $m_PeriodItemOption = (($buf[9] & 2)/2) & 1; //时限物品
        $m_IsExpiredItem = (($buf[9] & 4)/4) & 1; //经验物品
        $m_JewelOfHarmonyOption = $buf[10];  #再生强化属性
        $m_SocketOption[1] = $buf[11];     #镶嵌[1]
        $m_SocketOption[2] = $buf[12];     #镶嵌[2]
        $m_SocketOption[3] = $buf[13];     #镶嵌[3]
        $m_SocketOption[4] = $buf[14];     #镶嵌[4]
        $m_SocketOption[5] = $buf[15];     #镶嵌[5]
        $m_SocketBonusPotion = $buf[10];   #荧光
        $this->_type = $m_type;
        $item = [
            'type'                  =>  $m_type,                #总编码
            'index'                 =>  $m_index,               #小编吗
            'level'                 =>  $m_level,               #等级
            'skill'                 =>  $m_skill,               #技能
            'lucky'                 =>  $m_lucky,               #幸运
            'option'                =>  $m_Option,              #追加
            'durability'            =>  $m_Durability,          #耐久
            'section'               =>  $m_Section,             #大编码
            'setOption'             =>  $m_SetOption,           #套装
            'newOption'             =>  $m_NewOption,           #卓越
            'itemOptionEx'          =>  $m_ItemOptionEx,        #380
            'jewelOfHarmonyOption'  =>  $m_JewelOfHarmonyOption,#再生强化属性
            'socketOption'          =>  $m_SocketOption,        #镶嵌[0-4]
            'socketBonusPotion'     =>  $m_SocketBonusPotion,   #荧光
            'serial1'               =>  hexdec($this->serial1),
            'serial2'               =>  hexdec($this->serial2),
            'periodItemOption'      =>  $m_PeriodItemOption,    #时限
            'expiredItem'           =>  $m_IsExpiredItem,    #时限
            'vip'                   =>  $this->IsVIP,
        ];

        $this->itemInfo = $item;

        $this->Excellent =$buf[7];
        if(strlen($ItemData) == 20){
            if(($this->Excellent & 128) == 128){
                $this->Excellent -= 128;
            }
        } else{
            if($this->Excellent >= 128){
                $this->Excellent -= 128;
            }
        }
        #统计卓越数量
        $this->getExcCount();

        #全局物品信息
        $this->itemOption = $this->getItem($m_Section,$m_index);
        #镶嵌物品
        if ($this->serverFiles == "igcn"){
            if(isset($this->itemOption['Type']) && $this->itemOption['Type'] == 2) $this->IsSocket = true;
        }else{
            if(isset($this->itemOption['DropLevel']) && $this->itemOption['DropLevel'] == 380) $this->IsSocket = true;
        }

        #自定义镶嵌(大编小,小编编号)

        if($this->version && $this->serverFiles == 'egames'){
            $socketDIV = $this->localServerTXT('SocketType.txt');
            if(is_array($socketDIV)){
                foreach ($socketDIV[1] AS $key=>$t){
                    if(!$key) continue;
                    if($item['section'] == $t[0]  && $item['index'] == $t[1]) $this->IsSocket = true;
                }
            }
        }
        #是否套装
        if($m_SetOption) $this->IsSet = true;
        #验证是否为镶嵌物品
        if($this->IsSocket) {
            $item['jewelOfHarmonyOption'] = 0;
            $item['itemOptionEx'] = 0;
            if($this->serverFiles == 'igcn') $item['newOption'] = 0;
        }
        #380
        if ($m_ItemOptionEx) $this->Is380Item = true;
        #卷轴
        if($this->itemOption['Slot'] == 236) $this->IsPentagram = true;
        #耳环
        if($this->itemOption['Slot'] == 237 || $this->itemOption['Slot'] == 238) $this->IsEarring = true;
        #时限物品
        if (($m_NewOption & 0x7F)!= 0) $this->IsExtItem  = true;
        #4代翅膀
        if($this->itemOption['Slot'] == 7) $this->IsWing = true;
        if($this->itemOption['Slot'] == 7 && $this->itemOption['KindB'] == 76) $this->Is4thWing = true;
        if($this->itemOption['Slot'] == '-1') $this->IsNotItems = true;
        if(!$this->IsNotItems){if($item['newOption']) $this->IsExc = true;}
        if ($this->checkItemIsJewel($item['type'])) $this->IsJewel = 1;
        return $item;
    }

    public function ItemByteConvert($item)
    {
        for ($i = 0;$i<16;$i++){
            $lpMsg[$i] = 255;
        }
        $lpMsg[0] = $item['index'] & 0xFF;
        $lpMsg[1] = 0;
        $lpMsg[1]|= $item['level'] * 8;
        $lpMsg[1]|= $item['skill'] * 128;
        $lpMsg[1]|= $item['lucky'] * 4;
        $lpMsg[1]|= $item['option'] & 3;
        $lpMsg[2] = $item['durability'];
        $lpMsg[3] = 0;
        $lpMsg[3]|= ($item['index'] & 256) >> 1;
        $lpMsg[3]|= (($item['option'] >3)?64:0);
        $lpMsg[3]|= $item['newOption'];
        $lpMsg[4] = $item['setOption'];
        $lpMsg[5] = 0;
        $lpMsg[5]|= $item['index'] & $this->GET_ITEM(15,0) >> 5;
        $lpMsg[5]|= ($item['itemOptionEx'] & 128) >> 4;
        $lpMsg[5]|= ($item['periodItemOption'] & 1) << 1;
        $lpMsg[5]|= ($item['expiredItem'] & 1) << 2;
        for ($i = 0;$i<16;$i++){
            $lpMsg[$i] = str_pad(strtoupper(dechex($lpMsg[$i])),2,0,STR_PAD_LEFT);
        }
        return implode("",$lpMsg);
    }

    /**
     * 分拆背包代码到数组
     * @param $equipment
     * @return string|void
     */
    public function setEquipmentCode($equipment){
        if (!preg_match('/[a-fA-F0-9]/',$equipment)) return '物品解析错误!';
        if(!check_value($equipment)) return;
        $Data = str_split($equipment, ITEM_SIZE);
        return str_ireplace(str_pad("F",ITEM_SIZE,"F"),'',$Data); //给空物品赋值
    }

    /**
     * 获取图片链接
     * @param $ItemData
     * @return string
     */
    public function ItemsUrl($ItemData){
        $Url = '';
        $empty = __PATH_PUBLIC__.'items/empty.gif';
        if($ItemData){
            $dataInv = str_split($ItemData, 2); //划切2
            $i = 0;
            while($i<=16){//放进数组
                $item[$i] = hexdec($dataInv[$i]); //16进10
                $i++;
            }
            if (empty($item)) return $Url;

            $m_Section = (0xF0 & $item[9]) <<5;
            $m_Section = $m_Section / 512;			//分类
            $m_index = $item[0] % 256;
            $m_index |= ($item[7] & 0x80) * 2;		//序号
            $file = __ROOT_DIR__.'public/items/'.$m_Section.'/'.$m_index.'.gif';
            if(file_exists($file)){
                $Url = __PATH_PUBLIC__.'items/'.$m_Section.'/'.$m_index.'.gif';
            }else{
                $Url = $empty;
            }
        }
        return $Url;
    }

    /**
     * 输出物品
     * @param $itemData
     * @return string
     * @throws Exception
     */
    public function printItems($itemData)
    {
        if (!preg_match('/[0-9]/',$itemData)) return '物品解析错误!';
        if(!$itemData) return null;
        $result = '';
        #解算
        $item = $this->convertItem($itemData);
		
		//return  json_encode($this->itemOption);
		// return  json_encode($item);
		
        if($item['type'] != 255){
            $result.= '<div class="ItemData Items">';
            $SocketClass = $ExcellentClass = $SetClass = '';
            if($this->config['active_soc']) $SocketClass = ($this->IsSocket) ? 'SocketItemName ' : '';
            if($this->config['active_exc']) $ExcellentClass = ($item['newOption'] ? 'ExcItemName ' : '');
            if($this->config['active_set']) $SetClass = ($item['setOption'] ? 'SetItemName ' : '');
            $exclude  = ($this->IsWing ? 'text-white': '');
            #套装还是卓越
            $type = $this->getExcellentOrSetOption($item['section'],$item['index'],$item['setOption'],$item['newOption']);
            #物品名称
            $Name= $this->getItemName($item['section'],$item['index'],$item['level']);
            #等级
            $Level= $this->getItemLevel($item['level']);
			
			$this->l_=$Level;
			

            #物品名称开始
            $result.= '<div class="mb-1 '.$SetClass.$ExcellentClass.$SocketClass.$exclude.'">'.$type.$Name.$Level.'</div>';

            #耐久
            $result.= $this->getDurability($item['level'],$item['durability']);
       
	   
            #特殊属性
            if($this->version && $this->serverFiles == 'egames') {
                $result .= $this->getItemAddOption($item['section'], $item['index'], $item['itemOptionEx']);
                if($this->version == 2) $result .= $this->getCustomAbsoluteBufferEX($item['section'], $item['index'], $item['level']);
                if($this->version == 2) $result .= $this->getItemCompleteSetUpEX($item['section'], $item['index'], $item['level']);
                if($this->version == 2) $result .= $this->getCustomItemBuffer($item['section'], $item['index'], $item['setOption']);
                if($this->version == 2) $result .= $this->getCustomItemBufferEX($item['section'], $item['index'], $item['setOption']);
				
			
            }

            #需求
            if($this->config['active_class']) $result.= $this->getClassReq();

            #守护宝石PvP
            $result.= $this->get380PvPOption($item['ItemOptionEx']);
            #再生强化
            $result.= $this->getPvPOption($item['type'],$item['jewelOfHarmonyOption']);
            #技能
            $result.=$this->getSkillName($item['skill']);
			
            #套装+体
            if($this->serverFiles !== 'igcn'){
                if(!$this->IsSocket){
                    if($item['setOption']) $result.= $item['setOption'] == 5 || $item['setOption'] == 6 ? "<div class='text-primary'>体力 +5</div>" : "<div class='text-primary'>体力 +10</div>";
                }
            }
			
            #幸运
            $result.=$this->getLucky($item['lucky']);
			
            #生命追加属性
            $result.=$this->getOption($item['section'],$item['index'],$item['option']);
		
            #荧光石!
            if($this->IsNotItems) $result.=$this->getSocketStone($item['level']);
            #卓越属性
			
            if($this->config['active_exc']) $result.=$this->getExcellentOptions($item['newOption'],$item['socketOption'],$item['socketBonusPotion']);
				
            #套装属性
			if($this->config['active_set']) $result.=$this->getSetOptionText($item['section'],$item['index'],$item['setOption']);
			
            #特殊属性显示
            if($this->config['active_too']) $result.=$this->getTooName($item['type'],$item['setOption'],$item['level']);
			
            #镶嵌物品  几孔
            if($this->config['active_soc']) $result.=$this->getSocketOption($item['setOption'],$item['socketOption'],$item['socketBonusPotion']);
            #识别码1
            if($this->config['active_sel']) $result.= vsprintf('<div class="serial">识别码[%s]:%s</div>',[1,$item['serial1']]);
            #识别码2
            if($this->config['active_sel']) if($this->serverFiles =='igcn') $result.= vsprintf('<div class="serial">识别码[%s]:%s</div>',[2,$item['serial2']]);
            #时限
            $result.=$this->getPeriodItemOption($item['periodItemOption']);
            $result.='</div>';
        }

		
		
        return $result;

    }

    /**
     * 读xml
     * @param $xmlPath
     * @return mixed|SimpleXMLElement|string
     */
    public function getXml($xmlPath)
    {
        $xml = '';
        if (file_exists($xmlPath)) {
            $xml = simplexml_load_file($xmlPath);
        }
        $xmlJson= json_encode($xml);
        $xml=json_decode($xmlJson,true);

        return $xml;
    }

    /**
     * 获取物品信息
     * @param $section
     * @param $index
     * @return null
     * @throws Exception
     */
    public function getItem($section, $index)
    {
        if(!Validator::Number($section)) throw new Exception('无法识别的物品，请联系在线客服！');
        if(!Validator::Number($index)) throw new Exception('无法识别的物品，请联系在线客服！');

        if ($this->version && $this->serverFiles == 'egames'){
            if(!$this->parse_item_txt()) throw new Exception('[1]物品配置文件解析失败，请联系在线客服。');
            if(!is_array($this->info)) throw new Exception('无法识别的物品，请联系在线客服！');
            if(!array_key_exists($section,$this->info)) throw new Exception('无法识别的物品，请联系在线客服！');
            if(!array_key_exists($index,$this->info[$section])) throw new Exception('无法识别的物品，请联系在线客服！');
            return $this->info[$section][$index];
        }

        $xml = $this->ParseXml($section);
        $json = json_encode($xml);
        $item = json_decode($json,true);
        foreach ($item as $attribute){
            $attribute = $attribute['@attributes'];
            if($index == $attribute['Index']){
                return $attribute;
            }
        }

        return null;
    }

    /**
     * 读取itemList.xml
     * @param $category
     * @return SimpleXMLElement[]|string
     */
    public function ParseXml($category)
    {
        static $ItemList = null;
        if (file_exists($this->itemListFile)) {
            libxml_use_internal_errors(true);
            if ($ItemList == null) $ItemList = simplexml_load_file($this->itemListFile);
            if ($ItemList === false) {
                $error_line = '';
                foreach (libxml_get_errors() as $error) {
                    $error_line .= $error->message . ', 文件' . $error->file .',有一个致命错误在: ' . $error->line . '行<br>';
                }
                echo ('[文件解析] 无法解析xml: ' . $error_line . ' !system_error');
                return 'error';
            }
            $list = $ItemList->xpath("//ItemList/Section[@Index='" . $category . "']/Item");
            if(empty($list)) return 'error';
            return $list;
        }
        return 'error';
    }

    /**
     * 自定义380属性
     * @param $section
     * @param $index
     * @param $PvPOption
     * @return string
     * @throws Exception
     */
    public function getItemAddOption($section, $index, $PvPOption)
    {
        $temp = '';
        $ItemAddOptionText = $this->localServerTXT('ItemAddOption.txt');
        if(!is_array($ItemAddOptionText)) return $temp;
        foreach ($ItemAddOptionText[0] as $addKey => $item){
            if($addKey == 0) continue;
            if ($section == $item[0] && $index == $item[1]) {
                if ($section <=11){
                    $xml = $this->getXml($this->itemAddOptionFile);
                    if(!is_array($xml)) return $temp;
                    foreach ($xml['Option'] as $key=>$data){
                        $data = $data['@attributes'];
                        if($item[2] == $data['Index']){
                            if ($PvPOption>0) $temp.= '<div class="AddOption">'.sprintf($data['Name'],$item[3]).'</div>';
                        }
                        if($item[4] == $data['Index']){
                            if ($PvPOption>0) $temp.= '<div class="AddOption">'.sprintf($data['Name'],$item[5]).'</div>';
                        }
                    }
                }else{
                    $xml = $this->getXml($this->itemOptionTextFile);
                    if(!is_array($xml)) return $temp;
                    foreach ($xml['Option'] as $key=>$data){
                        $data = $data['@attributes'];
                        if($item[2] == $data['Index']){
                            if ($PvPOption>0) $temp.= '<div class="CustomItem">'.sprintf($data['Name'],$item[3]).'</div>';
                        }
                        if($item[4] == $data['Index']){
                            if ($PvPOption>0) $temp.= '<div class="CustomItem">'.sprintf($data['Name'],$item[5]).'</div>';
                        }
                    }
                }

            }
        }
        return $temp;
    }

    /**
     * @param $section
     * @param $index
     * @param $level
     * @return string
     * @throws Exception
     */
    public function getCustomAbsoluteBufferEX($section, $index, $level)
    {
        $title = '';
        $temp = '';
        $CustomAbsoluteBufferEX     = $this->localServerTXT('CustomAbsoluteBufferEX.txt');
        if(!is_array($CustomAbsoluteBufferEX)) return $temp;
        if(!is_array($CustomAbsoluteBufferEX[0][1])) return $temp;
        if(!is_array($CustomAbsoluteBufferEX[1])) return $temp;
        foreach ($CustomAbsoluteBufferEX[2] as $optionKey => $item){
            if(!$optionKey) continue;
            if ($section == $item[0] && $index == $item[1]) {
                $title = '<div class="'.$this->color($CustomAbsoluteBufferEX[0][1][1]).'">'.$CustomAbsoluteBufferEX[0][1][2].'</div>';
                if ($level == $item[2]){
                    for ($v=3;$v<=18;$v++){
                        if($item[$v]){
                            foreach ($CustomAbsoluteBufferEX[1] as $tKey=>$text){
                                if(!$tKey) continue;
                                if($item[$v] == $tKey){
                                    $temp.='<div class="'.$this->color($CustomAbsoluteBufferEX[0][1][0]).'">' . sprintf($text[1],$item[$v]) .'</div>';
                                }
                            }
                        }
                    }
                }
            }
        }
        return $title.$temp;
    }

    /**
     * 附魔文本
     * @param $section
     * @param $index
     * @param $setOption
     * @return string
     * @throws Exception
     */
    public function getCustomItemBuffer($section, $index, $setOption)
    {
        $temp = '';
        $CustomItemBuffer = $this->localServerTXT('CustomItemBuffer.txt');
        if(!is_array($CustomItemBuffer)) return $temp;
        foreach ($CustomItemBuffer[0] as $tKey => $item){
            if(!$tKey) continue;
            if ($section == $item[0] && $index == $item[1]){
                if($item[3] || $item[4]){
                    $newOption1[] = [
                        'Color'  => $item[2],
                        'Excellent'  => $item[3],
                        'Set'  => $item[4],
                        'Description'  => $item[5],
                    ];
                }
            }
        }
        if(empty($newOption1)) return $temp;
        #验证卓越
        foreach ($newOption1 as $key=>$data){
            if ($data['Excellent'] > 0 ){
                if($this->exe_count){
                    if($data['Excellent'] == 1){
                        $newOption2[$key] = $data;
                    }
                    else if($data['Excellent'] >1){
                        if($this->exe_count >= $data['Excellent']) $newOption2[$key] = $data;
                    }
                }
            } else{
                $newOption3[$key] = $data;
            }
        }
        if(empty($newOption2)) return $temp;

        #验证套装
        $set = 0;
        if($setOption == 5 || $setOption == 9) $set = 5;//老套装
        if($setOption == 6 || $setOption == 10) $set = 6;//新他在
        foreach ($newOption2 as $key=>$data){
            if ($data['Set'] > 0 ){
                if($setOption){
                    if($data['Set'] == 1){
                        $newOption3[$key] = $data;
                    }
                    else if($data['Set'] >1){
                        if($set == $data['Set']) $newOption3[$key] = $data;
                    }
                }
            } else{
                $newOption3[$key] = $data;
            }
        }
        if(empty($newOption3)) return $temp;

        foreach ($newOption3 as $data){
            $temp.= '<div class="'.$this->color($data['Color']).'">'.$data['Description'].'</div>';
        }
        return $temp;
    }

    /**
     * 附魔文本
     * @param $section
     * @param $index
     * @param $setOption
     * @return string
     * @throws Exception
     */
    public function getCustomItemBufferEX($section, $index, $setOption)
    {
        $temp = '';
        $CustomItemBufferEX = $this->localServerTXT('CustomItemBufferEX.txt');
        $CustomItemBufferEXText = $this->localServerTXT('TEXT_CustomItemBufferEX.txt');
        if(!is_array($CustomItemBufferEX[0])) return $temp;
        if(!is_array($CustomItemBufferEXText)) return $temp;
        foreach ($CustomItemBufferEX[0] as $tKey=>$item){
            if (!$tKey) continue;
            if ($section == $item[0] && $index == $item[1]){
                $newOption1[] = [
                    'Option' => $item[2],
                    'Excellent' => $item[3],
                    'Set' => $item[4],
                    'Rate' => $item[5],
                    'VIP' => $item[6],
                ];
            }
        }
        if(empty($newOption1)) return $temp;
        #验证卓越
        foreach ($newOption1 as $key=>$data){
            if ($data['Excellent'] > 0 ){
                if($this->exe_count){
                    if($this->exe_count <= $data['Excellent']){
                        $newOption2[$key] = $data;
                    }elseif($data['Excellent'] == 1){
                        $newOption3[$key] = $data;
                    }
                }
            } else{
                $newOption2[$key] = $data;
            }
        }
        if(empty($newOption2)) return $temp;
        #验证套装
        $set = 5;
        if($setOption == 5 || $setOption == 9) $set = 5;
        if($setOption == 6 || $setOption == 10) $set = 6;
        foreach ($newOption2 as $key=>$data){
            if ($data['Set'] > 0 ){
                if($setOption){
                    if($set == $data['Set']){
                        $newOption3[$key] = $data;
                    }elseif($data['Set'] == 1){
                        $newOption3[$key] = $data;
                    }
                }
            } else{
                $newOption3[$key] = $data;
            }
        }
        if(empty($newOption3)) return $temp;

        #验证VIP
        foreach ($newOption3 as $key=>$data){
            if($data['VIP']){
                if($this->IsVIP){
                    $newOption4[$key] = $data;
                }
            }else{
                $newOption4[$key] = $data;
            }
        }
        if(empty($newOption4)) return $temp;
        foreach ($newOption4 as $data){
            foreach ($CustomItemBufferEXText[0] as $tKey=>$text){
                if(!$tKey) continue;
                if($data['Option'] == $text[0]){
                    $temp.= '<div class="CustomItemBufferEX">'.sprintf($text[1],$data['Rate']).'</div>';
                }
            }
        }

        return $temp;
    }

    /**
     * @param $section
     * @param $index
     * @return array
     * @throws Exception
     */
    public function getCustomExcellentOption($section, $index)
    {
        $temp = [];
        $CustomExcellentOption = $this->localServerTXT('CustomExcellentOption.txt');
        if(!is_array($CustomExcellentOption)) return $temp;
        foreach ($CustomExcellentOption[0] as $tKey=>$item){
            if(!$tKey) continue;
            if($section == $item[0] && $index == $item[1]) {
                $temp = [
                        1 => $item[2],
                        2 => $item[3],
                        3 => $item[4],
                    ];
            }
        }
        return $temp;
    }

    /**
     * @param $section
     * @param $index
     * @return array
     * @throws Exception
     */
    public function getCustomWingBuffer($section, $index)
    {
        $temp = [];
        $CustomWingBuffer = $this->localServerTXT('CustomWingBuffer.txt');
        $CustomWingBufferTEXT = $this->localServerTXT('TEXT_CustomWingBuffer.txt');
        if(!is_array($CustomWingBuffer)) return $temp;
        if(!is_array($CustomWingBufferTEXT)) return $temp;
        foreach ($CustomWingBuffer[0] as $kKey => $item){
            if(!$kKey) continue;
            if($section == $item[0] && $index == $item[1]) {
                if(!array_key_exists($item[3],$CustomWingBufferTEXT[0])) continue;
                $temp[$item[2]] = [
                    'Rate'     => $item[4],
                    'Name'     => $CustomWingBufferTEXT[0][$item[3]][1]
                ];
            }
        }
        return $temp;
    }

    /**
     * 4代翅膀二级属性
     * @param $section
     * @param $index
     * @param $level
     * @return string
     * @throws Exception
     */
    public function getCustomWingBufferEX($section, $index, $level)
    {
        $temp = '';
        $title = '';
        $CustomWingBuffer_LV2 = $this->localServerTXT('CustomWingBuffer_LV2.txt');

        if(!is_array($CustomWingBuffer_LV2)) return $temp;
        if(!is_array($CustomWingBuffer_LV2[0])) return $temp;
        if(!is_array($CustomWingBuffer_LV2[1])) return $temp;
        if(!is_array($CustomWingBuffer_LV2[2])) return $temp;
        if(!is_array($CustomWingBuffer_LV2[3])) return $temp;
        #找到物品
        foreach ($CustomWingBuffer_LV2[3] as $tKey => $item){
            if(!$tKey) continue;
            if($section == $item[0] && $index == $item[1]){
                $title = '<div class="'.$this->color($CustomWingBuffer_LV2[0][1][1]).'">'.$CustomWingBuffer_LV2[0][1][2].'</div>';
                if(!array_key_exists($item[2],$CustomWingBuffer_LV2[2])) return $temp;
                if($level >=0 && $level <= 15){
                    if(!array_key_exists($item[2],$CustomWingBuffer_LV2[1])) return $temp;
                    if($item[2] == $CustomWingBuffer_LV2[2][$item[2]][0]){

                        $temp.= '<div class="'.$this->color($CustomWingBuffer_LV2[0][1][0]).'">'.sprintf($CustomWingBuffer_LV2[1][$item[2]][1],$CustomWingBuffer_LV2[2][$item[2]][$level+1]).'</div>';
                    }
                }
            }
        }
        return $title.$temp;
    }

    /**
     * 整套属性增加
     * @param $section
     * @param $index
     * @param $level
     * @return string
     * @throws Exception
     */
    public function getItemCompleteSetUpEX($section, $index, $level)
    {
        $temp = '';
        $title = '';
        $titleColor = 0;
        $ItemCompleteSetUpEX = $this->localServerTXT('ItemCompleteSetUpEX.txt');
        if(!is_array($ItemCompleteSetUpEX)) return $temp;
        if(!is_array($ItemCompleteSetUpEX[1])) return $temp;
        if(!is_array($ItemCompleteSetUpEX[3])) return $temp;
        if ($section >= 7 && $section <= 11){
            foreach ($ItemCompleteSetUpEX[0] as $kKey=>$item){
                if(!$kKey) continue;
                $newData = [
                    'Option_1' => $item[2],
                    'Option_2' => $item[3],
                    'Option_3' => $item[4],
                    'Option_4' => $item[5],
                    'Option_5' => $item[6],
                    'Option_6' => $item[7],
                    'Option_7' => $item[8],
                    'Option_8' => $item[9],
                    'Option_9' => $item[10],
                    'Option_10' => $item[11],
                    'Option_11' => $item[12],
                    'Option_12' => $item[13],
                ];
                if($level == $item[1]) {
                    if ($index == $item[0] || '-1' == $item[0]) {
                        if ($item[1] >= 10) {
                            if ($item[1] == 10) $titleColor = $ItemCompleteSetUpEX[1][1][0];
                            if ($item[1] == 11) $titleColor = $ItemCompleteSetUpEX[1][1][1];
                            if ($item[1] == 12) $titleColor = $ItemCompleteSetUpEX[1][1][2];
                            if ($item[1] == 13) $titleColor = $ItemCompleteSetUpEX[1][1][3];
                            if ($item[1] == 14) $titleColor = $ItemCompleteSetUpEX[1][1][4];
                            if ($item[1] == 15) $titleColor = $ItemCompleteSetUpEX[1][1][5];
                        }
                        for ($u = 1; $u <= 12; $u++) {
                            if ($newData['Option_'.$u]) {
                                if (array_key_exists($u, $ItemCompleteSetUpEX[3])) {
                                    $title = '<div class="' . $this->color($ItemCompleteSetUpEX[1][1][6]) . '">' . $ItemCompleteSetUpEX[1][1][7] . '</div>';
                                    $temp .= '<div class="'.$this->color($titleColor).'">' . sprintf($ItemCompleteSetUpEX[3][$u][1], $newData['Option_' . $u]) . '</div>';
                                }
                            }
                        }
                    }
                }
            }
        }
        return $title.$temp;
    }

    /**
     * 附魔字体颜色
     * @param $color
     * @return mixed
     */
    private function color($color){
        $style = [
            0   => 'Custom0 ',#白色
            1   => 'Custom1 ',#蓝色
            2   => 'Custom2 ',#红色
            3   => 'Custom3 ',#金色
            4   => 'Custom4 ',#绿色
            5   => 'Custom5 ',#红底白字
            6   => 'Custom6 ',#粉红色
            7   => 'Custom7 ',#白底蓝字
            8   => 'Custom8 ',#土黄色底白字
            9   => 'Custom9 ',#浅蓝底绿字
            10  => 'Custom10',#灰色
            11  => 'Custom11',#紫色
            12  => 'Custom12',#紫色
            13  => 'Custom13',#棕色
            14  => 'Custom14',#紫色
        ];
        return $style[$color];
    }

    /**
     * 读取Item.txt物品文件
     * @return bool
     */
    public function parse_item_txt()
    {
        static $file_data = null;
        static $items = [];
        if($this->check_file('item(Kor).txt')){
            $keys = [];
            $keys[0] = ["Index",	"Slot",	"SkillIndex",	"Width",	"Height",	"Serial",	"Option",	"Drop",	"Name",	"DropLevel",	"DamageMin",	"DamageMax",	"AttackSpeed",	"Durability",	"MagicDurability",	"MagicPower",	"ReqLevel",	"ReqStrength",	"ReqDexterity",	"ReqEnergy",	"ReqVitality",	"ReqCommand",	"SetAttrib",	"DarkWizard",	"DarkKnight",	"FairyElf",	"MagicGladiator",	"DarkLord",	"Summoner",	"RageFighter"];
            $keys[1] = $keys[0];
            $keys[2] = $keys[0];
            $keys[3] = $keys[0];
            $keys[4] = $keys[0];
            $keys[5] = $keys[0];
            $keys[6] = ["Index",	"Slot",	"SkillIndex",	"Width",	"Height",	"Serial",	"Option",	"Drop",	"Name",	"DropLevel",	"Defense",	"SuccessfulBlocking",	"Durability",	"ReqLevel",    "ReqStrength",	"ReqDexterity",	"ReqEnergy",	"ReqVitality",	"ReqCommand",	"SetAttrib",	"DarkWizard",	"DarkKnight",	"FairyElf",	"MagicGladiator",	"DarkLord",	"Summoner",	"RageFighter"];
            $keys[7] = $keys[6];
            $keys[8] = $keys[6];
            $keys[9] = $keys[6];
            $keys[10] = $keys[6];
            $keys[11] = $keys[6];
            $keys[12] = ["Index",	"Slot",	"SkillIndex",	"Width",	"Height",	"Serial",	"Option",	"Drop",	"Name",	"DropLevel",	"Defense",	"Durability",	"ReqLevel",	"ReqEnergy",	"ReqStrength",	"ReqDexterity",	"ReqCommand",	"Money",	"DarkWizard",	"DarkKnight",	"FairyElf",	"MagicGladiator",	"DarkLord",	"Summoner",	"RageFighter"];
            $keys[13] = ["Index",	"Slot",	"SkillIndex",	"Width",	"Height",	"Serial",	"Option",	"Drop",	"Name",	"DropLevel",	"Durability",	"IceRes",	"PoisonRes",	"LightRes",	"FireRes",	"EarthRes",	"WindRes",	"WaterRes", "SetAttrib",	"DarkWizard",	"DarkKnight",	"FairyElf",	"MagicGladiator",	"DarkLord",	"Summoner",	"RageFighter"];
            $keys[14] = ["Index",	"Slot",	"SkillIndex",	"Width",	"Height",	"Serial",	"Option",	"Drop",	"Name",	"Value",	"DropLevel"];
            $keys[15] = ["Index",	"Slot",	"SkillIndex",	"Width",	"Height",	"Serial",	"Option",	"Drop",	"Name",	"DropLevel",	"ReqLev",	"ReqEng",	"Zen",	"DarkWizard",	"DarkKnight",	"FairyElf",	"MagicGladiator",	"DarkLord",	"Summoner",	"RageFighter"];
            if($file_data == null){
                ini_set("auto_detect_line_endings", true);
                $file_data = file($this->file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
            }
            if(empty($items)){
                $type = 0;
                foreach($file_data AS $line){
                    $line = get_gb_to_utf8($line);#转码
                    if(is_numeric(trim(substr($line, 0, 1))) && strlen(trim($line)) <= 15){
                        $type = (int)trim($line);
                        continue;
                    }
                    if(preg_match('/([0-9\*]+)[\s]+([0-9\-\*]+)[\s]+([0-9\*]+)[\s]+([0-9\*]+)[\s]+([0-9\*]+)[\s]+([0-9\*]+)[\s]+([0-9\*]+)[\s]+([0-9\*]+)[\s]+"(.*)"[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})[\s]{0,}([0-9\*]{0,})$/u', $line, $match)){
                        unset($match[0]);
                        foreach($match AS $k => $v){
                            if(isset($keys[$type][$k - 1])){
                                $items[$type][$match[1]][$keys[$type][$k - 1]] = $v;
                            }
                        }
                    }
                }
            }
            $this->info = $items;
            return true;
        }
        return false;
    }

    /**
     * 加载服务端TXT配置文件
     * @param $file
     * @param bool $offset
     * @return array|void
     */
    public function localServerTXT($file,$offset=false)
    {
        $a = 0;
        $content = [];
        if(!$this->check_file($file)) return;
        ini_set("auto_detect_line_endings", true);
        $data = file($this->file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
        foreach ($data AS $key => $line) {
            //转码
            $line1 = get_gb_to_utf8($line);
            //过滤掉带有'/'后面的字符
            $line2 = str_replace(strstr($line1, '/'), null, $line1);
            //替换多余的tab键与空格
            $line3 = str_replace("\t\t", "\t", $line2);
            $line3 = str_replace("  ", " ", $line3);
            $line3 = str_replace("\tend", "end", $line3);
            $line3 = str_replace("end\t", "end", $line3);
            $line3 = str_replace("end ", "end", $line3);
            $line3 = str_replace(" end", "end", $line3);
            $line3 = str_replace('"', "", $line3);
            //过滤多余的空行
            if (trim($line3) == '') continue;
            //区分换行符打散到数组
            $line4 = (explode("\t", $line3));
            //赋值标头
            if(count($line4) == 1) if(is_numeric($line4[0])) $a = $line4[0];
            //重新定义数组键名(过滤掉空格);
            $content[$a][] = array_values(array_filter($line4, function($value){if((@count($value) > 0 && !@empty($value) && @isset($value)) || $value == '0') return 1; else return 0;}));

            foreach($content[$a] as $bKey=>$bTemp){
                if(!is_array($bTemp)) continue;
                //删除标头
                if($bKey == 0) if(count($content[$a][0]) == 1) if($offset) unset($content[$a][0]);
                if($bTemp == "end") unset($content[$a]);
                foreach ($bTemp as $cTemp){
                    $dTemp = (str_replace(" ",null,$cTemp));
                    if($dTemp == "end") unset($content[$a][$bKey]);
                }
            }

            if ($line3 == "end") $a++;
        }
        return $content;
    }

    /**
     * 检查txt文件是否存在
     * @param $file
     * @return bool
     */
    private function check_file($file)
    {
        $this->file = __PATH_ITEMS_FILE__.'txt/'.$file;
        if(is_file($this->file)) {
            return true;
        }
        return false;
    }


    /**
     * 判断套装还是卓越
     * @param $section
     * @param $index
     * @param $setOption
     * @param $newOption
     * @return string
     * @throws Exception
     */
    private function getExcellentOrSetOption($section, $index, $setOption, $newOption)
    {
        $temp= '';
        #Slot: 装备类型说明:
        # -1:不适用,0:左手,1:右手,2:头盔,3:铠甲,4:护腿,5:护手,6:靴子,7:翅膀,8:帮（宠物),9:项链,10:左戒指,11:右戒指,236:元素卷轴,237:耳环,238:耳环
        if(($this->itemOption['Slot'] >= 0 && $this->itemOption['Slot'] <=6) || ($this->itemOption['Slot'] >= 9 && $this->itemOption['Slot'] <=11))
        {
            if($setOption){
                if($this->config['active_set']) $temp.= $this->getSetName($section,$index,$setOption);
            }
            if($newOption){
                if($this->config['active_exc']) $temp.= '卓越的 ';
            }
            if($this->IsWing){
                $temp = '';
            }
        }
        return $temp;
    }

    /**
     * 获取装备名称
     * @param $section
     * @param $index
     * @param $level
     * @return SimpleXMLElement|void
     */
    public function getItemName($section,$index,$level)
    {
        #获取物品名称
        $name = $this->itemOption['Name'];

        #获取名称是否有二次定义
        $xmlPathLv =  $this->itemLevelToopFile;
        $xmlLv = $this->getXml($xmlPathLv);
        foreach ($xmlLv['LevelToop'] as $item2Temp) {
            $item2Temp = $item2Temp['@attributes'];
            if ($item2Temp['ItemType'] == $section && $item2Temp['ItemIndex'] == $index)
            {
                if($item2Temp['Level'] == $level){
                    $this->levelName2 = true;
                    $name = $item2Temp['Name'];
                }
            }
        }
        return $name;
    }

    /**
     * 获取物品等级
     * @param $m_level
     * @return string|void
     */
    public function getItemLevel($m_level)
    {
        if($this->itemOption['Slot'] == '-1') return;       #非装备
        if($m_level > 15) return;                           #异常
//        if($this->_type == 2063 || $this->_type == 2055) return;  #箭袋
//        if($this->_type == 6676 && $m_level == 3) return;         #旗帜
//        if($this->_type == 6693 && $m_level) return;              #金狼
        if($this->levelName2) return;
        if($m_level) return ' +'. $m_level;                 #正常
    }

    /**
     * 获取耐久力
     * @param $level
     * @param $durability
     * @return string|null
     * @throws Exception
     */
    private function getDurability($level,$durability){
        if($durability){
            $dur = 0;
            if (isset($this->itemOption['Durability']) && $this->itemOption['Durability']){
                $dur = $this->itemOption['Durability'];
            }
            if(isset($this->itemOption['MagicDurability']) && $this->itemOption['MagicDurability']){
                $dur = $this->itemOption['MagicDurability'];
            }
            if($level <= 4)
                $dur = $dur + ($level * 1);
            if($level < 10 && $level > 4)
                $dur = $dur + (($level - 2) * 2);
            if($level == 10)
                $dur = $dur + (($level * 2) - 3);
            if($level == 11)
                $dur = $dur + (($level * 2) - 1);
            if($level == 12)
                $dur = $dur + (($level + 1) * 2);
            if($level == 13)
                $dur = $dur + (($level + 3) * 2);
            if($level == 14)
                $dur = $dur + (($level * 2) + 11);
            if($level == 15)
                $dur = $dur + (($level * 2) + 17);
            if($this->Excellent > 0 && $this->IsSet){
                $dur += 20;
            } else if($this->Excellent <= 0 && $this->IsSet){
                $dur += 20;
            } else if($this->Excellent > 0 && $this->IsSet){
                $dur += 15;
            }
            #Slot: 装备类型说明:	 -1:不适用,0:左手,1:右手,2:头盔,3:铠甲,4:护腿,5:护手,6:靴子,7:翅膀,8:帮（宠物),9:项链,10:左戒指,11:右戒指,236:元素,237耳环,238耳环
            if($this->IsPentagram){
                return vsprintf('<div class="ItemDurability">插槽数:[%s]</div>',[$durability]);
            }else{
                if($this->IsNotItems) return null;
                if(!$dur){
                    return vsprintf('<div class="ItemDurability">数量:[%s]</div>',[$durability]);
                }
                return vsprintf('<div class="ItemDurability">耐久力:[%s/%s]</div>',[$durability,$dur]);
            }
        }
        return null;
    }

    /**
     * 获取技能名称
     * @param $skill
     * @return string|null
     * @throws Exception
     */
    private function getSkillName($skill)
    {
        if($skill>0){
            if($this->version && $this->serverFiles == 'egames'){
                $skillFile = $this->localServerTXT('skill(Kor).txt');
                if(!is_array($skillFile)) return null;
                //炎狼兽
                if($this->itemInfo['type'] == 6693) return '<div class="skillNameText">['.$skillFile[0][69][1].']</div>';
                //黑王马
                if($this->itemInfo['type'] == 6660) return '<div class="skillNameText">['.$skillFile[0][55][1].']</div>';

                foreach ($skillFile[0] as $key => $item){
                    if ($item == $this->itemOption['SkillIndex']) return '<div class="skillNameText">'.$item[1].'</div>';
                }

            }
            #解析技能xml
            $xml = $this->getXml($this->itemSkillListFile);
            foreach ($xml['Skill'] as $skillTemp) {
                if ($skillTemp['@attributes']['Index'] === $this->itemOption['SkillIndex']) {
                    return  '<div class="skillNameText">['.$skillTemp['@attributes']['Name'].']</div>';
                }
            }
        }
        return null;
    }

    /**
     * 获取套装名
     * @param $section
     * @param $index
     * @param $setOption
     * @return string|void
     * @throws Exception
     */
    private function getSetName($section,$index,$setOption){

        if($setOption > 0){
//            if(array_search($setOption,[5,6,9,10])) return;
            $SetOptionFull = $this->getSetOption($section,$index,$setOption);
            if(!is_array($SetOptionFull)) return;
            return '<span>'.$SetOptionFull['Name'].'</span> ';
        }
        return;
    }

    /**
     * 获取守护宝石强化属性
     * @param $itemOptionEx
     * @return string|null
     * @throws Exception
     */
    private function get380PvPOption($itemOptionEx)
    {
        if($itemOptionEx!=0){
            return '<div class="PvP380Item">380守护物品</div>';
        }
        return null;
    }

    /**
     * 获取再生强化属性
     * @param $type
     * @param $jewelOfHarmonyOption
     * @return string|null
     * @throws Exception
     */
    private function getPvPOption($type,$jewelOfHarmonyOption)
    {
        if($jewelOfHarmonyOption) {
            $GetItemStrengthenOption = ($jewelOfHarmonyOption &0xF0) >> 4;

            if($GetItemStrengthenOption != 0){

                $GetItemOptionLevel = ($jewelOfHarmonyOption & 0x0F);
                if( ($type >= ($this->GET_ITEM(0,0)) && $type <$this->GET_ITEM(4,0)) || ($type >= $this->GET_ITEM(4,0) && $type <$this->GET_ITEM(4,7)) || ($type >= $this->GET_ITEM(4,8) && $type <$this->GET_ITEM(4,15)) || ($type >= $this->GET_ITEM(4,16) && $type <$this->GET_ITEM(5,0)) ){
                    $PvPOptionIndex = 1;
                }else if($type >= $this->GET_ITEM(5,0) && $type <$this->GET_ITEM(6,0) ){
                    $PvPOptionIndex = 2;
                }else if($type >= $this->GET_ITEM(6,0) && $type <$this->GET_ITEM(12,0) ){
                    $PvPOptionIndex = 3;
                }
                $xmlPathLv = $this->itemHarmonyOptionFile;
                $xmlLv = $this->getXml($xmlPathLv);

                foreach ($xmlLv['Type'] as $PvPOptionTemp) {

                    if(empty($PvPOptionIndex)) return null;
                    if($PvPOptionIndex == $PvPOptionTemp['@attributes']['ID']){

                        foreach($PvPOptionTemp['Option'] as $Option){

                            if($GetItemStrengthenOption == $Option['@attributes']['Index']){
                                #取值
                                $harmonyValue = 0;
                                if(isset($Option['Effect'][$GetItemOptionLevel]['@attributes']['Value'.$GetItemOptionLevel])){
                                    $harmonyValue =$Option['Effect'][$GetItemOptionLevel]['@attributes']['Value'.$GetItemOptionLevel];
                                }
                                $PvPName =sprintf($Option['@attributes']['Name'],$harmonyValue);
                                return vsprintf('<div class="HarmonyOption">%s</div>',[$PvPName]);
                            }
                        }

                    }
                }

            }
        }

        return null;
    }

    /**
     * 获取幸运
     * @param $lucky
     * @return string
     * @throws Exception
     */
    private function getLucky($lucky)
    {
        if($lucky!=0){
            return '<div class="LuckyItem">幸运(灵魂宝石之成功率 +25%)</div><div class="LuckyItem">幸运(会心一击率 +5%)</div>';
        }

        return null;
    }

    /**
     * 获取物品追加属性
     * @param $section
     * @param $index
     * @param $option
     * @return string
     * @throws Exception
     */
    private function getOption($section,$index,$option)
    {
        #Slot: 装备类型说明:	 -1:不适用,0:左手,1:右手,2:头盔,3:铠甲,4:护腿,5:护手,6:靴子,7:翅膀,8:帮（宠物),9:项链,10:左戒指,11:右戒指,236:元素,237耳环,238耳环
        if($option != 0 && !$this->IsNotItems){
                #盾牌
                if($this->itemOption['Slot'] == 1){
                    if($section == 6){
                        if($this->itemOption['Defense'] >0){
                            return '<div class="ItemOption">'.vsprintf('追加防御率 +%d',[$option*5]).'</div>';
                        }
                    }
                }
                #装备
                elseif($this->itemOption['Slot'] >=2 && $this->itemOption['Slot'] <=6){
                    if($this->itemOption['Defense'] > 0){
                        return '<div class="ItemOption">'.vsprintf('追加防御力 +%d',[$option*4]).'</div>';
                    }
                }
                #翅膀
                elseif($this->itemOption['Slot'] == 7){
                    return '<div class="ItemOption">'.vsprintf('追加属性 +%d(%d%%)',[$option*4,$option]).'</div>';
                }
                #首饰
                elseif($this->itemOption['Slot'] == 9 || $this->itemOption['Slot'] == 10 || $this->itemOption['Slot'] == 11){
                    return '<div class="ItemOption">'.vsprintf('生命自动回复 +%d%%',[$option]).'</div>';
                }
                #武器
                else{
                    if(isset($this->itemOption['MagicPower']) && $this->itemOption['MagicPower'] > 0){
                        return '<div class="ItemOption">'.vsprintf('追加魔法攻击力 +%d',[$option*4]).'</div>';
                    }
                    return '<div class="ItemOption">'.vsprintf('追加攻击力 +%d',[$option*4]).'</div>';

                }
        }
        return null;
    }

    /**
     * 检查哪些是翅膀
     * @param $section
     * @param $index
     * @return int
     */
    private function IsWings($section, $index)
    {
        if ($this->IsWing){
            if (!check_value($section) || !check_value($index)) return 0;
            if ($section == 12){
                if($index >= 130 && $index == 135) return 0.5;
                if($index >= 0 && $index <= 2) return 1;
                if($index >= 3 && $index <= 6 || $index == 42 || $index == 49) return 2;
                if($index >=36 && $index <= 41|| $index == 43 || $index == 50) return 3;
                if($this->serverFiles == 'egames') if($index >=180 && $index <= 230) return 4;
                if ($this->serverFiles == 'igcn'){
                    if ($index == 269 || $index >= 422 && $index <= 429) return 2; //[绑定]
                    if ($index >= 262 && $index <= 265 || $index >= 279 && $index <= 282 || $index >= 284 && $index <= 287) return 2.5;
                    if ($index >= 430 && $index <= 436) return 3;
                    if ($index >= 266 && $index <= 268 || $index == 283) return 4.5;
                    if ($index == 270 || $index >= 414 && $index <= 421 || $index >= 437 && $index <= 445) return 4;
                }
            }
            if ($section == 13 && $index == 30) return 2;
        }

        return 0;
    }

    /**
     * 获取套装属性文本
     * @param $section
     * @param $index
     * @param $setOption
     * @return string|void
     * @throws Exception
     */
    private function getSetOptionText($section,$index,$setOption){
        $temp = '';
//        if ($this->itemOption['Type'] == 2) return;
//        if(!array_search($setOption,[5,6,9,10])) return;
        #属性说明:T10=9,T5 =5; T10=10,T5=6
        if($setOption>0){

            $temp.= '<div class="SetOption">套装物品属性信息</div>';
            $SetOptionAndName = $this->getSetOption($section,$index,$setOption);
            if(!is_array($SetOptionAndName)) return;
            $option = $this->getXml($this->itemSetOptionTextFile);
//            if($SetOptionAndName['OptIdx1_1'] != '-1') $temp.= '<div class="SetItemName">2件套效果</div>';
            foreach ($option['Set'] as $element){
                $element = $element['@attributes'];
                for ($i = 1; $i <= 6; $i++) {
                    if($SetOptionAndName['OptIdx1_'.$i] != '-1'){
                        if ($SetOptionAndName['OptIdx1_'.$i] === $element['Index']){
                            $text = ($element['Rate'] == 2) ? $SetOptionAndName['OptVal1_'.$i].'%' : $SetOptionAndName['OptVal1_'.$i];
                            $temp.= vsprintf('<div class="SetOptionText">%s+%s</div>',[$element['Name'],$text]);
                            break;
                        }
                    }
                }
                for ($i = 1; $i <= 6; $i++) {
                    if($SetOptionAndName['OptIdx2_'.$i] != '-1') {
                        if ($SetOptionAndName['OptIdx2_'.$i] === $element['Index']) {
                            $text = ($element['Rate'] == 2) ? $SetOptionAndName['OptVal2_'.$i] . '%' : $SetOptionAndName['OptVal2_'.$i];
                            $temp .= vsprintf('<div class="SetOptionText">%s+%s</div>', [$element['Name'], $text]);
                            break;
                        }
                    }
                }
                for ($i = 1; $i <= 2; $i++) {
                    if($SetOptionAndName['SpecialOptIdx'.$i] != '-1') {
                        if ($SetOptionAndName['OptIdx2_'.$i] === $element['Index']) {
                            $text = ($element['Rate'] == 2) ? $SetOptionAndName['SpecialOptVal'.$i] . '%' : $SetOptionAndName['SpecialOptVal'.$i];
                            $temp .= vsprintf('<div class="SetOptionText">%s+%s</div>', [$element['Name'], $text]);
                            break;
                        }
                    }
                }
                for ($i = 1; $i <= 9; $i++) {
                    if($SetOptionAndName['FullOptIdx'.$i] != '-1') {
                        if ($SetOptionAndName['FullOptIdx'.$i] === $element['Index']) {
                            $text = ($element['Rate'] == 2) ? $SetOptionAndName['FullOptVal'.$i] . '%' : $SetOptionAndName['FullOptVal'.$i];
                            $temp .= vsprintf('<div class="SetOptionText">%s+%s</div>', [$element['Name'], $text]);
                            break;
                        }
                    }
                }
            }
        }
		
		if(!empty($temp)){
			$this->IsSet_=true;
		}else{
			$this->IsSet_=false;
		}

        return $temp;
    }

    /**
     * 获取套装属性
     * @param $section
     * @param $index
     * @param $setOption
     * @return array|mixed|SimpleXMLElement|void
     * @throws Exception
     */
    private function getSetOption($section,$index,$setOption){
        $SetType = $this->getSetType($section,$index);
        if (!is_array($SetType)) return;
        if ($this->version && $this->serverFiles == 'egames'){
            $setOptionTxt = $this->localServerTXT('itemsetoption(Kor).txt');
            if (!is_array($setOptionTxt)) return;
            foreach ($setOptionTxt[0] AS $setItem){
                $newOpt = [
                    'Index' 	=> $setItem[0],
                    'Name'		=> $setItem[1],
                    'OptIdx1_1' => $setItem[2],
                    'OptVal1_1' => $setItem[3],
                    'OptIdx2_1' => $setItem[4],
                    'OptVal2_1' => $setItem[5],
                    'OptIdx1_2' => $setItem[6],
                    'OptVal1_2' => $setItem[7],
                    'OptIdx2_2' => $setItem[8],
                    'OptVal2_2' => $setItem[9],
                    'OptIdx1_3' => $setItem[10],
                    'OptVal1_3' => $setItem[11],
                    'OptIdx2_3' => $setItem[12],
                    'OptVal2_3' => $setItem[13],
                    'OptIdx1_4' => $setItem[14],
                    'OptVal1_4' => $setItem[15],
                    'OptIdx2_4' => $setItem[16],
                    'OptVal2_4' => $setItem[17],
                    'OptIdx1_5' => $setItem[18],
                    'OptVal1_5' => $setItem[19],
                    'OptIdx2_5' => $setItem[20],
                    'OptVal2_5' => $setItem[21],
                    'OptIdx1_6' => $setItem[22],
                    'OptVal1_6' => $setItem[23],
                    'OptIdx2_6' => $setItem[24],
                    'OptVal2_6' => $setItem[25],
                    'SpecialOptIdx1' => $setItem[26],
                    'SpecialOptVal1' => $setItem[27],
                    'SpecialOptIdx2' => $setItem[28],
                    'SpecialOptVal2' => $setItem[29],
                    'FullOptIdx1' => $setItem[30],
                    'FullOptVal1' => $setItem[31],
                    'FullOptIdx2' => $setItem[32],
                    'FullOptVal2' => $setItem[33],
                    'FullOptIdx3' => $setItem[34],
                    'FullOptVal3' => $setItem[35],
                    'FullOptIdx4' => $setItem[36],
                    'FullOptVal4' => $setItem[37],
                    'FullOptIdx5' => $setItem[38],
                    'FullOptVal5' => $setItem[39],
                ];
                if($setOption == 5 || $setOption == 9){
                    if($newOpt['Index'] == $SetType[0] || $newOpt['Index'] == $SetType[2]){
                        return $newOpt;
                    }
                }else if($setOption == 6 || $setOption == 10){
                    if($newOpt['Index'] == $SetType[1] || $newOpt['Index'] == $SetType[3]){
                        return $newOpt;
                    }
                }
            }

        }else{
            $setOptionTxt = $this->getXml($this->itemSetOptionFile);
//        if(array_search($setOption,[5,6,9,10])) return;
            if (!is_array($setOptionTxt)) return;
            foreach ($setOptionTxt['SetItem'] as $setItem){
                $setItem = $setItem['@attributes'];
                if($setOption == 5 || $setOption == 9){
                    if($setItem['Index'] == $SetType[0] || $setItem['Index'] == $SetType[2]){
                        return $setItem;
                    }
                }else if($setOption == 6 || $setOption == 10){
                    if($setItem['Index'] == $SetType[1] || $setItem['Index'] == $SetType[3]){
                        return $setItem;
                    }
                }
            }
        }
        return;
    }

    /**
     * 获取套装类型
     * @param $section
     * @param $index
     * @return array|void
     * @throws Exception
     */
    private function getSetType($section,$index){
        if ($this->version && $this->serverFiles == 'egames'){
            $SetFileType = $this->localServerTXT('itemsettype(Kor).txt');
            if (!is_array($SetFileType)) return;
            if (!array_key_exists($section,$SetFileType)) return;
            foreach ($SetFileType[$section] AS $tKey=> $Type){
                if(!$tKey) continue;
                if ($index == $Type[0]){
                    return [
                        0 => $Type[1],
                        1 => $Type[2],
                        2 => $Type[3],
                        3 => $Type[4]
                    ];
                }
            }
        }else{
            $xmlPathLv = $this->itemSetTypeFile;
            $xmlLv = $this->getXml($xmlPathLv);
            foreach ($xmlLv['Section'] as $SetTemp) {
                if ($SetTemp['@attributes']['Index'] == $section) {
                    if (isset($SetTemp['Item'])){
                        foreach ($SetTemp['Item'] as $SetIndexTemp) {
                            if ($SetIndexTemp['@attributes']['Index'] == $index) {
                                $Type = $SetIndexTemp['@attributes'];
                                return [
                                    0 => $Type['TierI'],
                                    1 => $Type['TierII'],
                                    2 => $Type['TierIII'],
                                    3 => $Type['TierIV']
                                ];
                            }
                        }
                    }
                }
            }
        }
        return;
    }

    /**
     * 职业使用要求
     * @return bool|string
     * @throws Exception
     */
    private function getClassReq()
    {
        $temp= '';
        if($this->itemOption['RuneWizard'] > 0 && $this->itemOption['DarkWizard'] > 0 && $this->itemOption['DarkKnight'] > 0){
            return $temp;
        }else{
            global $custom;
            if($this->IsNotItems) return null;
            if($this->itemOption['DarkWizard']>0){
                if(isset($custom['character_class'][0])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][0][0]]);
                }
            }
            if($this->itemOption['DarkKnight']>0){
                if(isset($custom['character_class'][16])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][16][0]]);
                }
            }
            if($this->itemOption['FairyElf']>0){
                if(isset($custom['character_class'][32])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][32][0]]);
                }
            }
            if($this->itemOption['MagicGladiator']>0){
                if(isset($custom['character_class'][48])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][48][0]]);
                }
            }
            if($this->itemOption['DarkLord']>0){
                if(isset($custom['character_class'][64])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][64][0]]);
                }
            }
            if($this->itemOption['Summoner']>0){
                if(isset($custom['character_class'][80])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][80][0]]);
                }
            }
            if ($this->itemOption['RageFighter'] > 0) {
                if(isset($custom['character_class'][96])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][96][0]]);
                }
            }
            if ($this->itemOption['GrowLancer'] > 0) {
                if(isset($custom['character_class'][112])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][112][0]]);
                }
            }
            if ($this->itemOption['RuneWizard'] > 0) {
                if(isset($custom['character_class'][128])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][128][0]]);
                }
            }
            if ($this->itemOption['Slayer'] > 0) {
                if(isset($custom['character_class'][144])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][144][0]]);
                }
            }
            if ($this->itemOption['GunCrusher'] > 0) {
                if(isset($custom['character_class'][160])) {
                    $temp .= vsprintf('<div class="UseItemReq">%s 可以使用</div>', [$custom['character_class'][160][0]]);
                }
            }
        }
        return $temp;
    }

    /**
     * @param $ExcellentOption
     * @param $socketOption
     * @param $socketBonusPotion
     */
    public function setExcellentOptions($ExcellentOption, $socketOption, $socketBonusPotion)
    {

    }

    /**
     * 获取卓越属性
     * @param $ExcellentOption
     * @param $socketOption
     * @param $socketBonusPotion
     * @return string
     * @throws Exception
     */
    private function getExcellentOptions($ExcellentOption, $socketOption, $socketBonusPotion){
        $temp = '';
        $xmlPath = $this->itemExcellentOptionsFile;
        $ExcellentOptionFile = $this->getXml($xmlPath);
        #Slot: 装备类型说明:	 -1:不适用,0:左手,1:右手,2:头盔,3:铠甲,4:护腿,5:护手,6:靴子,7:翅膀,8:帮（宠物),9:项链,10:左戒指,11:右戒指,236:元素,237耳环,238耳环
        if($this->version && $this->serverFiles == 'egames'){
            if($this->itemOption['Slot'] != '-1'){
                if($this->itemOption['Option']){
                    $Excellent = [];
                    $ExcItemArr = [];
                    #获取新是否有新值
                    $newItemValue = $this->getCustomExcellentOption($this->itemInfo['section'],$this->itemInfo['index']);
                    #武器项链
                    if(($this->itemOption['Slot'] == 0 || $this->itemOption['Slot'] == 1 || $this->itemOption['Slot'] == 9)){
                        if($this->itemInfo['section'] == 6){#盾牌
                            for ($i = 8;$i<=13;$i++){
                                if(!array_key_exists($i,(array)$ExcellentOptionFile['Common']['Option'])) continue;
                                $Excellent[(int)$ExcellentOptionFile['Common']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Common']['Option'][$i]['@attributes'];
                            }
                        }else{#武器项链
                            for ($i = 0;$i<=5;$i++){
                                if(!array_key_exists($i,(array)$ExcellentOptionFile['Common']['Option'])) continue;
                                $Excellent[(int)$ExcellentOptionFile['Common']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Common']['Option'][$i]['@attributes'];
                            }
                        }
                        if(!empty($newItemValue)){
                            $Excellent[0]['Value'] = $newItemValue[1]; #卓越攻击几率增加
                            $Excellent[1]['Value'] = $newItemValue[2]; #攻击力增加 +等级%d/20
                            $Excellent[2]['Value'] = $newItemValue[3]; #攻击力增加 + %d%%
                        }
                    }
                    #防具戒指
                    if (($this->itemOption['Slot'] >= 2 && $this->itemOption['Slot'] <= 6) || ($this->itemOption['Slot'] >= 10 && $this->itemOption['Slot'] <= 11)){
                        for ($i = 8;$i<=13;$i++){
                            if(!array_key_exists($i,(array)$ExcellentOptionFile['Common']['Option'])) continue;
                            $Excellent[(int)$ExcellentOptionFile['Common']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Common']['Option'][$i]['@attributes'];
                        }
                        if(!empty($newItemValue)){

                            $Excellent[0]['Value'] = $newItemValue[1]; #最大生命值增加
                            $Excellent[2]['Value'] = $newItemValue[2]; #伤害减少
                            $Excellent[4]['Value'] = $newItemValue[3]; #防御成功率增加
                        }
                    }

                    #翅膀
                    if ($this->itemOption['Slot'] == 7) {
                        if($this->itemInfo['section'] == 12) {
                            #二代
                            if ($this->itemInfo['index'] >= 3 && $this->itemInfo['index'] <= 6 || $this->itemInfo['Index'] == 42) {
                                for ($i = 0; $i <= 4; $i++) {
                                    if (!array_key_exists($i, (array)$ExcellentOptionFile['Wings']['Option'])) continue;
                                    $Excellent[(int)$ExcellentOptionFile['Wings']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Wings']['Option'][$i]['@attributes'];
                                }
                            }
                            #三代
                            if ($this->itemInfo['index'] >= 36 && $this->itemInfo['index'] <= 40 || $this->itemInfo['index'] == 43 || $this->itemInfo['index'] == 50) {
                                for ($i = 5; $i <= 8; $i++) {
                                    if (!array_key_exists($i, (array)$ExcellentOptionFile['Wings']['Option'])) continue;
                                    $Excellent[(int)$ExcellentOptionFile['Wings']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Wings']['Option'][$i]['@attributes'];
                                }
                            }
                            #武者披风
                            if ($this->itemInfo['index'] == 49) {
                                for ($i = 13; $i <= 15; $i++) {
                                    if (!array_key_exists($i, (array)$ExcellentOptionFile['Wings']['Option'])) continue;
                                    $Excellent[(int)$ExcellentOptionFile['Wings']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Wings']['Option'][$i]['@attributes'];
                                }
                            }

                            #自定义4D
                            if ($this->itemInfo['index'] >= 180 &&  $this->itemInfo['index'] <= 229) {
                                for ($i = 18; $i <= 21; $i++) {
                                    if (!array_key_exists($i, (array)$ExcellentOptionFile['Wings']['Option'])) continue;
                                    $Excellent[(int)$ExcellentOptionFile['Wings']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Wings']['Option'][$i]['@attributes'];
                                }
                            }

                        }

                        if($this->itemInfo['section'] == 13) {
                            #王者披风
                            if ($this->itemInfo['index'] == 30) {
                                for ($i = 9; $i <= 12; $i++) {
                                    if (!array_key_exists($i, (array)$ExcellentOptionFile['Wings']['Option'])) continue;
                                    $Excellent[(int)$ExcellentOptionFile['Wings']['Option'][$i]['@attributes']['Number']] = $ExcellentOptionFile['Wings']['Option'][$i]['@attributes'];
                                }
                            }
                        }
                        #覆盖EG扩展属性
                        if ($this->version == 2) {
                            $newWingValue = $this->getCustomWingBuffer($this->itemInfo['section'],$this->itemInfo['index']);
                            if(!empty($newWingValue)){
                                $u=2;
                                for($i=1;$i<=count($newWingValue);$i++){
                                    $Excellent[$u]['Name'] = $newWingValue[$i]['Name'];
                                    $Excellent[$u]['Value'] = $newWingValue[$i]['Rate'];
                                    $u++;
                                }
                            }
                        }
                    }

                    #解算属性
                    $exe = $this->Excellent;
					
					
                    if (!empty($Excellent)) {
						
						
						

                        foreach ($Excellent as $key => $exc) {
							
                            if (isset($this->itemOption['MagicPower']) && $this->itemOption['MagicPower']>0){
                                $ExcItemArr[$key] = $this->strReplace('%s', '魔法攻击力', $exc);
                            } else {
								 $ExcItemArr[$key] = $this->strReplace('%s', '攻击力', $exc);
                            }
                        }
						
 
                        if ($exe >= 64){
                            $exe -= 64;
                        }
                        if ($exe >= 32){
							//卓越攻击几率增加 10%
                            $temp .= vsprintf('<div class="ExcItemOption">%s</div>', [sprintf($ExcItemArr[0]['Name'], $ExcItemArr[0]['Value'])]);
                            $exe -=32;
                        }
						
                        if ($exe >= 16){
							//攻击力增加  +等级1/20
                            $temp .= vsprintf('<div class="ExcItemOption">%s</div>', [sprintf($ExcItemArr[1]['Name'], $ExcItemArr[1]['Value'])]);
                            $exe -=16;
                        }
						
                        if ($exe >= 8){
							//攻击力增加  +2%
                            $temp .= vsprintf('<div class="ExcItemOption">%s</div>', [sprintf($ExcItemArr[2]['Name'], $ExcItemArr[2]['Value'])]);
                            $exe -=8;
                        }
                        if ($exe >= 4){
							//攻击(魔法)速度增加 
                            $temp .= vsprintf('<div class="ExcItemOption">%s</div>', [sprintf($ExcItemArr[3]['Name'], $ExcItemArr[3]['Value'])]);
                            $exe -=4;
                        }
						
                        if ($exe >= 2){
							//杀死怪物时所获魔法值增加  +生命值/8
                            $temp .= vsprintf('<div class="ExcItemOption">%s</div>', [sprintf($ExcItemArr[4]['Name'], $ExcItemArr[4]['Value'])]);
							
							//throw new Exception($ExcItemArr[4]['Value']);
							
                            $exe -=2;
                        }
                        if ($exe >= 1){
							//杀死怪物时所获魔法值增加  +魔法值/8
                            $temp .= vsprintf('<div class="ExcItemOption">%s</div>', [sprintf($ExcItemArr[5]['Name'], $ExcItemArr[5]['Value'])]);
                            $exe -=1;
                        }
                    }
                    if ($this->version == 2) {
                        $temp .= $this->getCustomWingBufferEX($this->itemInfo['section'], $this->itemInfo['index'], $this->itemInfo['level']);
                    }
                }
            }
        }else{
            if ($this->itemOption['Slot'] != '-1') {
                #物品是否拥有属性
                if ($this->itemOption['Option']) {
                    $ExcOpt = []; #该物品对应的属性列表
                    $active = []; #该物品对应的属性
                    $ExcItemArr = []; #输出
                    if (($this->itemOption['Slot'] >= 0 && $this->itemOption['Slot'] <= 6) || ($this->itemOption['Slot'] >= 9 && $this->itemOption['Slot'] <= 11)) {
                        foreach ($ExcellentOptionFile['Common']['Option'] as $key => $element) {
                            $element = $element['@attributes'];
                            for ($i = 1; $i < 4; $i++) {
                                if ($this->itemOption['KindA'] == $element['ItemKindA_' . $i]) {
                                    $ExcOpt[] = $element;
                                }
                            }
                        }
                    }
                    if ($this->itemOption['Slot'] == 7) {
                        if ($this->Is4thWing) {
                            #4代翅膀属性
                            $thisGradeOption = $this->getXml($this->item4thWingGradeOptionFile);
                            for ($i = 1; $i <= 4; $i++) {
                                if ($socketOption[$i] > 0 && $socketOption[$i] < 255) {
                                    $index = substr(dechex($socketOption[$i]), 0, 1);
                                    $value = substr(dechex($socketOption[$i]), 1, 1);
                                    $count = substr_count($thisGradeOption['List']['Option'][$index]['@attributes']['Name'],'%d');
                                    if($count > 1) $thisGradeOption['List']['Option'][$index]['@attributes']['Name'] = substr(strstr($thisGradeOption['List']['Option'][$index]['@attributes']['Name'],'%d'),2);
                                    $temp .= vsprintf('<div class="WingGradeOption">%s</div>', [sprintf($thisGradeOption['List']['Option'][$index]['@attributes']['Name'], $thisGradeOption['List']['Option'][$index]['@attributes']['Grade'.$value.'Val'])]);
                                }
                            }
                            #4代翅膀元素属性
                            $This4thWingFile = $this->getXml($this->item4thWingAttributeFile);
                            #4代翅膀合成元素属性
                            if ($socketOption[5] > 0 && $socketOption[5] < 255) {
                                $index = substr(dechex($socketOption[5]), 0, 1);
                                $value = substr(dechex($socketOption[5]), 1, 1);
                                $temp .= vsprintf('<div class="WingAttribute">%s +%s</div>', [$This4thWingFile['AdditionalOptions']['Option'][$index]['@attributes']['Name'], $This4thWingFile['AdditionalOptions']['Option'][$index]['@attributes']['Value'.$value]]);
                            }
                            #4代翅膀默认元素属性
                            if ($socketBonusPotion) {
                                $temp .= vsprintf('<div class="WingAttribute">%s +%s</div>', [$This4thWingFile['MainOption']['@attributes']['Name'], $This4thWingFile['MainOption']['@attributes']['Value' . $socketBonusPotion]]);
                            }
                        } else {
                            #普通翅膀
                            foreach ($ExcellentOptionFile['Wings']['Option'] as $key => $element) {
                                $element = $element['@attributes'];
                                if (($this->itemOption['KindA'] == $element['ItemKindA']) && ($this->itemOption['KindB'] == $element['ItemKindB'])) {
                                    $ExcOpt[] = $element;
                                }
                            }
                        }
                    }
                    #属性解算
                    if (!empty($ExcOpt)) {
                        foreach ($ExcOpt as $key => $exc) {
                            if ($exc['Number'] < 6) {
                                $active[$key] = (($ExcellentOption & (1 << (5 - $exc['Number']))) == (1 << (5 - $exc['Number'])));
                            } else {
                                $active[$key] = (($socketOption[1] == $exc['Number']) || ($socketOption[2] == $exc['Number']) || ($socketOption[3] == $exc['Number']) || ($socketOption[4] == $exc['Number']) || ($socketOption[5] == $exc['Number']));
                            }
                            if ($active[$key]) {
                                if (isset($this->itemOption['MagicPower'])) {
                                    $ExcItemArr[$key] = $this->strReplace('%s', '魔法攻击力', $exc);
                                } else {
                                    $ExcItemArr[$key] = $this->strReplace('%s', '攻击力', $exc);
                                }
                            }
                            $temp .= vsprintf('<div class="ExcItemOption">%s</div>', [sprintf($ExcItemArr[$key]['Name'], $ExcItemArr[$key]['Value'])]);
                        }
                    }
                    #精通奖励属性
                    if (!$this->IsSocket) {
                        if (!$this->Is4thWing) {
                            if ($socketOption[5] > 0 && $socketOption[5] < 255) {
                                $temp .= '<div class="MasteryBonusOption">精通奖励属性</div>';
                                $itemMasteryBonusOption = $this->getXml($this->itemMasteryBonusOptionsFile);
                                foreach ($itemMasteryBonusOption['OptionList']['Option'] as $element) {
                                    $element = $element['@attributes'];
                                    if ($this->itemOption['Slot'] >= 2 && $this->itemOption['Slot'] <= 6) {
                                        if ($element['Number'] == 1) {
                                            if ($socketOption[5] == $element['Index']) {
                                                $temp .= vsprintf('<div class="MasteryBonusText">%s</div>', [sprintf($element['Name'], $element['Value'])]);
                                            }
                                        }
                                    }
                                    if ($this->itemOption['Slot'] == 0 || $this->itemOption['Slot'] == 1) {
                                        if ($element['Number'] == 2) {
                                            $temp .= vsprintf('<div class="MasteryBonusText">%s</div>', [sprintf($element['Name'], $element['Value'])]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
		
		
        return $temp;
    }

    /**
     * 获取镶嵌属性
     * @param $setOption
     * @param $socketOption
     * @param $socketBonusOption
     * @return string
     * @throws Exception
     */
    private function getSocketOption($setOption,$socketOption,$socketBonusOption){
        $temp = '';
		$c=0;
        #物品镶嵌
        if($this->IsSocket) {
            $temp = '<div class="SocketOption">镶嵌 物品属性信息</div>';
            $xmlPath = $this->itemSocketOptionFile;
            $xml = $this->getXml($xmlPath);
            $socketType = [
                1 => '[火]',
                2 => '[水]',
                3 => '[冰]',
                4 => '[风]',
                5 => '[雷]',
                6 => '[土]',
            ];
            for ($i = 1; $i <=count($socketOption); $i++) {
                #没有插槽
                if ($socketOption[$i] == 255) continue;
                #已开插槽
                if ($socketOption[$i] == 254){
                    $temp .= vsprintf('<div class="SocketOptionNull">镶宝[%s]:没有可以镶嵌的物品</div>', [$i]);
					if($i>$c){
							$c=$i;
					}
                    continue;
                }
                $socket = $this->socketType($setOption,$socketOption[$i]);
                if(!empty($socket)){
                    $level = $socket[1];
                    $socketIndex = $socket[0];
                }else{
                    continue;
                }
                if($this->serverFiles != 'igcn') {
                    if ($socketOption[$i] > 51) {
                        //$level = round($socketOption[$i] / 50, 0) + 1;
                        $level = floor($socketOption[$i] / 50) + 1;
                        $socketIndex = $socketOption[$i] - (50 * ($level - 1));
                    } else {
                        $level = 1;
                        $socketIndex = $socketOption[$i];
                    }
                }
                foreach ($xml['SocketItemOptionSettings']['Option'] as $element){
                    $element = $element['@attributes'];
                    if($socketIndex == $element['Index']){

                        $value = (array_search($socketIndex,[5,10,12,13,14,20,22,26,30,32])) ? $element['BonusValue'.$level].'%' : $element['BonusValue'.$level];
                        if($this->serverFiles == 'igcn'){
                            $value = (array_search($socketIndex,[5,10,12,13,14,20,30,32])) ? $element['BonusValue'.$level].'%' : $element['BonusValue'.$level];
                        }
                        $temp .= vsprintf('<div class="SocketOptionText">镶宝[%s]:%s[%s] (<span>%s +%s</span>)</div>', [$i,$socketType[(int)$element['ElementType']],$level,$element['Name'],$value]);
						
						if($i>$c){
							$c=$i;
					    }
                    }
                }
            }
			
			
					
            #幸运荧光属性
            foreach ($xml['SocketBonusSettings']['Option'] as $BonusOption){
                if($socketBonusOption == $BonusOption['@attributes']['Index']){
                    $temp .= '<div class="SocketOption">幸运荧光属性</div>';
                    $temp .= vsprintf('<div class="SocketOptionText"><span>%s +%s</span></div>',[$BonusOption['@attributes']['Name'],$BonusOption['@attributes']['BonusValue']]);
                }
            }
        }
        #耳环
        if($this->IsEarring){
            $temp .= '<div class="mt-2 mb-2"></div>';
            $xml = $this->getXml($this->itemEarringAttributeFile);

            for ($i=1;$i<=count($socketOption);$i++) {
                if ($socketOption[$i] == 255) continue;
//                $index = substr(dechex($socketOption[$i]),0,1);
//                $value = substr(dechex($socketOption[$i]),1,1);
                foreach ($xml['OptionsList']['Option'] as $element){
                    $element = $element['@attributes'];
                }
            }
            $Edition = 1;
            foreach ($xml['OptionLinkSettings']['Option'] as $element){
                $element = $element['@attributes'];
                if($this->itemOption['Index'] == $element['ItemIndex']){
                    $Edition = $element['Edition'];
                    $temp .= '<div class="attributeName mt-2 mb-2">season精通佩戴奖励</div>';
                }
            }
            for ($i=1;$i<=count($socketOption);$i++){
                if ($socketOption[$i] == 255) continue;
                foreach ($xml['OptionSettings']['OptionSet'] as $element){
                    $element = $element['@attributes'];
                    if($element['Edition'] == $Edition){
                        $description = $this->strReplace('和',' +%d<br>',$element['Description']);
                        $description = sprintf($description,5);
//                        $temp .= vsprintf('<div class="attribute">%s %s</div>',[$description,$element['Value']]);
                    }
                }
                $temp .= vsprintf('<div class="attribute">%s %s</div>',['属性增加','']);
            }
        }
        #元素
        if($this->IsPentagram){
            for ($i=1;$i<=count($socketOption);$i++){
                #没有插槽
                if ($socketOption[$i] == 255) continue;

                if($i == 1){
                    if ($socketOption[$i] == 254){
                        $temp .= '<div class="PentagramNone">[愤怒] - NONE - </div>';
                    }else{
                        $temp .= '<div class="Pentagram">[愤怒] 已经镶嵌愤怒元素属性</div>';
                    }
                }if($i == 2){
                    if ($socketOption[$i] == 254){
                        $temp .= '<div class="PentagramNone">[庇护] - NONE - </div>';
                    }else{
                        $temp .= '<div class="Pentagram">[庇护] 已经镶嵌庇护元素属性</div>';
                    }
                }if($i == 3){
                    if ($socketOption[$i] == 254){
                        $temp .= '<div class="PentagramNone">[高贵] - NONE - </div>';
                    }else{
                        $temp .= '<div class="Pentagram">[高贵] 已经镶嵌高贵元素属性</div>';
                    }
                }if($i == 4){
                    if ($socketOption[$i] == 254){
                        $temp .= '<div class="PentagramNone">[神圣] - NONE - </div>';
                    }else{
                        $temp .= '<div class="Pentagram">[神圣] 已经镶嵌神圣元素属性</div>';
                    }
                }if($i == 5){
                    if ($socketOption[$i] == 254){
                        $temp .= '<div class="PentagramNone">[狂喜] - NONE - </div>';
                    }else{
                        $temp .= '<div class="Pentagram">[狂喜] 已经镶嵌狂喜元素属性</div>';
                    }
                }

            }
        }
        if(!empty($temp)){
			$this->socketCount=$c;
		}else{
			$this->socketCount=0;
		}
		return $temp;
    }

    /**
     * @param $level
     * @return SimpleXMLElement|string
     */
    private function getSocketStone($level)
    {
        if($this->itemInfo['section'] == 12){
           if($this->itemInfo['index'] >= 60 && $this->itemInfo['index'] <= 65 ||
               $this->itemInfo['index'] >= 100 && $this->itemInfo['index'] <= 129){
               $xmlPath = $this->itemSocketOptionFile;
               $xml = $this->getXml($xmlPath);
               if(!$xml || !is_array($xml)) return '<div class="text-danger">[镶嵌文件读取失败]</div>';

               foreach ($xml['SocketItemOptionSettings']['Option'] as $data){
                   $data = $data['@attributes'];
                   $type = $this->getSocketStoneType($this->itemInfo['index']);
                   if($type == $data['ElementType']) {
                       if ($data['Level'] == $level) {
                           $value = (array_search($data['Index'],[5,10,12,13,14,20,22,26,30,32])) ? ' +'.$data['BonusValue1'].'%' : ' +'.$data['BonusValue1'];
                           if($this->itemInfo['index'] >= 60 && $this->itemInfo['index'] <= 65) $value = '';
                               return '<div class="SocketOptionText">' . $data['Name'] .$value. '</div>';
                       }
                   }
               }
           }
        }
        return null;
    }

    /**
     * @param $index
     * @return int
     */
    private function getSocketStoneType($index)
    {
        switch ($index){
            case 61:
            case 101:
            case 107:
            case 113:
            case 119:
            case 125:
                return 2;
            case 62:
            case 102:
            case 108:
            case 114:
            case 120:
            case 126:
                return 3;
            case 63:
            case 103:
            case 109:
            case 115:
            case 121:
            case 127:
                return 4;
            case 64:
            case 104:
            case 110:
            case 116:
            case 122:
            case 128:
                return 5;
            case 65:
            case 105:
            case 111:
            case 117:
            case 123:
            case 129:
                return 6;
            default:
            case 60:
            case 100:
            case 106:
            case 112:
            case 118:
            case 124:
                return 1;
        }
    }

    /**
     * 获取时限属性
     * @param $periodItemOption
     * @return string|void
     * @throws Exception
     */
    private function getPeriodItemOption($periodItemOption){

        if($periodItemOption > 0){
            if(($periodItemOption & 1) == 1){
                return '<div class="PeriodItem">已过期</div>';
            }
            if(($periodItemOption & 2) == 2){
                return '<div class="PeriodItemExpire">时限道具</div>';
            }
        }

        return null;
    }

    private function IsPeriodItem($m_PeriodItemOption){
        return ($m_PeriodItemOption & 1) == 1;
    }

    private function IsPeriodItemExpire($m_PeriodItemOption){
        if ($this->IsPeriodItem($m_PeriodItemOption) == false){
            return false;
        }
	    return ($m_PeriodItemOption & 2) == 2;
    }

    public function SetPeriodItem($m_PeriodItemOption)
    {
        $m_PeriodItemOption = 1;
    }

    public function SetPeriodItemExpire($m_PeriodItemOption)
    {
        $m_PeriodItemOption |= 2;
        $m_Durability = 0.0;
    }

    /**
     * 物品总编码计算
     * @param $section
     * @param $index
     * @return float|int
     */
    public function GET_ITEM($section, $index){

        return $section*512+$index;
    }

    /**
     * 替换字符串或数组
     * @param $find
     * @param $replace
     * @param $array
     * @return string|string[]
     */
    private function strReplace($find,$replace,$array){

        if(is_array($array)){
            $array=str_replace($find,$replace,$array);
            foreach ($array as $key => $val) {
                if (is_array($val)) $array[$key]=$this->strReplace($find,$replace,$array[$key]);
            }
        }else{
            $array=str_replace($find,$replace,$array);
        }

        return $array;

    }

    /**
     * 镶嵌属性
     * @param $setOption
     * @param $id
     * @return array
     */
    public function socketType($setOption,$id){
        //    $a= 0;
//    $b= 251;
//    $c= 246;
//    $d= 240;
//    $s=0;
//    echo 'array = [<br>';
//    for($i=0;$i<=253;$i++){
//        echo ''.$s.' => [<br>';
//        echo '1 => ['.($a+0).','.($a+50).','.($a+100).','.($a+150).','.($a+200).'],<br>';
//        echo '2 => ['.($b).','.($a+46).','.($a+96).','.($a+146).','.($a+196).'],<br>';
//        echo '3 => ['.($c).','.($a+40).','.($a+90).','.($a+140).','.($a+190).'],<br>';
//        echo '4 => ['.($d).','.($a+34).','.($a+84).','.($a+134).','.($a+184).'],<br>';
//        echo '],<br>';
//        $a++;
//        $b++;
//        $c++;
//        $d++;
//        $s++;
//        if($a>253)$a = 0;
//        if($b>253)$b = 0;
//        if($c>253)$c = 0;
//        if($d>253)$d = 0;
//        if($s>37) break;
//    }
        $socketCode =[
            0 => [
                1 => [0,50,100,150,200],
                2 => [250,46,96,146,196],
                3 => [246,40,90,140,190],
                4 => [240,34,84,134,184],
            ],
            1 => [
                1 => [1,51,101,151,201],
                2 => [251,47,97,147,197],
                3 => [247,41,91,141,191],
                4 => [241,35,85,135,185],
            ],
            2 => [
                1 => [2,52,102,152,202],
                2 => [252,48,98,148,198],
                3 => [248,42,92,142,192],
                4 => [242,36,86,136,186],
            ],
            3 => [
                1 => [3,53,103,153,203],
                2 => [253,49,99,149,199],
                3 => [249,43,93,143,193],
                4 => [243,37,87,137,187],
            ],
            4 => [
                1 => [4,54,104,154,204],
                2 => [0,50,100,150,200],
                3 => [250,44,94,144,194],
                4 => [244,38,88,138,188],
            ],
            5 => [
                1 => [5,55,105,155,205],
                2 => [1,51,101,151,201],
                3 => [251,45,95,145,195],
                4 => [245,39,89,139,189],
            ],
            6 => [
                1 => [6,56,106,156,206],
                2 => [2,52,102,152,202],
                3 => [252,46,96,146,196],
                4 => [246,40,90,140,190],
            ],
            7 => [
                1 => [7,57,107,157,207],
                2 => [3,53,103,153,203],
                3 => [253,47,97,147,197],
                4 => [247,41,91,141,191],
            ],
            8 => [
                1 => [8,58,108,158,208],
                2 => [4,54,104,154,204],
                3 => [0,48,98,148,198],
                4 => [248,42,92,142,192],
            ],
            9 => [
                1 => [9,59,109,159,209],
                2 => [5,55,105,155,205],
                3 => [1,49,99,149,199],
                4 => [249,43,93,143,193],
            ],
            10 => [
                1 => [10,60,110,160,210],
                2 => [6,56,106,156,206],
                3 => [2,50,100,150,200],
                4 => [250,44,94,144,194],
            ],
            11 => [
                1 => [11,61,111,161,211],
                2 => [7,57,107,157,207],
                3 => [3,51,101,151,201],
                4 => [251,45,95,145,195],
            ],
            12 => [
                1 => [12,62,112,162,212],
                2 => [8,58,108,158,208],
                3 => [4,52,102,152,202],
                4 => [252,46,96,146,196],
            ],
            13 => [
                1 => [13,63,113,163,213],
                2 => [9,59,109,159,209],
                3 => [5,53,103,153,203],
                4 => [253,47,97,147,197],
            ],
            14 => [
                1 => [14,64,114,164,214],
                2 => [10,60,110,160,210],
                3 => [6,54,104,154,204],
                4 => [0,48,98,148,198],
            ],
            15 => [
                1 => [15,65,115,165,215],
                2 => [11,61,111,161,211],
                3 => [7,55,105,155,205],
                4 => [1,49,99,149,199],
            ],
            16 => [
                1 => [16,66,116,166,216],
                2 => [12,62,112,162,212],
                3 => [8,56,106,156,206],
                4 => [2,50,100,150,200],
            ],
            17 => [
                1 => [17,67,117,167,217],
                2 => [13,63,113,163,213],
                3 => [9,57,107,157,207],
                4 => [3,51,101,151,201],
            ],
            18 => [
                1 => [18,68,118,168,218],
                2 => [14,64,114,164,214],
                3 => [10,58,108,158,208],
                4 => [4,52,102,152,202],
            ],
            19 => [
                1 => [19,69,119,169,219],
                2 => [15,65,115,165,215],
                3 => [11,59,109,159,209],
                4 => [5,53,103,153,203],
            ],
            20 => [
                1 => [20,70,120,170,220],
                2 => [16,66,116,166,216],
                3 => [12,60,110,160,210],
                4 => [6,54,104,154,204],
            ],
            21 => [
                1 => [21,71,121,171,221],
                2 => [17,67,117,167,217],
                3 => [13,61,111,161,211],
                4 => [7,55,105,155,205],
            ],
            22 => [
                1 => [22,72,122,172,222],
                2 => [18,68,118,168,218],
                3 => [14,62,112,162,212],
                4 => [8,56,106,156,206],
            ],
            23 => [
                1 => [23,73,123,173,223],
                2 => [19,69,119,169,219],
                3 => [15,63,113,163,213],
                4 => [9,57,107,157,207],
            ],
            24 => [
                1 => [24,74,124,174,224],
                2 => [20,70,120,170,220],
                3 => [16,64,114,164,214],
                4 => [10,58,108,158,208],
            ],
            25 => [
                1 => [25,75,125,175,225],
                2 => [21,71,121,171,221],
                3 => [17,65,115,165,215],
                4 => [11,59,109,159,209],
            ],
            26 => [
                1 => [26,76,126,176,226],
                2 => [22,72,122,172,222],
                3 => [18,66,116,166,216],
                4 => [12,60,110,160,210],
            ],
            27 => [
                1 => [27,77,127,177,227],
                2 => [23,73,123,173,223],
                3 => [19,67,117,167,217],
                4 => [13,61,111,161,211],
            ],
            28 => [
                1 => [28,78,128,178,228],
                2 => [24,74,124,174,224],
                3 => [20,68,118,168,218],
                4 => [14,62,112,162,212],
            ],
            29 => [
                1 => [29,79,129,179,229],
                2 => [25,75,125,175,225],
                3 => [21,69,119,169,219],
                4 => [15,63,113,163,213],
            ],
            30 => [
                1 => [30,80,130,180,230],
                2 => [26,76,126,176,226],
                3 => [22,70,120,170,220],
                4 => [16,64,114,164,214],
            ],
            31 => [
                1 => [31,81,131,181,231],
                2 => [27,77,127,177,227],
                3 => [23,71,121,171,221],
                4 => [17,65,115,165,215],
            ],
            32 => [
                1 => [32,82,132,182,232],
                2 => [28,78,128,178,228],
                3 => [24,72,122,172,222],
                4 => [18,66,116,166,216],
            ],
            33 => [
                1 => [33,83,133,183,233],
                2 => [29,79,129,179,229],
                3 => [25,73,123,173,223],
                4 => [19,67,117,167,217],
            ],
            34 => [
                1 => [34,84,134,184,234],
                2 => [30,80,130,180,230],
                3 => [26,74,124,174,224],
                4 => [20,68,118,168,218],
            ],
            35 => [
                1 => [35,85,135,185,235],
                2 => [31,81,131,181,231],
                3 => [27,75,125,175,225],
                4 => [21,69,119,169,219],
            ],
            36 => [
                1 => [36,86,136,186,236],
                2 => [32,82,132,182,232],
                3 => [28,76,126,176,226],
                4 => [22,70,120,170,220],
            ],
            37 => [
                1 => [37,87,137,187,237],
                2 => [33,83,133,183,233],
                3 => [29,77,127,177,227],
                4 => [23,71,121,171,221],
            ],
        ];
        $return = [];
        if($this->serverFiles != 'igcn') if($this->IsSocket) $setOption = 0;
        foreach ($socketCode as $type=>$item) {
            if ($setOption == 15) {
                foreach ($item[4] as $level=>$value) {
                    if($value == $id) $return = [$type,$level+16];
                }
            } elseif ($setOption == 10) {
                foreach ($item[3] as $level=>$value) {
                    if($value == $id) $return = [$type,$level+11];
                }
            } elseif ($setOption == 5) {
                foreach ($item[2] as $level=>$value) {
                    if($value == $id) $return = [$type,$level+6];
                }
            } else {
                foreach ($item[1] as $level=>$value) {
                    if($value == $id) $return = [$type,$level+1];
                }
            }
        }
        return $return;
    }

    /**
     * @param $hex
     * @return bool
     */
    public function IsItem($hex)
    {
        if(!check_value($hex)) return false;
        if(!preg_match('/[a-fA-F0-9]/',$hex)) return false;
        if($hex == str_pad("F",ITEM_SIZE,"F")) return false;
        return true;
    }

    /**
     * 统计卓越数量
     */
    public function getExcCount()
    {
        $exe = $this->Excellent;
        if($exe >= 64){
            $exe -= 64;
        }
        if($exe >= 32){
            $exe -= 32;
            $this->exe_count += 1;
        }
        if($exe >= 16){
            $exe -= 16;
            $this->exe_count += 1;
        }
        if($exe >= 8){
            $exe -= 8;
            $this->exe_count += 1;
        }
        if($exe >= 4){

            $exe -= 4;
            $this->exe_count += 1;
        }
        if($exe >= 2){

            $exe -= 2;
            $this->exe_count += 1;
        }
        if($exe >= 1){
            $exe -= 1;
            $this->exe_count += 1;
        }
    }

    /**
     * 特殊属性显示
     * @param $type
     * @param $setOption
     * @param $level
     * @return string|void
     */
    public function getTooName($type,$setOption,$level)
    {
        $tConfig = $this->localTooList();
        if (empty($tConfig)) return;
        if (!is_array($tConfig)) return;
        $xmlPathLv =  $this->itemTooNameFile;
        $xmlLv = $this->getXml($xmlPathLv);
        if(!is_array($xmlLv)) return;
        $temp = '';
        $useToo = false;
        for ($i=1;$i<=count($tConfig);$i++){ if($tConfig[$i]['item_type'] == $type) $useToo = true; }
        if($this->config['too_name']) {
            $temp = $useToo ? '<div class="SetOption">' . $this->config['too_name'] . '</div>' : '';
        }
        foreach ($tConfig as $i=>$cfg) {
            if(!array_key_exists($cfg['item_use'],$xmlLv['Too'])) continue;
            #找到物品
            if ($type == $cfg['item_type']) {
                $option[] = [
                    'index' => $xmlLv['Too'][$cfg['item_use']]['@attributes']['Index'],
                    'name'  => $xmlLv['Too'][$cfg['item_use']]['@attributes']['Name'],
                    'rate'  => $cfg['item_rate'],
                    'level' => $cfg['item_lev'],
                    'set'   => $cfg['item_set'],
                    'exc'   => $cfg['item_exc'],
                    'color' => $cfg['color'],
                ];
            }
        }

        if(empty($option)) return;

        #验证是否有等级要求
        foreach ($option as $key=>$data){
            if($data['level'] > 0 && $data['level'] < 100){
                if ($level == $data['level']) {
                    $newOption1[$key] = $data;
                }elseif($data['level'] > 100 && $data['level'] < 200){
                    if ($level >= ($data['level']-100)) {
                        $newOption1[$key] = $data;
                    }
                }
            }else{
                $newOption1[$key] = $data;
            }
        }

        if(empty($newOption1)) return;
        #验证卓越
        foreach ($newOption1 as $key=>$data){
            if ($data['exc'] > 0 ){
                if($this->exe_count){
                    if($this->exe_count == $data['exc']){
                        $newOption2[$key] = $data;
                    }elseif($data['exc'] == 1){
                        $newOption2[$key] = $data;
                    }
                }
            } else{
                $newOption2[$key] = $data;
            }
        }
        if(empty($newOption2)) return;
        #验证套装
        $set = 5;
        if($setOption == 5 || $setOption == 9) $set = 5;
        if($setOption == 6 || $setOption == 10) $set = 6;
        foreach ($newOption2 as $key=>$data){
            if ($data['set'] > 0 ){
                if($setOption){
                    if($set == $data['set']){
                        $newOption3[$key] = $data;
                    }elseif($data['set'] == 1){
                        $newOption3[$key] = $data;
                    }
                }
            } else{
                $newOption3[$key] = $data;
            }
        }
        if(empty($newOption3)) return;
        foreach ($newOption3 as $data){
            $temp .= '<div class="' . $data['color'] . '">' . sprintf($data['name'], $data['rate']) . '</div>';
        }
        return $temp;
    }

    /**
     * @param $userID
     * @param $username
     * @param $char_name
     * @param $item
     * @param bool $offset
     * @param bool $send_char
     * @return array|string|void
     * @throws Exception
     */
    public function generateBoxQuery($userID, $username, $char_name, $item, $offset = false, $send_char = true)
    {
        if(substr($item,0,1) == "&" || substr($item,0,1) == ",") $item = substr($item,1);
        if (strstr($item,"&")){
            $itemCodeArray = explode("&",$item);
        }else{
            $itemCodeArray = explode(",",$item);
        }
        $StorageBox = [];
        for($i=0;$i<count($itemCodeArray);$i++){
            $itemCode[$i] = $this->convertItem($itemCodeArray[$i]);
            $StorageBox[$i] = [
                'UserGuid'      => $userID,
                'CharacterName' => $char_name,
                'Type'          => 1,
                'ItemCode'      => $itemCode[$i]['type'],
                'ItemData'      => $itemCodeArray[$i],
                'ValueType'     => '-1',
                'ValueCnt'      => 0,
                'CustomData'    => 0,
                'GetDate'       => logDate(),
                'ExpireDate'    => logDate('+7 day'),
                'UsedInfo'      => 1, # 0已接收,1未接收
                'index'                 =>  $itemCode[$i]['index'],#小编吗
                'level'                 =>  $itemCode[$i]['level'],#等级
                'skill'                 =>  $itemCode[$i]['skill'],#技能
                'lucky'                 =>  $itemCode[$i]['lucky'],#幸运
                'option'                =>  $itemCode[$i]['option'],#追加
                'durability'            =>  $itemCode[$i]['durability'],#耐久
                'section'               =>  $itemCode[$i]['section'],#大编码
                'setOption'             =>  $itemCode[$i]['setOption'],#套装
                'newOption'             =>  $itemCode[$i]['newOption'],#卓越
                'itemOptionEx'          =>  $itemCode[$i]['itemOptionEx'],#380
                'jewelOfHarmonyOption'  =>  $itemCode[$i]['jewelOfHarmonyOption'],#再生强化属性
                'socketOption1'         =>  $itemCode[$i]['socketOption'][1],#镶嵌[0-4]
                'socketOption2'         =>  $itemCode[$i]['socketOption'][2],#镶嵌[0-4]
                'socketOption3'         =>  $itemCode[$i]['socketOption'][3],#镶嵌[0-4]
                'socketOption4'         =>  $itemCode[$i]['socketOption'][4],#镶嵌[0-4]
                'socketOption5'         =>  $itemCode[$i]['socketOption'][5],#镶嵌[0-4]
                'socketBonusPotion'     =>  $itemCode[$i]['socketBonusPotion'],#荧光
                'periodItemOption'      =>  $itemCode[$i]['periodItemOption'],#时限
            ];
        }
        if ($offset) return $StorageBox;
        $query = [];
        for($i=0;$i<count($itemCodeArray);$i++) {
            switch ($this->serverFiles){
                case "egames":
                    $query[$i] = "INSERT INTO [T_StorageBox] ([UserGuid],[CharacterName],[Type],[ItemCode],[ItemData],[ValueType],[ValueCnt],[CustomData],[GetDate],[ExpireDate],[UsedInfo]) VALUES (".$StorageBox[$i]['UserGuid'].", '".$StorageBox[$i]['CharacterName']."', ".$StorageBox[$i]['Type'].", '".$StorageBox[$i]['ItemCode']."', '".$StorageBox[$i]['ItemData']."', '".$StorageBox[$i]['ValueType']."', ".$StorageBox[$i]['ValueCnt'].", ".$StorageBox[$i]['CustomData'].", '".$StorageBox[$i]['GetDate']."', '".$StorageBox[$i]['ExpireDate']."', ".$StorageBox[$i]['UsedInfo'].")";
                    break;
                case "igcn":
                    $query[$i] = "INSERT INTO [IGC_GremoryCase] ([AccountID],[Name],[GCType],[GiveType],[ItemType],[ItemIndex],[Level],[ItemDur],[Skill],[Luck],[Opt],[SetOpt],[NewOpt],[BonusSocketOpt],[SocketOpt1],[SocketOpt2],[SocketOpt3],[SocketOpt4],[SocketOpt5],[UsedInfo],[Serial],[RecvDate],[ReceiptDate],[RecvExpireDate],[ItemExpireDate],[RecvDateConvert],[RecvExpireDateConvert],[ItemExpireDateConvert],[ItemOptionEx],[ItemSerial]) VALUES ('".$username."','".$StorageBox[$i]['CharacterName']."', 2, 100, ".$StorageBox[$i]['section'].", ".$StorageBox[$i]['index'].", ".$StorageBox[$i]['level'].", ".$StorageBox[$i]['durability'].", ".$StorageBox[$i]['skill'].", ".$StorageBox[$i]['lucky'].", ".$StorageBox[$i]['option'].", ".$StorageBox[$i]['setOption'].", ".$StorageBox[$i]['newOption'].", ".$StorageBox[$i]['socketBonusPotion'].", ".$StorageBox[$i]['socketOption1'].", ".$StorageBox[$i]['socketOption2'].", ".$StorageBox[$i]['socketOption3'].", ".$StorageBox[$i]['socketOption4'].", ".$StorageBox[$i]['socketOption5'].", ".$StorageBox[$i]['UsedInfo'].", 0, '".$StorageBox[$i]['GetDate']."', NULL, '".$StorageBox[$i]['ExpireDate']."', '1970-01-01 00:00:00', ".time().", ".strtotime($StorageBox[$i]['ExpireDate']).", 0, 0, 0)";
                    break;
                default:
                    throw new Exception("暂不支持您的游戏版本。");
            }
        }
        return $query;
    }

    /**
     * 加载TooList
     * @param string $name
     * @return mixed|void
     */
    public function localTooList($name = 'tooList'){
        if(!check_value($name)) return;
        if(!file_exists(__PATH_PLUGIN_ITEM_ROOT__ . $name . '.json')) return;
        $cfg = file_get_contents(__PATH_PLUGIN_ITEM_ROOT__ . $name . '.json');
        if(!check_value($cfg)) return;
        return json_decode($cfg, true);
    }

    /**
     * 加载配置文件
     * @param string $file
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($file='config')
    {
        $xmlPath = __PATH_PLUGIN_ITEM_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('透视物品配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载透视物品插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }

    /**
     * @param $itemID
     * @return int
     */
    public function checkItemIsJewel($itemID)
    {
        if ( $itemID == 6174 #祝福宝石组合
            || $itemID == 6175 #灵魂宝石组合
            || $itemID == 6280 #生命宝石组合
            || $itemID == 6281 #创造宝石组合
            || $itemID == 6282 #守护宝石组合
            || $itemID == 6283 #再生原石组合
            || $itemID == 6284 #再生宝石组合
            || $itemID == 6285 #玛雅之石组合
            || $itemID == 6286 #低级进化宝石组合
            || $itemID == 6287 #高级进化宝石组合
            || $itemID == 7181 #祝福宝石
            || $itemID == 7182 #灵魂宝石
            || $itemID == 7184 #生命宝石
            || $itemID == 7190 #创造宝石
            || $itemID == 7200 #守护宝石
            || $itemID == 7209 #再生原石
            || $itemID == 7210 #再生宝石
            || $itemID == 7211 #初级进化宝石
            || $itemID == 7212 #高级进化宝石
            || $itemID == 6159 #玛雅之石
            || $itemID == 10740 #暗之祝福宝石
            || $itemID == 10741 #暗之灵魂宝石
            || $itemID == 10742 #暗之生命宝石
            || $itemID == 10743 #卓越宝石
            || $itemID == 10744 #幸运宝石
            || $itemID == 10745 #技能宝石
            || $itemID == 10746 #困顿宝石
            || $itemID == 10747 #全属宝石
            || $itemID == 10748 #镶嵌宝石
        ) return 1;
        return 0;
    }

    /**
     * 从总编号获得物品的大小编号
     * @param $type
     * @return array
     */
    public function itemSolve($type){
        $y = 0;
        for($i=0;$i<512;$i++){
            if(is_int(($type-$i)/512)){

                $y = $i;
            }
        }
        $x = ($type-$y)/512;
        return [intval($x),$y];
    }

    public function itemType($section,$index,$type,$slot)
    {
        # -1:不适用,0:左手,1:右手,2:头盔,3:铠甲,4:护腿,5:护手,6:靴子,7:翅膀,8:帮（宠物),9:项链,10:左戒指,11:右戒指,236:元素卷轴,237:耳环,238:耳环
        $itemType = [0,"道具"];
        if($section >= 0 && $section <= 5) $itemType = [1,"武器"];
        if($section >= 6 && $section <= 11)  $itemType = [2,"防具/装备"];
        if($slot >= 2 && $slot <= 6) $itemType = [2,"防具/装备"];
        if($slot == 7) $itemType = [3,"翅膀"];
        if($slot >= 9 && $slot <= 11 || $slot == 237 || $slot == 238) $itemType = [4,"首饰"];
        if($this->checkItemIsJewel($type)) $itemType = [6,"宝石"];
        if($section == 12 && ($index >= 60 && $index <= 129)) $itemType = [7,"荧石"];
        if($slot == 8) $itemType = [8,"宠物/坐骑"];
        return $itemType;
    }
}
?>