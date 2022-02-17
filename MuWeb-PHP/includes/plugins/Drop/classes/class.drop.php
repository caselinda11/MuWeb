<?php
/**
 * [溯源系统]类相关函数
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

class drop {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;

    /**
     * 构造函数
     * myPlugin constructor.
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
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */
    function loadFile($path)
    {
        $path = $path.'/';
        $dir = opendir($path);
        while(($d = readdir($dir)) == true){
            #不让.和..出现在读取出的列表里
            if($d == '.' || $d == '..') continue;
            $key = strstr($d,'_',1); //把文件中_前面的日期作为键
            if(!is_numeric($key)) continue; //过滤掉不是日期的文件
            #文件不存在则跳过
            if (!file_exists($path.$d)) continue;
            $FileName[$key] = $path.$d;
        }
        ksort($FileName);
        return array_filter($FileName);
    }



    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('[溯源系统]该模块不存在。');
		if(!$this->_moduleExists($module)) throw new Exception('[溯源系统]该模块不存在。');
		if(!@include_once(__PATH_DROP_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('[溯源系统]该模块不存在。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_DROP_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
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
        $xmlPath = __PATH_DROP_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[溯源系统]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('[溯源系统]无法读取配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}