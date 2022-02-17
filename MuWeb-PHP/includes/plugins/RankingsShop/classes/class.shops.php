<?php
/**
 * 商城消费排名类相关函数
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
use CreditSystem;

class Shop {

	private $_modulesPath = 'modules';
    private $_results,$_excludedCharacters;
    private $serverFiles;
    private $mconfig;

    /**
     * 构造函数
     * shops constructor.
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
		if(!Validator::Alpha($module)) throw new Exception('无法加载消费排名模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载消费排名模块。');
		if(!@include_once(__PATH_PLUGIN_RANKING_SHOPS_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new Exception('无法加载消费排名模块。');
	}

    /**
     * 更新缓存文件
     * @param $type
     * @throws Exception
     */
	public function UpdateRankingCache($type){
	    if('shops' != $type) return;
        $this->_shopsRanking();
    }

    /**
     * 生成缓存文件
     * @throws Exception
     */
    private function _shopsRanking(){
        $result = $this->getShopsData();
        if(!is_array($result)) return;
        $cache = BuildCacheData($result);
        updateCacheFile('rankings_shops.cache', $cache);
    }

    /**
     * 获取财富排名数据
     * @return array|void
     * @throws Exception
     */
    public function getShopsData(){
        $Character = new Character();
        $query = "SELECT TOP ".$this->_results."
					  [buy_username]
                      ,[buy_character_name]
                      ,[buy_item_code]
                      ,[buy_price]
                      ,[buy_price_type]
                      ,[buy_date]
                      ,[servercode]
                      FROM [X_TEAM_SHOP_LOG]
                      WHERE [buy_character_name] NOT IN(".$this->_rankingsExcludeChars().")
                      ORDER BY [buy_date] DESC";
        $result = Connection::Database('Web')->query_fetch($query);
        if(!is_array($result)) return;
        foreach ($result as $keys=>$data){
            $char[$keys] = $Character->getCharacterDataForCharacterName(getGroupIDForServerCode(0), $data['buy_character_name']);
            if (empty($char[$keys])) continue;
            $OnlineData = $Character->getOnlineDataForUsername(getGroupIDForServerCode(0),$data['buy_username']);
            if(!is_array($OnlineData)) continue;
            #组装数组
            $newData[$keys] = array_replace_recursive($data,$char[$keys],$OnlineData);
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
		if(!file_exists(__PATH_PLUGIN_RANKING_SHOPS_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
     * 获取货币类型
     * @param $PriceType
     * @return mixed|string
     * @throws Exception
     */
    public function getPriceType($PriceType){
        $creditSystem = new CreditSystem();
        $creditConfigList = $creditSystem->showConfigs();
        if(is_array($creditConfigList)) {
            foreach($creditConfigList as $myCredits) {
                if($PriceType==$myCredits['config_id']){
                    return $myCredits['config_title'];
                }
            }
        }
        return '未知';
    }
    /**
     * 加载配置文件
     * @param string $file
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($file='config')
    {
        $xmlPath = __PATH_PLUGIN_RANKING_SHOPS_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('消费排名插件配置文件不存在，请重新上传。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载消费排名配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}