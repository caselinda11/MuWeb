<?php
/**
 * [FlagTransfer]类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Connection;
use Validator;
use common;
use Token;
use CreditSystem;
use Plugin\equipment;
use Plugin\Market\warehouse;
use Character;

class FlagTransfer {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;
    private $NULL,$equipment,$_CharacterData,$_InventoryData,$flagCode;
    /**
     * 构造函数
     * FlagTransfer constructor.
     * @throws Exception
     */
	public function __construct()
    {
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        #网站库
        $this->web = Connection::Database("Web");
        #配置文件
        $this->config = $this->loadConfig();
        #基类函数
        $this->common = new common();

        $this->equipment = new equipment();
        #空物品值
        $this->NULL = str_pad("F",ITEM_SIZE,"F");
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */

    /**
     * 获取角色背包
     * @param $group
     * @param $username
     * @return mixed|void|null
     * @throws Exception
     */
    public function getCharacterInventory($group,$username)
    {
        if (!check_value($group)) return;
        if (!check_value($username)) return;

        $query = "SELECT Name,Class,CONVERT(varchar(max),Inventory,2) AS Inventory FROM Character WHERE AccountID = ? AND CtlCode = 0";
        $result = Connection::Database('MuOnline', $group)->query_fetch($query,[$username]);
        if (!is_array($result)) return;
        $this->_InventoryData = $result;
        foreach ($result as &$item){
            #过滤返回
            $item['Inventory'] = array_filter((array)$this->equipment->setEquipmentCode($item['Inventory']));
        }
        $this->_CharacterData = $result;
        return $this->_CharacterData;
    }

    /**
     * 校验角色背包中是否有旗帜
     * @param $id
     * @return int
     * @throws Exception
     */
    private function checkInventoryFlag($id){
        if (!check_value($id)) return 0;
        if (!is_array($this->_CharacterData)) return 0;
        if(isset($this->_CharacterData[$id])){
            if(!empty($this->_CharacterData[$id]['Inventory'])){
                if(is_array($this->_CharacterData[$id]['Inventory'])){
                    foreach ($this->_CharacterData[$id]['Inventory'] as $inventory){
                        $data = $this->equipment->convertItem($inventory);
                        if(is_array($data)){
                            if($data['type'] == 6676 && $data['level'] == 3) {
                                $this->flagCode = $inventory;
                                return 1;
                            }
                        }
                    }
                }

            }
        }
        $this->flagCode = 0;
        return 0;
    }

    /**
     * 发送旗帜
     * @param $group
     * @param $username
     * @param $id
     * @param $receive_char
     * @throws Exception
     */
    public function setFlagTransfer($group, $username, $id, $receive_char)
    {
        if(!Token::checkToken('FlagTransfer')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($id)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($receive_char)) throw new Exception('出错了，请您重新输入！');
        $char = new Character();
        if(!$char->_checkCharacterExists($group,$receive_char)) throw new Exception("接受方角色名错误，请重新输入。");
        $receive_username = $this->common->getUsernameForCharacterName($group,$receive_char);
        if(!$receive_username) throw new Exception("接受方角色名错误，请重新输入。");
        if(!$this->config['receive']) if($id == $receive_char) throw new Exception("操作失败，您不能转移旗帜给自己。");
        if($this->common->checkUserOnline($group,$username)) throw new Exception("检测到您的账号在线，请断开连接再尝试。");
        if($this->common->checkUserOnline($group,$receive_username)) throw new Exception("检测到接受方账号在线，请联系对方断开连接再尝试。");
        if(!$this->checkInventoryFlag($id)) throw new Exception('您的角色[<strong>'.$this->_CharacterData[$id]['Name'].'</strong>]背包中没有识别到<strong>纪念戒指</strong>!');
        if(!$this->flagCode) throw new Exception('无法识别您背包中的[<strong>纪念戒指</strong>]，请联系在线客服。');

        #重组背包数据
        $new_Inventory = null;
        $Inventory = str_split($this->_InventoryData[$id]['Inventory'], ITEM_SIZE);
        $itemId = array_search($this->flagCode,$Inventory);
        $Inventory[$itemId] = $this->NULL;
        $new_Inventory = implode('',$Inventory);

        $muOnline = Connection::Database("MuOnline",$group);
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($this->config['credit_type']);
        $configSettings = $creditSystem->showConfigs(true);
        #操作数据
        $receive_userID = $this->common->getUserGIDForUsername($group,$receive_username);
        if(!$receive_userID) throw new Exception("提交错误，请重新输入。");
        $itemCode = $this->equipment->convertItem($this->flagCode);
        $StorageBox = [
            'UserGuid'      => $receive_userID,
            'CharacterName' => $receive_char,
            'Type'          => 1,
            'ItemCode'      => $itemCode['type'],
            'ItemData'      => $this->flagCode,
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

        #操作仓库
//        $warehouse = new warehouse($group,$receive_username);
//        $newWarehouse = $warehouse->warehouseAddItem($this->flagCode);

        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            #减积分
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($username);
                    break;
                case 'userid':
                    $creditSystem->setIdentifier($_SESSION['userid']);
                    break;
                case 'character':
                    $creditSystem->setIdentifier($_SESSION['character']);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group,$this->config['credit_price']);

            $muOnline->query("UPDATE [Character] SET [Inventory] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ? AND [Name] = ?",[$new_Inventory, $username, $this->_CharacterData[$id]['Name']]);
            switch ($this->serverFiles){
                case "egames":
                    $query = "INSERT INTO [T_StorageBox] ([UserGuid],[CharacterName],[Type],[ItemCode],[ItemData],[ValueType],[ValueCnt],[CustomData],[GetDate],[ExpireDate],[UsedInfo]) VALUES (".$StorageBox['UserGuid'].", '".$StorageBox['CharacterName']."', ".$StorageBox['Type'].", '".$StorageBox['ItemCode']."', '".$StorageBox['ItemData']."', '".$StorageBox['ValueType']."', ".$StorageBox['ValueCnt'].", ".$StorageBox['CustomData'].", '".$StorageBox['GetDate']."', '".$StorageBox['ExpireDate']."', ".$StorageBox['UsedInfo'].")";
                    $muOnline->query($query);
                    break;
                case "igcn":
                    $muOnline->query("INSERT INTO [IGC_GremoryCase] ([AccountID],[Name],[GCType],[GiveType],[ItemType],[ItemIndex],[Level],[ItemDur],[Skill],[Luck],[Opt],[SetOpt],[NewOpt],[BonusSocketOpt],[SocketOpt1],[SocketOpt2],[SocketOpt3],[SocketOpt4],[SocketOpt5],[UsedInfo],[Serial],[RecvDate],[ReceiptDate],[RecvExpireDate],[ItemExpireDate],[RecvDateConvert],[RecvExpireDateConvert],[ItemExpireDateConvert],[ItemOptionEx],[ItemSerial]) VALUES ('".$username."','".$StorageBox['CharacterName']."', 2, 100, ".$StorageBox['section'].", ".$StorageBox['index'].", ".$StorageBox['level'].", ".$StorageBox['durability'].", ".$StorageBox['skill'].", ".$StorageBox['lucky'].", ".$StorageBox['option'].", ".$StorageBox['setOption'].", ".$StorageBox['newOption'].", ".$StorageBox['socketBonusPotion'].", ".$StorageBox['socketOption1'].", ".$StorageBox['socketOption2'].", ".$StorageBox['socketOption3'].", ".$StorageBox['socketOption4'].", ".$StorageBox['socketOption5'].", ".$StorageBox['UsedInfo'].", 0, '".$StorageBox['GetDate']."', NULL, '".$StorageBox['ExpireDate']."', '1970-01-01 00:00:00', ".time().", ".strtotime($StorageBox['ExpireDate']).", 0, 0, 0)");
                    break;
                default:
                    throw new Exception("暂不支持您的游戏版本。");
            }
            //            $muOnline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$newWarehouse, $receive_username]);
            if($this->config['log']) {
                $this->web->query("INSERT INTO [X_TEAM_FLAG_TRANSFER_LOG] ([send_username],[send_Name],[servercode],[receive_username],[date]) VALUES (?,?,?,?,?)", [$username, $this->_CharacterData[$id]['Name'], getServerCodeForGroupID($group), $receive_username, logDate()]);
            }
            $muOnline->commit();
            $this->web->commit();
            alert('usercp/TransferFlag','恭喜您旗帜转移成功，旗帜已转移至对方储物柜，点击确定回到主界面！');

        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }

    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[旗帜转移]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[旗帜转移]插件模块。');
		if(!@include_once(__PATH_FLAG_TRANSFER_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[旗帜转移]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_FLAG_TRANSFER_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

    /**
     * 加载配置文件
     * @param string $file
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($file='config')
    {
        $xmlPath = __PATH_FLAG_TRANSFER_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[旗帜转移]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[旗帜转移]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}