<?php
/**
 * 我的插件类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin\Rankings;

use Exception;
use Connection;
use Character;
use Validator;

class Money {

	private $_modulesPath = 'modules';
    private $_results,$_excludedCharacters;
    private $serverFiles;
    private $mconfig;

    /**
     * 构造函数
     * Money constructor.
     */
	public function __construct()
    {
        // 排名配置
        loadModuleConfigs('rankings');
        $this->_results = (check_value(mconfig('results')) ? mconfig('results') : 25);
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);

        // 排除角色
        if(check_value(mconfig('excluded_characters'))) {
            $excludedCharacters = explode(",", (string)mconfig('excluded_characters'));
            $this->_excludedCharacters = $excludedCharacters;
        }
        $this->mconfig = $this->loadConfig('config');
    }

    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载财富插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载财富插件模块。');
		if(!@include_once(__PATH_PLUGIN_RANKING_MONEY_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new Exception('无法加载财富插件模块。');
	}

    /**
     * 更新缓存文件
     * @param $type
     * @throws Exception
     */
	public function UpdateRankingCache($type){
	    if('money' != $type) return;
        $this->_moneyRanking();
    }

    /**
     * 生成缓存文件
     * @throws Exception
     */
    private function _moneyRanking(){
        $result = $this->getMoneyData();
        if(!is_array($result)) return;
        $cache = BuildCacheData($result);
        updateCacheFile('rankings_money.cache', $cache);
    }

    /**
     * 获取财富排名数据
     * @return array|void
     * @throws Exception
     */
    public function getMoneyData(){
        $character = new Character();

	    global $serverGrouping;
	    foreach ($serverGrouping as $code=>$item){
            $Db = ($item['SQL_USE_2_DB']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
            switch ($this->serverFiles){
                case "igcn":
                $query = "SELECT TOP ".$this->_results."
						AccountCharacter.Id AS AccountID,
                        AccountCharacter.GameIDC,
                        GameShopSystem.WCoin,
                        GameShopSystem.GoblinPoint
                        from AccountCharacter AS AccountCharacter
                        LEFT JOIN [T_InGameShop_Point] AS GameShopSystem
                        ON AccountCharacter.Id=GameShopSystem.AccountID
						LEFT JOIN [".$Db."].[MEMB_INFO] AS AccountSystem
                        ON AccountCharacter.Id=AccountSystem.memb___id
                        collate Chinese_PRC_CI_AS 
                        WHERE AccountCharacter.GameIDC NOT IN(".$this->_rankingsExcludeChars().")
                        AND AccountSystem.servercode = ".$item['SERVER_GROUP']."
                        ORDER BY GameShopSystem.WCoin DESC,GameShopSystem.GoblinPoint DESC";
                break;
                case "egames":
                case "haoyi":
                $query = "SELECT TOP ".$this->_results."
						AccountCharacter.Id AS AccountID,
						AccountCharacter.GameIDC,
						GameShopSystem.jf AS WCoin,
						GameShopSystem.yb AS GoblinPoint
                        from AccountCharacter AS AccountCharacter
                        LEFT JOIN [".$Db."].[MEMB_INFO] AS GameShopSystem
                        ON AccountCharacter.Id = GameShopSystem.memb___id
                        collate Chinese_PRC_CI_AS 
                        WHERE AccountCharacter.GameIDC NOT IN(".$this->_rankingsExcludeChars().")
                        AND GameShopSystem.servercode = ".$item['SERVER_GROUP']."
                        ORDER BY GameShopSystem.jf DESC,GameShopSystem.yb DESC";
                break;
                default:
                    return;
            }
            $result[$code] = Connection::Database('MuOnline',$code)->query_fetch($query);
            if(empty($result[$code])) continue;

            foreach ($result[$code] as $data){
                $Player = Connection::Database('MuOnline', $code)->query_fetch_single("SELECT Class,Money FROM Character WHERE Name = ?",[$data['GameIDC']]);
                if(!is_array($Player)) continue;
                $OnlineData = $character->getOnlineDataForUsername($code,$data['AccountID']);
                if(!is_array($OnlineData)) continue;

                #组装数组
                $newData[] = array_replace_recursive((array)$item['SERVER_GROUP'],$Player,$data,$OnlineData);
            }
        }
        if(empty($newData)) return;
        #排序
        return $newData;
    }

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_PLUGIN_RANKING_MONEY_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

    /**
     * 排除指定角色
     * @return string|void
     */
    private function _rankingsExcludeChars() {
        if(!is_array($this->_excludedCharacters)) return;
        $return = [];
        foreach($this->_excludedCharacters as $characterName) {
            $return[] = "'".$characterName."'";
        }
        return implode(",", $return);
    }

    /**
     * 加载配置文件
     * @param string $file
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($file='config')
    {
        $xmlPath = __PATH_PLUGIN_RANKING_MONEY_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('财富插件配置文件不存在，请重新上传。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载财富插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}