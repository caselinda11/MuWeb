<?php
/**
 * 个人资料与战盟个人资料类函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class weProfiles {

	private $_request;
	private $_type;
	
	private $_reqMaxLen;
	private $_guildsCachePath;
	private $_playersCachePath;
	private $_cacheUpdateTime;
	private $_fileData;
    private $common,$group,$dB,$Me_dB;
    /**
     * 构造函数
     * @throws Exception
     */
	function __construct() {
		$this->common = new common();
		# 设置
		$this->_guildsCachePath = __PATH_INCLUDES_CACHE__ . 'profiles/guilds/';
		$this->_playersCachePath = __PATH_INCLUDES_CACHE__ . 'profiles/players/';
        loadModuleConfigs('profiles');
		$this->_cacheUpdateTime = mconfig('update_time');
		# 检查缓存目录
		$this->checkCacheDir($this->_guildsCachePath);
		$this->checkCacheDir($this->_playersCachePath);
		
	}

    /**
     * 发送请求
     * @param $code   //分区号
     * @param $type    //类型  角色|战盟
     * @param $input   //角色名
     * @throws Exception
     */
	public function setRequest($code,$type,$input) {
	    if (!check_value($code)) throw new Exception('[1]请求错误,分区识别失败!');
        if (!check_value($type)) throw new Exception('[2]请求错误,类型识别失败!');
        if (!check_value($input)) throw new Exception('[3]请求错误,昵称识别失败!');
        switch($type) {
            case "guild":
                $this->_type = "guild";
                $this->_reqMaxLen = 12; #战盟名称链接长度
                break;
            case "player":
            default:
                $this->_type = "player";
                $this->_reqMaxLen = 15; #玩家名称链接长度限制
                break;
        }
	    if(!Validator::Number($code)) throw new Exception('分区错误!');
	    $group = getGroupIDForServerCode($code);
//		if(!Validator::ChineseCharacter($input)) throw new Exception('请求的数据类型错误!');
		if(strlen($input) < 4 || strlen($input) > $this->_reqMaxLen) throw new Exception('[4]请求数据错误!');
        $this->dB = Connection::Database('MuOnline',$group);
        $this->Me_dB = Connection::Database('Me_MuOnline',$group);
        $this->group = $group;
        $this->_request = $input;
	}

    /**
     * 检查目录是否存在
     * @param $path
     * @throws Exception
     */
	private function checkCacheDir($path) {
		if(check_value($path)) {
			if(!file_exists($path) || !is_dir($path)) {
				if(config('error_reporting')) {
					throw new Exception("无效的缓存目录 ($path)");
				} else {
					throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
				}
			} else {
				if(!is_writable($path)) {
					if(config('error_reporting')) {
						throw new Exception("缓存目录不可写 ($path)");
					} else {
						throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
					}
				}
			}
		}
	}

    /**
     * 缓存数据过程
     * @throws Exception
     */
    private function checkCache() {
		switch($this->_type) {
			case "guild":
                #战盟资料
				$reqFile = $this->_guildsCachePath .'['.$this->group.']'. urlencode($this->_request) . '.cache';
                #如果缓存文件不存在
				if(!file_exists($reqFile)) {
					$this->cacheGuildData();
				}
                #如果缓存文件存在
				$fileData = file_get_contents($reqFile);
				$fileData = explode(",", $fileData);
                #如果文件内容是数组
				if(is_array($fileData)) {
					if(time() > ((int)$fileData[0]+(int)$this->_cacheUpdateTime)) {
						$this->cacheGuildData();
					}
				} else {
					throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
				}
                #如果缓存数据没有超过时间
				$this->_fileData = file_get_contents($reqFile);
				break;
            case "player":
			default:
                #角色资料
                #D:/phpstudy/WWW/Xteam/includes/cache/profiles/players/宇哥法师.cache
				$reqFile = $this->_playersCachePath .'['.$this->group.']'. urlencode($this->_request) . '.cache';
				#如果缓存文件不存在
				if(!file_exists($reqFile)) {
					$this->cachePlayerData();
				}
                #如果缓存文件存在
				$fileData = file_get_contents($reqFile);
				$fileData = explode(",", $fileData);

                #如果文件内容是数组
				if(is_array($fileData)) {
					if((time() - $fileData[0]) > $this->_cacheUpdateTime) {
						$this->cachePlayerData();
					}
				} else {
					throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
				}
                #如果缓存数据没有超过时间
				$this->_fileData = file_get_contents($reqFile);
		}
	}

    /**
     * 缓存战盟数据
     * @throws Exception
     */
	public function cacheGuildData() {
		# 基础数据
		$guildData = $this->dB->query_fetch_single("SELECT *, CONVERT(varchar(max), "._CLMN_GUILD_LOGO_.", 2) as "._CLMN_GUILD_LOGO_." FROM "._TBL_GUILD_." WHERE "._CLMN_GUILD_NAME_." = ?", [$this->_request]);
		if(!$guildData) throw new Exception('您的请求无法完成，请再试一次。');
		
		# 成员数据
		$guildMembers = $this->dB->query_fetch("SELECT * FROM "._TBL_GUILDMEMB_." WHERE "._CLMN_GUILDMEMB_NAME_." = ?", [$this->_request]);
		if(!$guildMembers) throw new Exception('您的请求无法完成，请再试一次。');
		$members = [];
		foreach($guildMembers as $gMember) {
			$members[] = $gMember[_CLMN_GUILDMEMB_CHAR_];
		}
		$gMembers_str[] = implode(",", $members);
		
		// 缓存
		$data = [
			time(),
			$guildData[_CLMN_GUILD_NAME_],
			$guildData[_CLMN_GUILD_LOGO_],
			$guildData[_CLMN_GUILD_SCORE_],
			$guildData[_CLMN_GUILD_MASTER_],
            $gMembers_str,
		];

        return $data;
		// 缓存可读数据
		$cacheData = implode(",", $data);
		
		// 更新缓存文件
		$reqFile = $this->_guildsCachePath .'['.$this->group.']'. urlencode($this->_request) . '.cache';
		$fp = fopen($reqFile, 'w+');
		fwrite($fp, $cacheData);
		fclose($fp);
	}

    /**
     * 缓存玩家数据
     * @throws Exception
     */
    private function cachePlayerData() {

		$Character = new Character();
		# 玩家基础数据
		$playerData = $Character->getCharacterDataForCharacterName($this->group,$this->_request);
		if(!$playerData) throw new Exception('暂时无法获取该角色信息，请稍后再试。');
        #取在线信息
        $OnlineData = $Character->getOnlineDataForUsername($this->group,$playerData['AccountID']);
        #所属战盟
        $guildName = null;
        $guildName = $Character->getGuildNameForName($this->group,$this->_request);
        $guildLoad['G_Master'] = null;
        $guildMember = 0;
        if(!empty($guildName)){
            #取战盟盟主
            $guildLoad = $Character->getGuildDataForGuildName($this->group,$guildName);
            # 成员统计人数
            $guildMember =$Character->getGuildMemberForGuildName($this->group,$guildName);
        }

//		//------------------------------------------------
//		// 组装数据
		$data = [
			time(), #现在时间
		    $playerData['Name'],            #名称
	        $playerData['Class'],           #角色类
            $playerData['cLevel'],          #等级
            $playerData['mLevel'],          #大师
            $playerData['LevelUpPoint'],    #升级点
            $playerData['mPoint'],          #大师点
            $playerData['Strength'],        #力量
            $playerData['Dexterity'],       #敏捷
            $playerData['Vitality'],        #体力
            $playerData['Energy'],          #智力
            $playerData['Leadership'],      #统率
            $playerData['Inventory'],       #背包
            $playerData['MapNumber'],       #所在地图
            $playerData['PkLevel'],         #红名状态
            $playerData['PkCount'],         #杀人统计
            $playerData['CtlCode'],         #角色状态
            $playerData['GenFamily'],       #家族类型 0/1/2
            $playerData['GenLevel'],        #家族排名
            $playerData['GensContribution'],#家族贡献
            $OnlineData['ConnectStat'],     #在线状态 0/1
            $OnlineData['IP'],              #联机IP
            $OnlineData['ServerName'],      #所在服务器
            $OnlineData['ConnectTM'],       #上线时间
            $OnlineData['DisConnectTM'],    #下线时间
            $guildName,                     #所属战盟
            $guildLoad['G_Master'],         #所属战盟
            $guildMember,                   #战盟人数
		];
		// 缓存可读数据
		$cacheData = implode(",", $data);
		// 更新缓存文件
		$reqFile = $this->_playersCachePath .'['.$this->group.']'. urlencode($this->_request) . '.cache';
		$fp = fopen($reqFile, 'w+');
		fwrite($fp, $cacheData);
		fclose($fp);
	}

    /**
     * 数据请求过程
     * @return array
     * @throws Exception
     */
	public function data() {
		if(!check_value($this->_request)) throw new Exception('您的请求无法处理，请与我们的在线客服联系。');
		$this->checkCache();
//		debug($this->_fileData);
		return(explode(",", $this->_fileData));
	}
}