<?php
/**
 * 页面加载类函数头
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Handler {

	private $_disableSystemFooterVersion = false; //是否禁用版本信息
	private $_disableSystemFooterCredits = false; //是否禁用版权信息

    /**
     * 加载页面
     * @throws Exception
     */
	public function loadPage() {
		global $config,$lang,$custom,$tSettings;

		# 对象实例
		$handler = $this;
		# 访问
		if(!defined('access')) throw new Exception('对不起，您没有足够权限访问该页面!');
        switch (access) {
            case 'index':
                # 检查模板是否存在
                if (!$this->templateExists($config['website_template'])) throw new Exception('所选模版美化无法加载[' . $config['website_template'] . ']!');

                # 加载模板主页
                include(__PATH_TEMPLATES__ . $config['website_template'] . '/index.php');

                # 显示管理员面板按钮
                if (isLoggedIn() && canAccessAdminCP($_SESSION['username'])) {
                    echo '<a href="' . __PATH_ADMINCP_HOME__ . '" class="btn btn-danger" style="position:absolute;top:10px;right:10px;z-index:999">进入后台</a>';
                }

                break;
            case 'admincp':
            case 'api':
            case 'cron':
            case 'install':
                break;
            default:
                throw new Exception('对不起，您没有足够权限访问该页面!');
        }
	}

    /**
     * 主页对象
     * @param string $page
     * @param string $subpage
     */
	public function loadModule($page = 'home',$subpage = 'home') {
		global $config,$lang,$custom,$mconfig,$tSettings;
		try {
			$handler = $this;
			$page = $this->cleanRequest($page);
			$subpage = $this->cleanRequest($subpage);

			$request = explode("/", $_GET['request']);
			if(is_array($request)) {
				for($i = 0; $i < count($request); $i++) {
					if(check_value($request[$i])) {
						if(check_value($request[$i+1])) {
							$_GET[$request[$i]] = filter_var($request[$i+1], FILTER_SANITIZE_STRING);
						} else {
							$_GET[$request[$i]] = NULL;
						}
					}
					$i++;
				}
			}

			if(!check_value($page)) { $page = 'home'; }

			if(!check_value($subpage)) {
				if($this->moduleExists($page)) {
					@loadModuleConfigs($page);
                    include(__PATH_MODULES__.$page.'.php');
				} else {
					$this->module404();
				}
			} else {
				// 新闻路径
				switch($page) {
					case 'news':
						if($this->moduleExists($page)) {
							@loadModuleConfigs($page);
                            include(__PATH_MODULES__.$page.'.php');
						} else {
							$this->module404();
						}
					break;
					default:
						$path = $page.'/'.$subpage;
						if($this->moduleExists($path)) {
							$cnf = $page.'.'.$subpage;
							@loadModuleConfigs($cnf);
                            include(__PATH_MODULES__.$path.'.php');
						} else {
							$this->module404();
						}
					break;
				}
			}
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}

    /**
     * 验证modules目录下$page.php模块页面是否存在
     * @param $page
     * @return bool
     */
	private function moduleExists($page) {
		if(file_exists(__PATH_MODULES__ . $page . '.php')) return true;
		return false;
	}

    /**
     * 验证modules/usercp目录下$page.php模块页面是否存在
     * @param $page
     * @return bool
     */
	private function userCpModuleExists($page) {
		if(file_exists(__PATH_MODULES_USERCP__ . $page . '.php')) return true;
		return false;
	}

    /**
     * 验证templates目录下$page index.php模块页面是否存在
     * @param $template
     * @return bool
     */
	private function templateExists($template) {
		if(file_exists(__PATH_TEMPLATES__ . $template . '/index.php')) return true;
		return false;
	}


    /**
     * 验证admincp/modules/目录下$page.php是否存在
     * @param $page
     * @return bool
     */
	private function adminCpModuleExists($page) {
		if(file_exists(__PATH_ADMINCP_MODULES__ . $page . '.php')) return true;
		return false;
	}

    /**
     * 版权信息
     * @return string
     */
	public function powered() {
        $temp = '';
		if($this->_disableSystemFooterCredits) return $temp;
        $temp.='<a href="http://www.baidu.com" target="_blank" class="powered"> [WebEngine CMS] </a>';
        if(!$this->_disableSystemFooterVersion) $temp.= ' 版本[' . __X_TEAM_VERSION__.']';
        return $temp;
	}

    /**
     * 加载管理员界面模块
     * @param string $module
     * @throws Exception
     */
	public function loadAdminCPModule($module='home') {

		$module = (check_value($module) ? $module : 'home');

		if($this->adminCpModuleExists($module)) {

			// 管理员访问权限级别
			$adminAccessLevel = config('admins');
			$accessLevel = $adminAccessLevel[$_SESSION['username']];

			// 模块访问级别
			$modulesAccessLevel = config('admincp_modules_access');
			if(is_array($modulesAccessLevel)) {
				if(array_key_exists($module, $modulesAccessLevel)) {
					if($accessLevel >= $modulesAccessLevel[$module]) {
                        include(__PATH_ADMINCP_MODULES__.$module.'.php');
					} else {
						message('error','您无权访问此模块！');
					}
				} else {
                    include(__PATH_ADMINCP_MODULES__.$module.'.php');
				}
			}
		} else {
			message('error','无效模块');
		}
	}


    /**
     * 过滤请求字符串
     * @param $string
     * @return string|string[]|null
     */
	private function cleanRequest($string) {
		return preg_replace("/[^a-zA-Z0-9\s\/]/", "", $string);
	}

    /**
     * 跳转主页
     */
	private function module404() {
		redirect();
	}

}