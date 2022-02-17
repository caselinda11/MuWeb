<?php
/**
 * 商城插件相关函数类
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Validator;
use Connection;
use CreditSystem;
use common;
use Plugin\equipment;
use Token;
class Shop {

    private $_modulesPath = 'modules';
    private $serverFiles;
    private $web,$tConfig;

    /**
     * 构造函数
     * Shop constructor.
     * @throws Exception
     */
    public function __construct()
    {
       //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        $this->web = Connection::Database("Web");
        $this->tConfig = $this->loadConfig();
    }

    /**
     * 购买物品
     * @param $group
     * @param $username
     * @param $character_name
     * @param $id
     * @return string
     * @throws Exception
     */
    public function setBuyShopItem($group, $username, $character_name, $id)
    {
        if(!check_value($group)) throw new Exception('数据错误，请重新尝试！');
        if(!check_value($username)) throw new Exception('数据错误，请重新尝试！');
        if(!check_value($_SESSION['userid'])) throw new Exception('数据错误，请重新尝试！');
        if(!check_value($character_name)) throw new Exception('数据错误，请重新尝试！');
        if(!check_value($id)) throw new Exception('数据错误，请重新尝试！');
        if(!Validator::Number($id)) throw new Exception('数据错误，请重新尝试！');
        $data = $this->getShopDataList($id);
        if(!is_array($data)) throw new Exception('该商品已下架，请重新选择商品。');
        if($data['item_count']) if($data['item_count'] - $this->checkItemCount($id) <= 0) throw new Exception('该商品已经售馨，请下次活动再来。');

        #获取物品的总编码
        $items = new equipment();
        $itemCodeArray = explode(",",$data['item_code']);
        $StorageBox = [];
        for($i=0;$i<count($itemCodeArray);$i++){
            $itemCode[$i] = $items->convertItem($itemCodeArray[$i]);
            $StorageBox[$i] = [
                'UserGuid'      => $_SESSION['userid'],
                'CharacterName' => $character_name,
                'Type'          => 1,
                'ItemCode'      => $itemCode[$i]['type'],
                'ItemData'      => $itemCodeArray[$i],
                'ValueType'     => '-1',
                'ValueCnt'      => 0,
                'CustomData'    => 0,
                'GetDate'       => logDate(),
                'ExpireDate'    => logDate('+7 day'),
                'UsedInfo'      => 1, # 0已接收,1未接收
                //物品信息
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
        if (empty($StorageBox))  throw new Exception('[10] - '.'出错了，请您重新输入！');

        $muOnline = Connection::Database("MuOnline",$group);
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($data['price_type']);
        $configSettings = $creditSystem->showConfigs(true);
        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($username);
                    break;
                case 'userid':
                    $creditSystem->setIdentifier($_SESSION['userid']);
                    break;
                case 'character':
                    $creditSystem->setIdentifier($character_name);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group,$data['item_price']);
            #EG端说明 ValueType = -1普通物品,0积分,1元宝,2声望,3金币
            for($i=0;$i<count($itemCodeArray);$i++) {
                switch ($this->serverFiles){
                    case "egames":
                        $query = "INSERT INTO [T_StorageBox] ([UserGuid],[CharacterName],[Type],[ItemCode],[ItemData],[ValueType],[ValueCnt],[CustomData],[GetDate],[ExpireDate],[UsedInfo]) VALUES (".$StorageBox[$i]['UserGuid'].", '".$StorageBox[$i]['CharacterName']."', ".$StorageBox[$i]['Type'].", '".$StorageBox[$i]['ItemCode']."', '".$StorageBox[$i]['ItemData']."', '".$StorageBox[$i]['ValueType']."', ".$StorageBox[$i]['ValueCnt'].", ".$StorageBox[$i]['CustomData'].", '".$StorageBox[$i]['GetDate']."', '".$StorageBox[$i]['ExpireDate']."', ".$StorageBox[$i]['UsedInfo'].")";
                        $muOnline->query($query);
                        break;
                    case "igcn":
                        //debug("INSERT INTO [IGC_GremoryCase] ([AccountID],[Name],[GCType],[GiveType],[ItemType],[ItemIndex],[Level],[ItemDur],[Skill],[Luck],[Opt],[SetOpt],[NewOpt],[BonusSocketOpt],[SocketOpt1],[SocketOpt2],[SocketOpt3],[SocketOpt4],[SocketOpt5],[UsedInfo],[Serial],[RecvDate],[ReceiptDate],[RecvExpireDate],[ItemExpireDate],[RecvDateConvert],[RecvExpireDateConvert],[ItemExpireDateConvert],[ItemOptionEx],[ItemSerial]) VALUES (".$StorageBox[$i]['UserGuid'].",'".$StorageBox[$i]['CharacterName']."', 2, 100, ".$StorageBox[$i]['section'].", ".$StorageBox[$i]['index'].", ".$StorageBox[$i]['level'].", ".$StorageBox[$i]['durability'].", ".$StorageBox[$i]['skill'].", ".$StorageBox[$i]['lucky'].", ".$StorageBox[$i]['option'].", ".$StorageBox[$i]['setOption'].", ".$StorageBox[$i]['newOption'].", ".$StorageBox[$i]['socketBonusPotion'].", ".$StorageBox[$i]['socketOption1'].", ".$StorageBox[$i]['socketOption2'].", ".$StorageBox[$i]['socketOption3'].", ".$StorageBox[$i]['socketOption4'].", ".$StorageBox[$i]['socketOption5'].", ".$StorageBox[$i]['UsedInfo'].", 0, '".$StorageBox[$i]['GetDate']."', NULL, '".$StorageBox[$i]['ExpireDate']."', '1970-01-01 00:00:00', ".time().", ".strtotime($StorageBox[$i]['ExpireDate']).", 0, 0, 0)");
                        $muOnline->query("INSERT INTO [IGC_GremoryCase] ([AccountID],[Name],[GCType],[GiveType],[ItemType],[ItemIndex],[Level],[ItemDur],[Skill],[Luck],[Opt],[SetOpt],[NewOpt],[BonusSocketOpt],[SocketOpt1],[SocketOpt2],[SocketOpt3],[SocketOpt4],[SocketOpt5],[UsedInfo],[Serial],[RecvDate],[ReceiptDate],[RecvExpireDate],[ItemExpireDate],[RecvDateConvert],[RecvExpireDateConvert],[ItemExpireDateConvert],[ItemOptionEx],[ItemSerial]) VALUES ('".$username."','".$StorageBox[$i]['CharacterName']."', 2, 100, ".$StorageBox[$i]['section'].", ".$StorageBox[$i]['index'].", ".$StorageBox[$i]['level'].", ".$StorageBox[$i]['durability'].", ".$StorageBox[$i]['skill'].", ".$StorageBox[$i]['lucky'].", ".$StorageBox[$i]['option'].", ".$StorageBox[$i]['setOption'].", ".$StorageBox[$i]['newOption'].", ".$StorageBox[$i]['socketBonusPotion'].", ".$StorageBox[$i]['socketOption1'].", ".$StorageBox[$i]['socketOption2'].", ".$StorageBox[$i]['socketOption3'].", ".$StorageBox[$i]['socketOption4'].", ".$StorageBox[$i]['socketOption5'].", ".$StorageBox[$i]['UsedInfo'].", 0, '".$StorageBox[$i]['GetDate']."', NULL, '".$StorageBox[$i]['ExpireDate']."', '1970-01-01 00:00:00', ".time().", ".strtotime($StorageBox[$i]['ExpireDate']).", 0, 0, 0)");
                        //$muOnline->exec("INSERT INTO [IGC_GremoryCase] ([AccountID],[Name],[GCType],[GiveType],[ItemType],[ItemIndex],[Level],[ItemDur],[Skill],[Luck],[Opt],[SetOpt],[NewOpt],[BonusSocketOpt],[SocketOpt1],[SocketOpt2],[SocketOpt3],[SocketOpt4],[SocketOpt5],[UsedInfo],[Serial],[RecvDate],[ReceiptDate],[RecvExpireDate],[ItemExpireDate],[RecvDateConvert],[RecvExpireDateConvert],[ItemExpireDateConvert],[ItemOptionEx],[ItemSerial]) VALUES (:UserGuid, :CharacterName, 2, 100, :section, :index, :level, :durability, :skill, :lucky, :option, :setOption, :newOption, :socketBonusPotion, :socketOption1, :socketOption2, :socketOption3, :socketOption4, :socketOption5, :UsedInfo, 0, :GetDate, NULL, ".$StorageBox[$i]['ExpireDate'].", '1970-01-01 00:00:00', ".time().", ".strtotime($StorageBox[$i]['ExpireDate']).", 0, 0, 0)", $StorageBox[$i]);
                        break;
                    default:
                        throw new Exception("暂不支持您的游戏版本。");
                }
            }
            if($this->tConfig){
                $this->web->query("INSERT INTO [X_TEAM_SHOP_LOG] ([buy_username],[servercode],[buy_character_name],[buy_item_name],[buy_id],[buy_price],[buy_price_type],[buy_date]) VALUES (?,?,?,?,?,?,?,?)",[$username,getServerCodeForGroupID($group),$character_name,$data['item_name'],$data['id'],$data['item_price'],$data['price_type'],logDate()]);
            }
            $muOnline->commit();
            $this->web->commit();
            return alert('/shops','恭喜您购买成功，物品已经存入您的保管盒中！');

        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }

    }

    /**
     * 计算当前商品剩余多少件
     * @param $id
     * @return int|mixed
     * @throws Exception
     */
    public function checkItemCount($id)
    {
        if(!check_value($id)) return 0;
        $count = $this->web->query_fetch_single("SELECT COUNT(*) as count FROM [X_TEAM_SHOP_LOG] WHERE [buy_id] = ?",[$id]);
        if(!is_array($count)) return 0;
        return $count['count'];
    }

    /**
     * 获取商城数据列表
     * @param int $ID
     * @return array|bool|void|null
     */
    public function getShopDataList($ID=0)
    {
        if ($ID){
            if(!check_value($ID)) return;
            $data =  $this->web->query_fetch_single("SELECT * FROM [X_TEAM_SHOP] WHERE [id] = ?",[$ID]);
        }else{
            $data =  $this->web->query_fetch("SELECT * FROM [X_TEAM_SHOP]");
        }
       if(!is_array($data)) return;
       return $data;
    }

    /**
     * 条件查询
     * @param $itemClass
     * @param $itemType
     * @return array|bool|void|null
     * @throws Exception
     */
    public function search($itemClass, $itemType)
    {
        $itemClassWhere = '';
        $itemTypeWhere  = '';

        if ($itemType == 'all') $itemType = null;

        if (!Validator::UnsignedNumber($itemClass) && !array_key_exists($itemClass, $this->classType())) throw new Exception("非常提交，请稍后再试。");

        if (!empty($itemType)){
            if (!Validator::UnsignedNumber($itemType) && !array_key_exists($itemType, $this->shopType())) throw new Exception("非常提交，请稍后再试。");
            $itemTypeWhere = " AND [item_type] = ".$itemType." ";
        }

        if($itemClass>0){
            $itemClassWhere = " AND [class_type] = ".$itemClass." ";
        }

        $query = "SELECT * FROM [X_TEAM_SHOP] WHERE [status] = 1".$itemClassWhere.$itemTypeWhere;
        $data = $this->web->query_fetch($query);
        if(!is_array($data)) return;
        return $data;
    }

    /**
     * 获取商城购买日志
     * @param string $buy_username
     * @param bool $WHERE
     * @return array|bool|mixed|void|null
     */
    public function getShopBuyLog($WHERE = false,$buy_username='')
    {
        if ($WHERE){
            if(!check_value($buy_username)) return;
            $data =  $this->web->query_fetch_single("SELECT * FROM [X_TEAM_SHOP_LOG] WHERE [buy_username] = ?",[$buy_username]);
        }else{
            $data =  $this->web->query_fetch("SELECT * FROM [X_TEAM_SHOP_LOG]");
        }
        if(!is_array($data)) return;
        return $data;
    }

    /**
     * 商品类型归类
     * @return array
     */
    public function shopType()
    {
        return $shopType = [
            0 => '礼包',
            1 => '武器',
            2 => '防具',
            3 => '首饰',
            4 => '翅膀',
            5 => '守护',
            7 => '宝石',
            8 => '其他',
        ];
    }

    public function classType($class=null)
    {

        return  $classType = [
            0 => '所有职业',
            1 => '魔法师',
            2 => '剑士',
            3 => '弓箭手',
            4 => '魔剑士',
            5 => '圣导师',
            6 => '召唤师',
            7 => '角斗士',
        ];
    }
/******************************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
    public function loadModule($module) {
        if(!Validator::Alpha($module)) throw new Exception('无法加载在线商城插件模块。');
        if(!$this->_moduleExists($module)) throw new Exception('无法加载在线商城插件模块。');
        if(!@include_once(__PATH_PLUGIN_SHOP_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new Exception('无法加载在线商城插件模块。');
    }


    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
    private function _moduleExists($module) {
        if(!check_value($module)) return;
        if(!file_exists(__PATH_PLUGIN_SHOP_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
        return true;
    }

    /**
     * 加载配置文件
     * @param string $file
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($file = 'config')
    {
        $xmlPath = __PATH_PLUGIN_SHOP_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('在线商城配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载在线商城插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}