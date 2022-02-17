<?php
/**
 * 封停排名类相关函数
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

class Ban {

	private $_modulesPath = 'modules';
    private $_results,$_excludedCharacters;
    private $serverFiles;
    private $mconfig;

    /**
     * 构造函数
     * Ban constructor.
     * @throws Exception
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
		if(!Validator::Alpha($module)) throw new Exception('无法加载封停插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载封停插件模块。');
		if(!@include_once(__PATH_PLUGIN_RANKING_BAN_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) throw new Exception('无法加载封停插件模块。');
	}

    /**
     * 更新缓存文件
     * @param $type
     * @throws Exception
     */
	public function UpdateRankingCache($type){
	    if('bans' != $type) return;
        $this->_BanRanking();
    }

    /**
     * 生成缓存文件
     * @throws Exception
     */
    private function _BanRanking(){
        $result = $this->getBanData();
        if(!is_array($result)) return;
        $cache = BuildCacheData($result);
        updateCacheFile('rankings_ban.cache', $cache);
    }

    /**
     * 获取财富排名数据
     * @return array|void
     * @throws Exception
     */
    public function getBanData(){
	    global $serverGrouping;
	    foreach ($serverGrouping as $code=>$item){
             $Db = ($item['SQL_USE_2_DB']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
                $query = "SELECT TOP ".$this->_results." 
                            AccountSystem.memb___id,
                            AccountSystem.bloc_code,
                            CharacterSystem.Name,
                            CharacterSystem.Class,
                            CharacterSystem.cLevel
                            FROM [Character] AS CharacterSystem
                            INNER JOIN [".$Db."].[dbo].MEMB_INFO AS AccountSystem
                            ON CharacterSystem.AccountID = AccountSystem.memb___id
                            collate Chinese_PRC_CI_AS
                            WHERE CharacterSystem.Name NOT IN(".$this->_rankingsExcludeChars().")
                            AND AccountSystem.bloc_code = 1 
                            AND AccountSystem.servercode = ".$item['SERVER_GROUP']."
                            ORDER BY CharacterSystem.cLevel desc";
            $result[$code] = Connection::Database('Me_MuOnline',$code)->query_fetch($query);
            if(empty($result[$code])) continue;

            foreach ($result[$code] as $data){
                #组装数组
                $newData[] = array_replace_recursive((array)$item['SERVER_GROUP'],$data);
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
		if(!file_exists(__PATH_PLUGIN_RANKING_BAN_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_PLUGIN_RANKING_BAN_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('封停插件配置文件不存在，请重新上传。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载封停插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}