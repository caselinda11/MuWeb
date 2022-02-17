<?php
/**
 * 排名类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Rankings {
    private $_results;
    private $_excludedCharacters = array('');
    private $_rankingsMenu;
    private $serverFiles;
    /**
     * 构造函数
     * Rankings constructor.
     * @throws Exception
     */
    function __construct() {

        #服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);

        // 排名配置
        loadModuleConfigs('rankings');
        $this->_results = (check_value(mconfig('results')) ? mconfig('results') : 25);

        // 排除角色
        if(check_value(mconfig('excluded_characters'))) {
            $excludedCharacters = explode(",", (string)mconfig('excluded_characters'));
            $this->_excludedCharacters = $excludedCharacters;
        }

        # 默认排名导航模块
        $this->_rankingsMenu = [
            // 语言短语, 模块名, 状态, 服务器类型[数组]
            ['角色排名', 'level', mconfig('enable_level')],
            ['战盟排名', 'guilds', mconfig('enable_guilds')],
            ['家族排名', 'gens', mconfig('enable_gens')],
            ['推广排名', 'votes', mconfig('enable_votes')],
        ];

        # 扩展菜单链接
        $extraMenuLinks = getRankingMenuLinks();
        if(is_array($extraMenuLinks)) {
            foreach($extraMenuLinks as $menuLink) {
                $this->_rankingsMenu[] = [$menuLink[0], $menuLink[1], $menuLink[2],$menuLink[3]];
            }
        }
    }

    /**
     * 更新排名缓存
     * @param $type
     * @throws Exception
     */
    public function UpdateRankingCache($type) {
        switch($type) {
            case 'level':
                $this->_levelsRanking();
                break;
            case 'guilds':
                $this->_guildsRanking();
                break;
            case 'votes':
                $this->_votesRanking();
                break;
            case 'gens':
                $this->_gensRanking();
                break;
            default:
                return;
        }
    }

    /**
     * 等级排名缓存文件生成
     * @throws Exception
     */
    public function _levelsRanking() {
        $result = $this->_getLevelRankingData();
        if(!is_array($result)) return;
        $cache = BuildCacheData($result);
        updateCacheFile('rankings_level.cache', $cache);
    }

    /**
     * 战盟排名缓存文件生成
     * @throws Exception
     */
    public function _guildsRanking() {
        #取分区数组
        global $serverGrouping;
        foreach ($serverGrouping AS $code=>$item){
            $Db = ($item['SQL_USE_2_DB']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
            $query = "SELECT TOP ".$this->_results." "._TBL_GUILD_."."._CLMN_GUILD_NAME_.", "._TBL_GUILD_."."._CLMN_GUILD_MASTER_.", "._TBL_GUILD_."."._CLMN_GUILD_SCORE_.",CONVERT(varchar(max), "._CLMN_GUILD_LOGO_.", 2) as "._CLMN_GUILD_LOGO_." FROM "._TBL_GUILD_." LEFT JOIN Character on Guild.G_Master = Character.Name COLLATE Chinese_PRC_CI_AS LEFT JOIN [".$Db."].[dbo].[MEMB_INFO] on MEMB_INFO.memb___id = Character.AccountID COLLATE Chinese_PRC_CI_AS where MEMB_INFO.servercode = ".$item['SERVER_GROUP']." ORDER BY "._CLMN_GUILD_SCORE_." DESC";

            $result[$code] = Connection::Database('MuOnline',$code)->query_fetch($query);
            if(empty($result[$code])) continue;
            foreach ($result[$code] as $data){
                # 成员统计数据
                $guildMember = Connection::Database('MuOnline',$code)->query_fetch_single("select count(*) AS Member from ["._TBL_GUILDMEMB_."] where ["._CLMN_GUILDMEMB_NAME_."] = ?", [$data['G_Name']]);
                $data[_CLMN_GUILD_NAME_] = strtr($data[_CLMN_GUILD_NAME_],',#\/:*?"\'','??\/:*?"\'');
                $data[_CLMN_GUILD_MASTER_] = strtr($data[_CLMN_GUILD_MASTER_],',#\/:*?"\'','??\/:*?"\'');
                #组装数组
                if(!$data['G_Score']) $data['G_Score'] = 0;
                $newData[] = array_replace_recursive((array)$item['SERVER_GROUP'],$data,$guildMember);
            }
        }
        if(empty($newData)) return;
        //不区分大区,进行重新排序.
        $newData = arraySortByKey($newData,'Member',false);
        $cache = BuildCacheData($newData);
        updateCacheFile('rankings_guilds.cache',$cache);
    }

    /**
     * 家族排名缓存文件生成
     * @throws Exception
     */
    public function _gensRanking()
    {
        #取分区数组
        global $serverGrouping;
        $character = new Character();
        foreach ($serverGrouping AS $code=>$item){
            $Db = ($item['SQL_USE_2_DB']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
            switch ($this->serverFiles){
                case "mudevs":
                    // 等级，家族，大师不同的表
                    $query = "SELECT TOP ".$this->_results." 
                            CharacterSystem.AccountID,
                            CharacterSystem.Name,
                            CharacterSystem.cLevel,
                            CharacterSystem.Class,
                            CharacterSystem.MapNumber,
                            MasterSystem."._CLMN_ML_LVL_." as mLevel,
                            GensSystem."._CLMN_GENS_TYPE_." as GenFamily,
                            GensRewardSystem."._CLMN_GENS_LEVEL_." AS GensClass,
                            GensSystem."._CLMN_GENS_POINT_." as GensContribution
                            FROM [Character] AS CharacterSystem
                            LEFT JOIN ["._TBL_GENS_."] AS GensSystem 
                            ON CharacterSystem.Name = GensSystem."._CLMN_GENS_NAME_."
                            COLLATE Chinese_PRC_CI_AS
                            LEFT JOIN ["._TBL_MASTERLVL_."] AS MasterSystem 
                            ON CharacterSystem.Name = MasterSystem."._CLMN_ML_NAME_."
                            COLLATE Chinese_PRC_CI_AS
							LEFT JOIN [".$Db."].[dbo].["._TBL_MI_."] AS AccountSystem
							ON CharacterSystem.AccountID = AccountSystem.memb___id
                            COLLATE Chinese_PRC_CI_AS
                            LEFT JOIN Gens_Reward AS GensRewardSystem 
                            ON CharacterSystem.Name = GensRewardSystem.Name
                            collate Chinese_PRC_CI_AS
                            WHERE CharacterSystem.Name NOT IN(".$this->_rankingsExcludeChars().")
							AND AccountSystem."._CLMN_GROUP_." = ".$item['SERVER_GROUP']."
							AND CharacterSystem.cLevel > 0
							AND GensSystem."._CLMN_GENS_TYPE_." > 0
                            ORDER BY GensSystem."._CLMN_GENS_POINT_." DESC,GensRewardSystem."._CLMN_GENS_LEVEL_." DESC";
                    break;
                case "louis":
                case "xteam":
                case "muemu":
                case "egames":
                case "haoyi":
                case "igcn":
                    // 等级+家族在同一表，大师等级不同的表
                    $query = "SELECT TOP ".$this->_results." 
                            CharacterSystem.AccountID,
                            CharacterSystem.Name,
                            CharacterSystem.cLevel,
                            CharacterSystem.Class,
                            CharacterSystem.MapNumber,
                            MasterSystem."._CLMN_ML_LVL_." as mLevel,
                            GensSystem."._CLMN_GENS_TYPE_." as GenFamily,
                            GensSystem."._CLMN_GENS_LEVEL_." as GensClass,
                            GensSystem."._CLMN_GENS_POINT_." as GensContribution
                            FROM Character AS CharacterSystem
                            LEFT JOIN ["._TBL_GENS_."] AS GensSystem 
                            ON CharacterSystem.Name = GensSystem."._CLMN_GENS_NAME_."
                            COLLATE Chinese_PRC_CI_AS
                            LEFT JOIN "._TBL_MASTERLVL_." AS MasterSystem 
                            ON CharacterSystem.Name = MasterSystem."._CLMN_ML_NAME_."
                            COLLATE Chinese_PRC_CI_AS
							LEFT JOIN [".$Db."].[dbo].[MEMB_INFO] AS AccountSystem
							ON CharacterSystem.AccountID=AccountSystem.memb___id
                            COLLATE Chinese_PRC_CI_AS
                            WHERE CharacterSystem.Name NOT IN(".$this->_rankingsExcludeChars().")
							AND AccountSystem."._CLMN_GROUP_." = ".$item['SERVER_GROUP']."
							AND CharacterSystem.cLevel > 0
							AND CharacterSystem."._CLMN_GENS_POINT_." > 0
							AND CharacterSystem."._CLMN_GENS_LEVEL_." > 0
                            ORDER BY CharacterSystem."._CLMN_GENS_POINT_." DESC,CharacterSystem."._CLMN_GENS_LEVEL_." DESC";
                    break;
                default;
                    return;
            }

            $result[$code] = Connection::Database('MuOnline',$code)->query_fetch($query);
            if(empty($result[$code])) continue;
            foreach ($result[$code] as $data){
                $OnlineData = $character->getOnlineDataForUsername($code,$data['AccountID']);
                if(!is_array($OnlineData)) continue;
                $data['Name'] = strtr($data['Name'],',#\/:*?"\'','??\/:*?"\'');
                #组装数组
                $newData[] = array_replace_recursive((array)$item['SERVER_GROUP'],$data,$OnlineData);
            }
        }
        if(empty($newData)) return;
        //不区分大区,进行重新排序.
//        $newData = arraySortByKey($newData,'GensContribution',false);
        $cache = BuildCacheData($newData);
        updateCacheFile('rankings_gens.cache', $cache);
    }

    /**
     * 推广排名缓存文件生成
     * @throws Exception
     */
    public function _votesRanking() {

        $voteMonth = date("m/01/Y 00:00");
        $voteMonthTimestamp = strtotime($voteMonth);
        $accounts = Connection::Database('Web')->query_fetch("SELECT TOP ".$this->_results." user_id,servercode,COUNT(*) as count FROM [".X_TEAM_VOTE_LOGS."] WHERE [timestamp] >= ? GROUP BY user_id,servercode ORDER BY count DESC", [$voteMonthTimestamp]);

        if(!is_array($accounts)) return;
        $common = new common();
        $Character = new Character();
        foreach($accounts as $data) {
            $servercode = getGroupIDForServerCode($data['servercode']);
            #用GID找到账号信息
            $accountInfo = $common->getUserInfoForUserGID($servercode, $data['user_id']);
            if (!is_array($accountInfo)) continue;

            $characterName = $Character->getAccountCharacterIDC($servercode,$accountInfo[_CLMN_USERNM_]);
            if (!check_value($characterName)) continue;

            $characterData = $Character->getCharacterDataForCharacterName($servercode,$characterName);
            if (!is_array($characterData)) continue;

            if (in_array($characterName, $this->_excludedCharacters)) continue;
            # $OnlineData[5]
            $OnlineData = $Character->getOnlineDataForUsername($servercode,$characterData['AccountID']);
            if(!is_array($OnlineData)) continue;
            $characterName = strtr($characterName,',#\/:*?"\'','??\/:*?"\'');
            $result = [
                0   =>  $accountInfo['servercode'],         #分区号
                1   =>  $characterName,                     #最后在线角色名
                2   =>  $characterData[_CLMN_CHR_CLASS_],   #角色类型
                3   =>  $characterData[_CLMN_CHR_MAP_],     #角色所在地图
                4   =>  $data['count'],                     #推广次数
            ];
            if(!is_array($result)) continue;
            $rankingData[] = array_replace_recursive($result,$OnlineData);
        }
        if(empty($rankingData)) return;
        //不区分大区,进行重新排序.
        $rankingData = arraySortByKey($rankingData,4,false);
        $cache = BuildCacheData($rankingData);
        updateCacheFile('rankings_votes.cache',$cache);
    }

    /**
     * 排名导航栏
     */
    public function rankingsMenu() {
        echo '<div class="card mb-3">';
        echo '<div class="btn-group" role="group">';
        foreach($this->_rankingsMenu as $rm_item) {
            if(is_array($rm_item[3])) {
                if(!in_array($this->serverFiles, $rm_item[3])) continue;
            }
            if($rm_item[2]) {
                if($_REQUEST['subpage'] == $rm_item[1]) {
                    echo '<a href="'.__PATH_MODULES_RANKINGS__.$rm_item[1].'" class="btn btn-outline-dark active">'.$rm_item[0].'</a>';
                } else {
                    echo '<a href="'.__PATH_MODULES_RANKINGS__.$rm_item[1].'" class="btn btn-outline-dark">'.$rm_item[0].'</a>';
                }
            }
        }
        echo '</div>';
        echo '</div>';
    }

    /**
     * 排除指定角色
     * @return string|void
     */
    private function _rankingsExcludeChars() {
        if(!is_array($this->_excludedCharacters)) return;
        $return = array();
        foreach($this->_excludedCharacters as $characterName) {
            $return[] = "'".$characterName."'";
        }
        return implode(",", $return);
    }

    /**
     * 获取等级排名数据
     * @return array|bool|void|null
     * @throws Exception
     */
    public function _getLevelRankingData() {
        #取分区数组
        global $serverGrouping;
        $character = new Character();
        foreach ($serverGrouping AS $code=>$item){
            $Db = ($item['SQL_USE_2_DB']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
            switch ($this->serverFiles){
                case "igcn":
                case "louis":
                case "xteam":
                case "mudevs":
                case "muemu":
                case "egames":
                case "haoyi":
                    // 等级，大师等级不同的表
                    $query = "SELECT TOP ".$this->_results." 
                            CharacterSystem.AccountID,
                            CharacterSystem.Name,
                            CharacterSystem.cLevel,
                            CharacterSystem.LevelUpPoint,
                            CharacterSystem.Class,
                            CharacterSystem.Strength,
                            CharacterSystem.Dexterity,
                            CharacterSystem.Vitality,
                            CharacterSystem.Energy,
                            CharacterSystem.Money,
                            CharacterSystem.MapNumber,
                            CharacterSystem.PkLevel,
                            CharacterSystem.PkCount,
                            CharacterSystem.CtlCode,
                            CharacterSystem.Leadership,
                            MasterSystem."._CLMN_ML_LVL_." as mLevel
                            FROM Character AS CharacterSystem
                            LEFT JOIN "._TBL_MASTERLVL_." AS MasterSystem 
                            ON CharacterSystem.Name = MasterSystem."._CLMN_ML_NAME_."
                            COLLATE Chinese_PRC_CI_AS
							LEFT JOIN [".$Db."].[dbo].[MEMB_INFO] AS AccountSystem
							ON CharacterSystem.AccountID=AccountSystem.memb___id
                            COLLATE Chinese_PRC_CI_AS
                            WHERE CharacterSystem.Name NOT IN(".$this->_rankingsExcludeChars().")
							AND AccountSystem."._CLMN_GROUP_." = ".$item['SERVER_GROUP']."
							AND CharacterSystem.cLevel > 0
                            ORDER BY CharacterSystem."._CLMN_CHR_LVL_." DESC,MasterSystem."._CLMN_ML_LVL_." DESC";
                    break;
                default;
                    return;
            }

            $result[$code] = Connection::Database("MuOnline",$code)->query_fetch($query);
            if(empty($result[$code])) continue;
            foreach ($result[$code] as $data){
                $OnlineData = $character->getOnlineDataForUsername($code,$data['AccountID']);
                if(!is_array($OnlineData)) continue;
                $data['Name'] = strtr($data['Name'],',#\/:*?"\'','??\/:*?"\'');
                #组装数组
//                $newData[] = array_replace_recursive((array)$item['SERVER_GROUP'],$data,$OnlineData);
                $newData[] = [
                    $item['SERVER_GROUP'],
                    $data['AccountID'],
                    $data['Name'],
                    (int)$data['cLevel'],
                    $data['LevelUpPoint'],
                    $data['Class'],
                    $data['Strength'],
                    $data['Dexterity'],
                    $data['Vitality'],
                    $data['Energy'],
                    $data['Money'],
                    $data['MapNumber'],
                    $data['PkLevel'],
                    $data['PkCount'],
                    $data['CtlCode'],
                    $data['Leadership'],
                    (int)$data['mLevel'],
                    $OnlineData['ConnectStat'],
                ];
            }
        }
        if(empty($newData)) return;
        return $newData;
    }

    /**
     * 第一名数据
     * @param $group
     * @param $class
     * @param $name
     * @param $level
     * @param $status
     * @param $form
     * @throws Exception
     */
    public function getRankingTopNo1($group,$class,$name,$level,$status,$form){
        global $custom;
        $imageFileName = array_key_exists($class, $custom['character_class']) ? $custom['character_class'][$class][2] : 'avatar.jpg';
        $imageFullPath = __PATH_PUBLIC__.'/img/' . config('character_avatars_dir') . '/' . $imageFileName;
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<div class="media">';
        echo '<div style="background:url('.__PATH_PUBLIC__.'img/Top/no1.png) no-repeat; width: 160px;height: 194px" class="align-self-center mr-3">';
        echo '<div style="padding-top: 49px;padding-left:47px">';
        echo '<img src="'.$imageFullPath.'" data-toggle="tooltip" width="60" class="rounded-circle" title="'.$name.'" alt="'.$name.'"/>';
        echo '</div>';
        echo '</div>';
        echo '<div class="media-body">';
        echo '<table id="search" class="table text-center">';
        echo '<tbody>';
        echo '<tr>';
        echo '<td>玩家</td>';
        echo '<td>'.$name.$status.'</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>等级</td>';
        echo '<td>'.$level.'</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>所属大区</td>';
        echo '<td>'.$group.'</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td>来自</td>';
        echo '<td>'.$form.'</td>';
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }


}