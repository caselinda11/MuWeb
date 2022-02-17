<?php
/**
 * 插件类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Plugins {

    private $db;

    /**
     * 构造方法
     * Plugins constructor.
     * @throws Exception
     */
    function __construct() {
		$this->db = Connection::Database('Web');
	}

    /**
     * 插件上传
     * @param $_FILE
     * @throws Exception
     */
	public function importPlugin($_FILE) {
	    #验证类型
		if($_FILE["file"]["type"] == "text/xml") {
			$xml = simplexml_load_file($_FILE["file"]["tmp_name"]);
			$pluginData = convertXML($xml->children());
			if($this->checkXML($pluginData)) {
			    #验证版本兼容性
				if($this->checkCompatibility($pluginData['compatibility'])) {
				    #验证检查插件目录是否存在
					if($this->checkPluginDirectory($pluginData['folder'])) {
					    #验证文件是否存在
						if($this->checkFiles($pluginData['files'],$pluginData['folder'])) {
                            #执行SQL文件
                            if($this->installSqlQuery($pluginData['folder'],'Script.sql')){
                                # 安装插件
                                $install = $this->installPlugin($pluginData);
                                if($install) {
                                    message('success','插件成功导入，2秒后刷新此页面。');
                                    redirect(2,'admincp/?module=Plugins_Install',2);
                                } else {
                                    message('error','无法导入插件。');
                                }
                            }else{
                                message('error','数据库文件执行失败，请手动操作!');
                            }
							$update_cache = $this->rebuildPluginsCache();
							if(!$update_cache) {
								message('error','无法更新插件缓存数据，请确保文件存在并且可写！');
							}
						} else { message('error','缺少插件文件。'); }

					} else { message('error','找不到插件文件夹，请确保将其上传到正确的路径。'); }
				} else { message('error','该插件与您当前的版本不兼容。'); }
			} else { message('error','无效的文件或缺少数据。'); }
		} else { message('error','无效的文件类型（仅XML）。'); }
	}

    /**
     * 识别xml
     * @param $array
     * @return bool
     */
	private function checkXML($array) {
		if(array_key_exists('name',$array)
		&& array_key_exists('author',$array)
		&& array_key_exists('version',$array)
		&& array_key_exists('compatibility',$array)
		&& array_key_exists('folder',$array)
		&& array_key_exists('files',$array)) {
			if(check_value($array['name'])
			&& check_value($array['author'])
			&& check_value($array['version'])
			&& check_value($array['folder'])) {
				if(is_array($array['compatibility']) && is_array($array['files'])) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

    /**
     * 验证版本信息
     * @param $array
     * @return bool
     */
	private function checkCompatibility($array) {
		if(array_key_exists('version',$array)) {
			if(is_array($array['version'])) {
				if(in_array(__X_TEAM_VERSION__,$array['version'])) {
					return true;
				} else {
					return false;
				}
			} else {
				if(__X_TEAM_VERSION__ >= $array['version']) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

    /**
     * 验证插件目录是否存在
     * @param $name
     * @return bool
     */
	private function checkPluginDirectory($name) {
		if(file_exists($this->pluginPath($name)) && is_dir($this->pluginPath($name))) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * 验证文件是否存在
     * @param $array   //文件名
     * @param $plugin_name //插件名
     * @return bool
     */
	private function checkFiles($array,$plugin_name) {
		if(array_key_exists('file',$array)) {

			if(is_array($array['file'])) {
				$error = false;
				foreach($array['file'] as $thisFile) {
					$file = $this->pluginPath($plugin_name).$thisFile;
					if(!file_exists($file)) {
						$error = true;
					}
				}
				if($error) {
					return false;
				} else {
					return true;
				}
			} else {

				$file = $this->pluginPath($plugin_name).$array['file'];
				if(file_exists($file)) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

    /**
     * 插件目录
     * @param $name
     * @return string
     */
	public function pluginPath($name) {
		return __PATH_INCLUDES_PLUGINS__.$name.'/';
	}

    /**
     * 安装插件
     * @param $pluginData
     * @return bool
     */
	private function installPlugin($pluginData) {
		$compatibility = $pluginData['compatibility']['version'];
		$files = $pluginData['files']['file'];
		if(is_array($pluginData['compatibility']['version'])) {
			$compatibility = implode(",",$pluginData['compatibility']['version']);
		}
		if(is_array($pluginData['files']['file'])) {
			$files = implode(",",$pluginData['files']['file']);
		}
		$data = [
			$pluginData['name'],
			$pluginData['author'],
			$pluginData['version'],
			$compatibility,
			$pluginData['folder'],
			$files,
			1,
			time(),
			$_SESSION['username']
		];
		$query = "INSERT INTO ".X_TEAM_PLUGINS." ([name],[author],[version],[compatibility],[folder],[files],[status],[install_date],[installed_by]) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $result = $this->db->query($query, $data);
		if($result) {
		    #@忽略生成出来的错误信息
			@$this->_getPluginLatestVersion($pluginData['folder'], $pluginData['version']);
			return true;
		} else {
			return false;
		}
	}


    /**
     * 如果有sql文件,执行SQL语句
     * @param $path
     * @param $SqlFile
     * @return bool
     */
	private function installSqlQuery($path,$SqlFile){
        $file = $this->pluginPath($path).$SqlFile;
	    if(file_exists($file)){
            $sql = file_get_contents($file);
            $Query = $this->db->query($sql);
            if(!$Query) return false;
        }
        return true;
    }

    /**
     * 输出插件列表
     * @return array|bool|null
     */
	public function getInstalledPlugins() {
        return $this->db->query_fetch("SELECT * FROM ".X_TEAM_PLUGINS." ORDER BY id ASC");
	}

    /**
     * 更新查询状态
     * @param $plugin_id
     * @param $new_status
     */
	public function updatePluginStatus($plugin_id,$new_status) {
		$this->db->query("UPDATE ".X_TEAM_PLUGINS." SET status = ? WHERE id = ?", array($new_status, $plugin_id));
		$update_cache = $this->rebuildPluginsCache();
		if(!$update_cache) {
			message('error','无法更新插件缓存数据，请确保文件存在并且可写！');
		}
	}

    /**
     * 卸载插件
     * @param $plugin_id
     * @return bool
     */
	public function uninstallPlugin($plugin_id) {
		$uninstall = $this->db->query("DELETE FROM ".X_TEAM_PLUGINS." WHERE id = ?", array($plugin_id));
		if($uninstall) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * 重建插件缓存
     * @return bool|void
     */
	public function rebuildPluginsCache() {
		$plugins = $this->db->query_fetch("SELECT * FROM ".X_TEAM_PLUGINS." WHERE status = 1 ORDER BY id ASC");
		if(!is_array($plugins)) {
			$update = updateCacheFile('plugins.cache', "");
			if(!$update) return;
			return true;
		}
		
		foreach($plugins as $key => $row) {
			$compatibility = explode(',', $row['compatibility']);
			if(!is_array($compatibility)) continue;
			if(!in_array(__X_TEAM_VERSION__, $compatibility)) continue;
			
			$files = explode(',', $row['files']);
			if(!is_array($files)) continue;
			
			$plugins[$key]['compatibility'] = $compatibility;
			$plugins[$key]['files'] = $files;
		}
		
		$cacheData = encodeCache($plugins);
		$update = updateCacheFile('plugins.cache', $cacheData);
		if(!$update) return;
		return true;
	}

    /**
     * 获取插件最后版本号验证
     * @param $plugin
     * @param string $version
     * @return mixed|void
     */
	public function _getPluginLatestVersion($plugin, $version='2.0.0') {
		if(!check_value($plugin)) return;
		if(!check_value($version)) return;
		
		$url = 'http://version.niudg.com/version/plugin.php';
		
		$localResult = [
			'version' => urlencode($version),
			'baseurl' => urlencode(__BASE_URL__),
			'plugin' => urlencode($plugin),
		];

		foreach($localResult as $key => $value) {
			$fieldsArray[] = $key . '=' . $value;
		}
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($localResult));
		curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $fieldsArray));
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'XteamFramework');
		curl_setopt($ch, CURLOPT_HEADER, false);

		$result = curl_exec($ch);
		curl_close($ch);
		
		if(!$result) return;
		$resultArray = json_decode($result, true);
		if(!is_array($resultArray)) return;
		return $resultArray;
	}
}