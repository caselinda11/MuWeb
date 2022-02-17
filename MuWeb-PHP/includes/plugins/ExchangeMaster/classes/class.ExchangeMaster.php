<?php
/**
 * [大师铸造]类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Connection;
use Validator;
use Plugin\equipment;
use Plugin\Market\warehouse;
use common;
use Token;
use Character;
class ExchangeMaster {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$item,$NULL,$common,$wareData;
    public $count = 0;#物品总数
    /**
     * 构造函数
     * ExchangeMaster constructor.
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
        #空物品值
        $this->NULL = str_pad("F",ITEM_SIZE,"F");
        $this->item = new equipment();
        $this->common = new common();
        $this->getItemsCount($_SESSION['group']);
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */

    /**
     * 兑换大师
     * @param $group
     * @param $username
     * @param $char_name
     * @param $number
     * @throws Exception
     */
    public function setItemsExchange($group, $username, $char_name, $number)
    {
        if(!Token::checkToken('ExchangeMaster')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($char_name)) throw new Exception('出错了，请您重新输入！');
        if(!$number) throw new Exception('请正确输入需要兑换的数量！');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('您的账号已在线，请断开连接。');
        $itemNumber = $this->config['item_number']*$number;
        if($this->count < $itemNumber) throw new Exception('您仓库的'.$this->config['item_name'].'不足，请重新输入数量。');
        $Character = new Character();
        $charData = $Character->getCharacterDataForCharacterName($group, $char_name);
        if(!is_array($charData)) throw new Exception('操作失败，无法获取您的角色数据。');
        if($charData['mLevel'] < $this->config['min_master'])  throw new Exception('您选择的角色大师必须达到'.$this->config['min_master'].'级才可使用此功能。');
        if($charData['mLevel'] > $this->config['max_master']) throw new Exception('操作失败，您选择的角色大师已经达到最高等级。');
        if(($charData['mLevel'] + $number) > $this->config['max_master']) throw new Exception('操作失败，您最多还可兑换['.$this->config['max_master'] - $charData['mLevel'].']级！');

        $ware = new warehouse($group, $username);

        if (!$ware->deleteWarehouseItemForItem($this->config['items'],$itemNumber)) throw new Exception("操作失败，请确保仓库中有足够物品数量。");

        #组装
        $newWarehouse = join("",$ware->warehouseList).$ware->extendWarehouseData;;

        $addMaster = $number * $this->config['master_level'];
        $addMasterPoint = $number * $this->config['master_point'];
        $point = $number * $this->config['point'];
        $muOnline = Connection::Database("MuOnline",$group);
        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();

            $muOnline->query("UPDATE "._TBL_MASTERLVL_." SET "._CLMN_ML_LVL_." = "._CLMN_ML_LVL_." + ?,"._CLMN_ML_POINT_." = "._CLMN_ML_POINT_." + ? WHERE "._CLMN_ML_NAME_." = ?",[$addMaster,$addMasterPoint,$char_name]);
            $muOnline->query("UPDATE ["._TBL_CHR_."] SET "._CLMN_CHR_LVLUP_POINT_." = "._CLMN_CHR_LVLUP_POINT_." + ? WHERE "._CLMN_CHR_NAME_." = ?",[$point,$char_name]);
            $muOnline->query("UPDATE [warehouse] SET [Items] = CONVERT(VARBINARY(MAX),?,2) WHERE [AccountID] = ?",[$newWarehouse,$username]);

            if($this->config['log']) {
                $this->web->query("INSERT INTO [X_TEAM_EXCHANGE_MASTER_LOG] ([AccountID],[Name],[servercode],[exchange_number],[date]) VALUES (?,?,?,?,?)",[$username,$char_name,getServerCodeForGroupID($group),$number,logDate()]);
            }

            $muOnline->commit();
            $this->web->commit();
            alert('usercp/ExchangeMaster','恭喜您角色大师铸造成功，点击确定返回。');

        }catch (Exception $exception){

            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }

    }

    /**
     * @param $group
     * @throws Exception
     */
    public function getItemsCount($group)
    {
        $ware = new warehouse($group);
        $this->wareData = $ware->warehouseList;
        if(!is_array($this->wareData)) throw new Exception("获取仓库失败!");
        foreach ($this->wareData as $list){
            if($list == $this->NULL) continue;
            $items = $this->item->convertItem($list);
            if($items['type'] == $this->config['items']) $this->count++;
        }
    }


    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[大师铸造]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[大师铸造]插件模块。');
		if(!@include_once(__PATH_Exchange_Master_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[大师铸造]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_Exchange_Master_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_Exchange_Master_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[大师铸造]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[大师铸造]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}