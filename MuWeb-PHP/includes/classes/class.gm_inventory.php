<?php
/**
 * GM保管箱操作插件
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin\Market;

use Connection;
use Exception;
use Plugin\equipment;
use Validator;
use Account;
use Token;

class gm_inventory {

    private $serverFiles,$equipment,$account;
    private $WAREHOUSE_LENGTH = 3840;
    private $WAREHOUSE_MAX_SIZE = 120;
    private $web,$muonline,$me_muonline,$NULL,$NULLWAREHOUSE;
    public $warehouseList = [];
    #是否使用扩展GM保管箱
    public $useExtendWarehouse = false;
    public $extendWarehouseData;
    private $_width,$_height;
    public $_items = [];
    public $_map = [];

    /**
     * 构造函数
     * @param $group
     * @param string $username
     * @throws Exception
     */
    public function __construct($group, $username = '')
    {
        #服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        #GM保管箱BUFF长度
        if($this->serverFiles =="igcn") $this->WAREHOUSE_LENGTH = $this->WAREHOUSE_LENGTH;
        #初始化GM保管箱代码
        $this->NULLWAREHOUSE = str_pad("F",$this->WAREHOUSE_LENGTH,"F");
		
		
        #空物品值
        $this->NULL = str_pad("F",ITEM_SIZE,"F");
        $this->web = Connection::Database('Web');
        $this->muonline = Connection::Database('MuOnline',$group);
        $this->me_muonline = Connection::Database('MuOnline',$group);
        $this->equipment = new equipment();
        $this->account = new Account();
        #初始化
        $this->_width = 8;
        $this->_height =15;

        for ($x = 0; $x < $this->_width; $x++) {
            for ($y = 0; $y < $this->_height; $y++) {
                $this->_items[$x][$y] = $this->NULL;
                $this->_map[$x][$y] = 0;
            }
        }
        #获取GM保管箱物品[]
        if(empty($username)) $username = $_SESSION['username'];
        $this->_getWarehouseList($group,$username);
        $this->init();
    }

    /**
     * @param $map
     * @return mixed
     * @throws Exception
     */
    public function getWarehouseForMap($map)
    {
        if(!check_value($map))  throw new Exception("[WAREHOUSE][1] 位置编号错误！");
        if(!Validator::UnsignedNumber($map)) throw new Exception("[WAREHOUSE][2] 位置编号错误！");
        $item = $this->_getItemLength();
        if(!array_key_exists($map,$item)) throw new Exception("[WAREHOUSE][3] 位置编号错误！");
        $items = $this->equipment->convertItem($item[$map]['code']);
        return [
            "code" => $item[$map]['code'],
            "name" => $this->equipment->getItemName($items['section'],$items['index'],$items['level']),
            ];
    }
    /**
     * GM保管箱添加物品
     * @param $itemCode
     * @return string
     * @throws Exception
     */
    public function warehouseAddItem($itemCode){
        if($itemCode == $this->NULL) throw new Exception("[WAREHOUSE] 操作失败,物品错误!");
        #操作GM保管箱
        $newWarehouse = $this->setWarehouseAddNewItem($itemCode);

        if(!$newWarehouse) throw new Exception("[WAREHOUSE] GM保管箱位置不足，请确保GM保管箱上分8*8位置有足够的位置存放。");
        #拼接
        $newWarehouse = $newWarehouse.$this->extendWarehouseData;
        #为了安全二次校验
//        if(!stripos($newWarehouse,$itemCode)) throw new Exception("[WAREHOUSE] 操作失败,物品错误!");
        return $newWarehouse;
    }

    /**
     * GM保管箱移除物品
     * @param $itemCode
     * @return int|string
     * @throws Exception
     */
    public function warehouseRemoveItem($itemCode){
        if($itemCode == $this->NULL) throw new Exception("[WAREHOUSE] 操作失败,物品错误!");
        #操作GM保管箱
        $newWarehouse = $this->setWarehouseRemoveItem($itemCode);

        if(!$newWarehouse) throw new Exception("[WAREHOUSE] 操作失败,无法识别该物品!");
        #拼接
        $newWarehouse = $newWarehouse.$this->extendWarehouseData;
        return $newWarehouse;
    }

    /**
     * @throws Exception
     */
    public function init()
    {
        $item = $this->_getItemLength();
        #生成120或240 BUF
        $SIZE = ($this->useExtendWarehouse) ? $this->WAREHOUSE_MAX_SIZE : ($this->WAREHOUSE_MAX_SIZE/2);
        for ($i = 0; $i < $SIZE; $i++) {
            #获取位置X,Y
            $cords = $this->_getCordBySlot($i);
            if(!empty($item[$i])){
                #放置物品到位置
                $this->_putItem($item[$i]['code'], $item[$i]['width'], $item[$i]['height'], $cords["x"], $cords["y"]);
            }
        }
    }
    /**
     * 获取GM保管箱代码
     * @param $group
     * @param $username
     * @return string
     * @throws Exception
     */
    public function getWarehouse($group, $username)
    {
        if(!check_value($username)) throw new Exception('[WAREHOUSE] 请求错误！');
        if(!Validator::AlphaNumeric($username)) throw new Exception('[WAREHOUSE] 账号错误哦，获取失败！');
        $result = Connection::Database("MuOnline",$group)->query_fetch_single("SELECT CONVERT(varchar(max),Items,2) as Items FROM GmInventory WHERE AccountID = ?",[$username]);
        if (!$result) throw new Exception('您还没打开过GM保管箱,请先进游戏打开一次GM保管箱再来!');
        #预防无数据
//        if(empty($result)) $result['Items'] = str_pad("F",$this->WAREHOUSE_LENGTH,"F");
        return $result['Items'];
    }


    /**
     * 初始化GM保管箱
     * @param $group
     * @param $username
     * @return bool|void
     * @throws Exception
     */
    public function insertWarehouse($group,$username)
    {
        if ($this->account->checkUserOnline($group,$username)) throw new Exception('[WAREHOUSE] 当前用户正在游戏中,请先退出游戏!');
        if(!check_value($username)) throw new Exception('[WAREHOUSE] 请求错误！');
        if(!Validator::AlphaNumeric($username)) throw new Exception('[WAREHOUSE] 账号错误哦，获取失败！');
        $result = $this->muonline->query("INSERT INTO [GmInventory]  ([AccountID],[Items]) VALUES (?,CONVERT(VARBINARY(MAX), ? ,2))",[$username,$this->NULLWAREHOUSE]);
        if (!$result) return false;
        return true;
    }

    /**
     *
     * @return mixed|void
     * @throws Exception
     */
    public function getWarehouseList(){
        return $this->warehouseList;
    }

    /**
     * 获取GM保管箱数据
     * @param $group
     * @param $username
     * @return mixed
     * @throws Exception
     */
    private function _getWarehouseList($group,$username){
        if(!check_value($username)) throw new Exception('[WAREHOUSE] 请求错误！');
        if(!Validator::AlphaNumeric($username)) throw new Exception('[WAREHOUSE] 账号错误哦，获取失败！');
        $data = $this->getWarehouse($group,$username);
        if(!$data) throw new Exception('[WAREHOUSE] 请求错误！');
        #根据服务器类型截断
        $length = ($this->useExtendWarehouse) ? $this->WAREHOUSE_LENGTH : ($this->WAREHOUSE_LENGTH / 2);
		
		
        $warehouseArr = substr($data,0,$length);
        #划切到数组
        $this->warehouseList = str_split($warehouseArr,ITEM_SIZE);
        #备份扩展GM保管箱数据
        $this->extendWarehouseData = substr($data,$length);
        return $this->warehouseList;
    }

    /**
     * 输出物品位置
     * @return array
     * @throws Exception
     */
    public function _getItemLength(){
        $item = [];
        if(empty($this->warehouseList)) return $item;
        $x = $y = 0;
        foreach ($this->warehouseList as $code=>$list){
            $this->equipment->convertItem($list);
            $item[$code] = [
                'url'    => $this->equipment->ItemsUrl($list),
                'width'  => $this->equipment->itemOption['Width'],
                'height' => $this->equipment->itemOption['Height'],
                'y'      => $y,
                'x'      => $x,
                'code'   => $list,
            ];
            $x++;
            if ($x >= 8) {
                $x = 0;
                $y++;
                if ($y >= 15) {
                    $y = 0;
                }
            }
            if($list == $this->NULL) $item[$code] = [];
        }

        return $item;
    }

    /**
     * 添加物品
     * @param $itemCode
     * @return int
     * @throws Exception
     */
    public function setWarehouseAddNewItem($itemCode)
    {
        if($itemCode == $this->NULL) throw new Exception("[WAREHOUSE] 操作失败,物品错误!");
        if (!preg_match('/[a-fA-F0-9]/',$itemCode)) throw new Exception("[WAREHOUSE] 操作失败,物品错误!");

        #获取物品宽高;
        if (isset($this->equipment)) {
            if (isset($this)) {
                $this->equipment->convertItem($itemCode);
            }
        }
        $_addItem = $this->equipment->itemOption;
        if($this->_addItem($itemCode,$_addItem['Width'],$_addItem['Height'])){
            return $this->_generate();
        }
        return 0;
    }


    /**
     * 移除物品
     * @param $itemCode
     * @return int|string
     * @throws Exception
     */
    public function setWarehouseRemoveItem($itemCode)
    {
        if($itemCode == $this->NULL) throw new Exception("[WAREHOUSE] 操作失败,物品错误!");
        if (!preg_match('/[a-fA-F0-9]/',$itemCode)) throw new Exception("[WAREHOUSE] 操作失败,物品错误!");

        #获取物品宽高;
        $this->equipment->convertItem($itemCode);
        if($this->_removeItem($itemCode,$this->equipment->itemOption['Width'],$this->equipment->itemOption['Height'])){
            return $this->_generate();
        }
        return 0;
    }
	
	public function getNULLWAREHOUSE(){
		
		return $this->NULLWAREHOUSE;
		
	}

    /**
     * @param $slot
     * @return array
     */
    private function _getCordBySlot($slot)
    {
        return [
            "x" => $slot % $this->_width,
            "y" => floor($slot / $this->_width)
        ];
    }

    /**
     * 是否使用扩展GM保管箱
     * @return int
     */
    private function _getSlice()
    {
        $_slice = 1;
        if($this->useExtendWarehouse) $_slice = 2;
        return $_slice;
    }

    /**
     * 添加物品
     * @param $item
     * @param $Width
     * @param $height
     * @return bool
     * @throws Exception
     */
    public function _addItem($item, $Width, $height)
    {
        if($this->_width < $Width) throw new Exception("操作失败,该物品宽度大于GM保管箱宽度!");
        if($this->_height < $height) throw new Exception("操作失败,该物品高度大于GM保管箱高度!");
        if($height <=0 || $Width <=0) throw new Exception("无效的物品尺寸(宽度<=0或高度<=0)!");
        for ($y = 0; $y <= $this->_width - $height; $y++) {
            for ($x = 0; $x <= $this->_width - $Width; $x++) {
                if ($this->_fitItem($Width, $height, $x, $y)) {
                    $this->_putItem($item, $Width, $height, $x, $y);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 移除物品
     * @param $item
     * @param $Width
     * @param $height
     * @return bool
     * @throws Exception
     */
    private function _removeItem($item, $Width, $height)
    {
        if($this->_width < $Width) throw new Exception("操作失败,该物品宽度大于GM保管箱宽度!");
        if($this->_height < $height) throw new Exception("操作失败,该物品高度大于GM保管箱高度!");
        if($height <=0 || $Width <=0) throw new Exception("无效的物品尺寸(宽度<=0或高度<=0)!");
        for ($y = 0; $y < $this->_height; $y++) {
            for ($x = 0; $x < $this->_width; $x++) {
                $i = $this->getItem($x, $y);
                if ($i !== null && $i === $item) {
                    $this->_items[$x][$y] = null;
                    for ($PosY = $y; $PosY < $y + $height; $PosY++) {
                        for ($PosX = $x; $PosX < $x + $Width; $PosX++) {
                            $this->_map[$PosX][$PosY] = 0;
                        }
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 生成新的GM保管箱数据
     * @return string
     */
    private function _generate()
    {
        $temp = [];
        for ($y = 0; $y < $this->_height; $y++) {
            for ($x = 0; $x < $this->_width; $x++) {
                $item = $this->getItem($x, $y);
                if ($item == null) {
                    $temp[] = $this->NULL;
                } else {
                    $temp[] = $item;
                }
            }
        }
        return join("", $temp);
    }

    /**
     * @param $x
     * @param $y
     * @return mixed|null
     */
    private function getItem($x, $y)
    {
        if ($x < 0 || $y < 0 || $this->_width <= $x || $this->_height <= $y) {
            return null;
        }
        return $this->_items[$x][$y];
    }

    /**
     * 生成物品状态BUF
     * @param $item
     * @param $Width
     * @param $height
     * @param $PosX
     * @param $PosY
     * @return bool
     * @throws Exception
     */
    private function _putItem($item, $Width, $height, $PosX, $PosY)
    {
        if ($this->_width <= $PosX || $PosX < 0) throw new Exception("Invalid slot position. (x > width || x < 0)");
        if ($this->_height <= $PosY || $PosY < 0) throw new Exception("Invalid slot position. (y > height || y < 0)");

        for ($y = $PosY; $y < $PosY + $height; $y++) {
            for ($x = $PosX; $x < $PosX + $Width; $x++) {
                $this->_map[$x][$y] = 1;
            }
        }
        $this->_items[$PosX][$PosY] = $item;
        return true;
    }

    /**
     * 识别有位置存放物品
     * @param $Width
     * @param $height
     * @param $PosX
     * @param $PosY
     * @return bool
     * @throws Exception
     */
    private function _fitItem($Width, $height, $PosX, $PosY)
    {
        if ($this->_width <= $PosX || $PosX < 0) throw new Exception("Invalid slot position. (x > width || x < 0)");
        if ($this->_height <= $PosY || $PosY < 0) throw new Exception("Invalid slot position. (y > height || y < 0)");

        for ($y = $PosY; $y < $PosY + $height; $y++) {
            for ($x = $PosX; $x < $PosX + $Width; $x++) {
                $mHeight = $this->_height / $this->_getSlice();
                $slice = ceil(($y + 1) / $mHeight);
                if ($mHeight * $slice < $PosY + $height) {
                    return false;
                }
                if ($this->_hasItem($x, $y)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 检测该位置是否有物品存放
     * @param $x
     * @param $y
     * @return bool|mixed
     */
    private function _hasItem($x, $y)
    {
        if ($x < 0 || $y < 0 || $this->_width <= $x || $this->_height <= $y) {
            return false;
        }
         return $this->_map[$x][$y];
    }

    /**
     * 重置GM保管箱物品函数
     * @param $group
     * @param $username
     * @param $password
     * @return string
     * @throws Exception
     */
    public function resetWarehouse($group, $username, $password)
    {
        if (!Token::checkToken('resetWarehouse')) throw new Exception('[WAREHOUSE-ERROR]出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('');
        if(!check_value($username)) throw new Exception('');
        if(!check_value($password)) throw new Exception('');
        if ($this->account->checkUserOnline($group,$username)) throw new Exception('[WAREHOUSE-ERROR]当前账号正在游戏中，请先退出游戏！');
        if(!$this->account->validateUsername($group, $username, $password))  throw new Exception('[WAREHOUSE-ERROR]您输入的密码不正确！');
        $NULLWAREHOUSE = str_pad("F", 1920,"F");
        $result = $this->muonline->query("UPDATE [GmInventory] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$NULLWAREHOUSE,$username]);
        if(!$result) throw new Exception('[WAREHOUSE-ERROR]操作失败，请联系客服。');
        return alert('usercp/ResetWarehouse','您的GM保管箱已重置成功！');
    }

    /**
     * 从GM保管箱获取物品数量
     * @param $itemType //物品总编号
     * @return int|string
     * @throws Exception
     */
    public function getWarehouseItemNumberForItem($itemType)
    {
        $count = 0;
        if(empty($this->warehouseList)) return $count;
        if(!check_value($itemType)) return '[WAREHOUSE-ERROR]物品错误';
        if (!preg_match('/[0-9]/',$itemType)) return '[WAREHOUSE-ERROR]物品错误';
        for ($i=0;$i<=count($this->warehouseList);$i++){
            $wareItem = $this->equipment->convertItem($this->warehouseList[$i]);
            if($wareItem['type'] == $itemType) $count++;
        }
        return $count;
    }

    /**
     * 删除物品
     * @param $itemType
     * @param $number
     * @return int
     * @throws Exception
     */
    public function deleteWarehouseItemForItem($itemType,$number)
    {
        if(!check_value($itemType)) return 0;
        if (!preg_match('/[a-fA-F0-9]/',$itemType)) return 0;
        $b=0;
        for ($i=0;$i<=count($this->warehouseList);$i++){
            if($this->warehouseList[$i] == $this->NULL) continue;
            $wareItem = $this->equipment->convertItem($this->warehouseList[$i]);
            if($wareItem['type'] == $itemType){
                $this->warehouseList[$i] = $this->NULL;
                $b++;
            }
            if($b == $number) break;
        }
        return $b;
    }
}