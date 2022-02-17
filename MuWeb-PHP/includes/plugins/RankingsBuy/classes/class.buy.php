<?php
/**
 * 充值排名类相关函数
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

class buy {

	private $_modulesPath = 'modules';
    private $_results,$_excludedUsername;
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
        $this->mconfig = $this->loadConfig('config');
        // 排除角色
        if(check_value($this->mconfig['excluded_username'])) {
            $excludedUsername = explode(",", (string)$this->mconfig['excluded_username']);
            $this->_excludedUsername = $excludedUsername;
        }
    }

    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[充值排名]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[充值排名]插件模块。');
		if(!@include_once(__PATH_PLUGIN_RANKING_BUY_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new Exception('无法加载[充值排名]插件模块。');
	}

    /**
     * 更新缓存文件
     * @param $type
     * @throws Exception
     */
	public function UpdateRankingCache($type){
	    if('buy' != $type) return;
        $this->_buyRanking();
    }

    /**
     * 生成缓存文件
     * @throws Exception
     */
    private function _buyRanking(){
        $result = $this->getBuyData();
        if(!is_array($result)) return;
        $cache = BuildCacheData($result);
        updateCacheFile('rankings_buy.cache', $cache);
    }

    /**
     * 获取财富排名数据
     * @return array|void
     * @throws Exception
     */
    public function getBuyData(){
        $Character = new Character();
        #取分区数组
        global $serverGrouping;
        foreach ($serverGrouping AS $code=>$item){
            $query = "SELECT TOP ".$this->_results."
                       [memb___id]
                      ,[bloc_code]
                      ,".$this->mconfig['jf_Field']."
                      ,".$this->mconfig['yb_Field']."
                       FROM [MEMB_INFO]
                       WHERE [memb___id] NOT IN(".$this->_rankingsExcludeUsername().")
                       AND ".$this->mconfig['jf_Field']." > 0
                       AND ".$this->mconfig['yb_Field']." > 0
                       ORDER BY ".$this->mconfig['jf_Field']." DESC,".$this->mconfig['yb_Field']." DESC";
            $result = Connection::Database("Me_MuOnline",$code)->query_fetch($query);
            if(!is_array($result)) return;
            foreach ($result as $keys=>$data){
                $OnlineData = $Character->getOnlineDataForUsername((int)$code,$data['memb___id']);
                if(!is_array($OnlineData)) continue;
                #组装数组
                $newData[$keys] = array_replace_recursive((array)$code,$data,$OnlineData);
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
		if(!file_exists(__PATH_PLUGIN_RANKING_BUY_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

    /**
     * 排除指定角色
     * @return string|void
     */
    private function _rankingsExcludeUsername() {
        if(!is_array($this->_excludedUsername)) return;
        $return = [];
        foreach($this->_excludedUsername as $username) {
            $return[] = "'".$username."'";
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
        $xmlPath = __PATH_PLUGIN_RANKING_BUY_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[充值排名]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[充值排名]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}