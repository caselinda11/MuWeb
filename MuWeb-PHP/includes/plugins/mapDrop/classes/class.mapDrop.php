<?php
/**
 * [mapDrop]类相关函数
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

class mapDrop {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;
    private $path = __ROOT_DIR__.'public/EachMonsterMapDrop/';
    private $monsterFile = __ROOT_DIR__.'public/MonsterList.xml';
    public $monsterList = [];
    /**
     * 构造函数
     * mapDrop constructor.
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
        $this->loadMonsterFile();
    }
    /***********************************************************************/

    public function localXml($xmlPath){
        $xml = '未找到该地图的掉落设置';
        if (file_exists($xmlPath)) {
            $xml = simplexml_load_file($xmlPath);
        }
        $XmlJson= json_encode($xml);
        $xml=json_decode($XmlJson,true);

        return $xml;
    }

    //取文件名
    public function map($id=0)
    {
        $result = '';
        global $custom;
        foreach ($custom['map_list'] as $key => $value) {
            if ($id == $key) {
                $result.='MonsterMap_('.$key.')_Bag.xml';
            }
        }
        return $result;
    }

    public function getMapData($id){
        $mapFile = $this->map($id);
        $xmlPath = $this->localXml($this->path.$mapFile);
        if(!is_array($xmlPath)) {
            return FALSE;
        }
        return $xmlPath['DropItem'];
    }

    /**
     * @return array
     */
    public function loadMonsterFile(){
        $xmlPath = $this->localXml($this->monsterFile);
        if(is_array($xmlPath['Monster']))
            foreach ($xmlPath['Monster'] as $value){
            $monster = $value['@attributes'];
            $this->monsterList[$monster['Index']] = [
                'Name'                  => $monster['Name'],
                'Level'                 => $monster['Level'],
                'HP'                    => $monster['HP'],
                'MP'                    => $monster['MP'],
                'RegenTime'             => $monster['RegenTime'],
                'AttackRange'           => $monster['AttackRange'],
                'ViewRange'             => $monster['ViewRange'],
                'DamageMin'             => $monster['DamageMin'],
                'DamageMax'             => $monster['DamageMax'],
                'Defense'               => $monster['Defense'],
                'MagicDefense'          => $monster['MagicDefense'],
                'PentagramDamageMin'    => $monster['PentagramDamageMin'],
                'PentagramDamageMax'    => $monster['PentagramDamageMax'],
                'PentagramDefense'      => $monster['PentagramDefense'],
            ];
        }
        return $this->monsterList;
    }

    public function getMonsterName($id){
        $result = '未知怪物';
        foreach ($this->monsterList as $key=>$value){
            if($id == $key) $result =  $value['Name'];
        }
        return $result;
    }

    public function f3($str)
    {
        $result = array();
        preg_match_all("/(?:\()(.*)(?:\))/i",$str, $result);
        return $result[1][0];
    }

    public function setItemExc($id){
        if('-2' == $id){
            $result = '有';
        }else {
            $result = '-';
        }
        return $result;
    }
    public function setItemSocketCount($id){
        switch ($id){
            case '1;1':
                $result = '1~1';
                break;
            case '1;2':
                $result = '1~2';
                break;
            case '1;3':
                $result = '1~3';
                break;
            case '1;4':
                $result = '1~4';
                break;
            case '1;5':
                $result = '1~5';
                break;
            default:
                $result = '-';
                break;
        }
        return $result;
    }

    public function setItemElement($id){
        switch ($id){
            case '1':
                $result = '火';
                break;
            case '2':
                $result = '水';
                break;
            case '3':
                $result = '土';
                break;
            case '4':
                $result = '风';
                break;
            case '5':
                $result = '暗';
                break;
            case '-1':
                $result = '随机';
                break;
            case '-2':
                $result = '随';
                break;
            default:
                $result = '-';
                break;
        }
        return $result;
    }

    public function setItemLucky($id){
        switch ($id){
            case '-1':
                $result = '随机';
                break;
            case '1':
                $result = '有';
                break;
            default:
                $result = '-';
                break;
        }
        return $result;
    }
    public function setItemSkill($id){
        switch ($id){
            case '-1':
                $result = '随机';
                break;
            case '1':
                $result = '有';
                break;
            default:
                $result = '-';
                break;
        }
        return $result;
    }

    public function setItemOption($id){
        switch ($id){
            case '-1':
                $result = '随机';
                break;
            case '-2':
            case '-3':
                $result = '+12';
                break;
            default:
                $result = '-';
                break;
        }
        return $result;
    }

    /**
     * 放置您的自定义方法
     */

    /**
     * @return mixed
     */
    public function loadMapDropList()
    {
        $dir = opendir($this->path);
        while(($d = readdir($dir)) == true){

            #不让.和..出现在读取出的列表里
            if($d == '.' || $d == '..') continue;

            $FileName[$this->f3($d)] = $this->outputName($this->f3($d));
        }
        ksort($FileName);
        return array_filter($FileName);
    }


    /**
     * @param $id
     * @return mixed
     */
    public function outputName($id)
    {
        global $custom;
        foreach ($custom['map_list'] as $key => $value) {
            if ($id == $key) {
                return $value;
            }
        }
        return null;
    }

    /**
     * @param int $id
     * @return string
     * @throws Exception
     */
    public function getMapFileName($id=0)
    {
        $file = $this->loadMapDropList();
        if(!array_key_exists($id,$file)) throw new Exception("文件识别错误，无法打开该地图文件。");
        $xmlPath = $this->path.'MonsterMap_('.$id.')_Bag.xml';
        $fh = fopen("$xmlPath",'r') or die($php_errormsg);
        $simple = fread($fh,filesize("$xmlPath"));
        fclose($fh) or die($php_errormsg);

        $p = xml_parser_create();
        xml_parse_into_struct($p, $simple, $values, $index);
        xml_parser_free($p);
        debug($values);
//        $meta = [
//            '' => $vals[''];
//        ];
        return $values;
    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[地图掉落]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[地图掉落]插件模块。');
		if(!@include_once(__PATH_MAP_DROP_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[地图掉落]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_MAP_DROP_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_MAP_DROP_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[地图掉落]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[地图掉落]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}