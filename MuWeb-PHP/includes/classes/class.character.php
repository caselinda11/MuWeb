<?php
/**
 * 角色类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Character
{
    private $config;
    private $serverFiles;
    private $common;
    private $web;
    /**
     * 构造函数
     * @throws Exception
     */
    function __construct()
    {
        // 网站配置
        $this->config = webConfigs();
        $this->serverFiles = strtolower($this->config['server_files']);
        $this->common = new common();
        $this->web = Connection::Database("Web");
    }

    /**
     * 角色加点
     * @param $group
     * @param $username
     * @param $character_name
     * @param $uid
     * @param int $str
     * @param int $agi
     * @param int $vit
     * @param int $ene
     * @param int $com
     * @throws Exception
     */
    function setCharacterAddStats($group, $username, $character_name , $uid, $str = 0, $agi = 0, $vit = 0, $ene = 0, $com = 0)
    {
        if (!Validator::Number($uid)) throw new Exception('[0]出现意外错误，请与我们的在线客服联系。');
        if(!Token::checkToken('addstats'.$uid)) throw new Exception('[1]出错了，请您重新输入！');
        if (!check_value($username)) throw new Exception('[2]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($character_name)) throw new Exception('[3]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::UsernameLength($username)) throw new Exception('[4]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::AlphaNumeric($username)) throw new Exception('[5]出现意外错误，请与我们的在线客服联系。');
        if (!$this->_checkCharacterExists($group, $character_name)) throw new Exception('[6]您没有权限向这个角色添加属性点。');
        if (!$this->_checkCharacterBelongsToAccount($group, $character_name, $username)) throw new Exception('[7]您没有权限向这个角色添加属性点。');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('[8]您的账号已在线，请断开连接。');

        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);

        if ($str < 1) $str = 0;
        if ($agi < 1) $agi = 0;
        if ($vit < 1) $vit = 0;
        if ($ene < 1) $ene = 0;
        if ($com < 1) $com = 0;

        #验证最低加点要求
        $total_add_points = $str + $agi + $vit + $ene + $com;
        if ($total_add_points < mconfig('addstats_mini_points')) throw new Exception('[9]加点失败，您至少使用['.mconfig('addstats_mini_points').']属性点以上才可使用在线加点功能。');
        if ($total_add_points > $characterData[_CLMN_CHR_LVLUP_POINT_]) throw new Exception('[10]您没有足够的升级属性点。');
        global $custom;
        if ($com >= 1) {
            if (!in_array($characterData[_CLMN_CHR_CLASS_], $custom['character_cmd'])) throw new Exception('[11]统率只能为圣导师使用。');
        }

        $max_stats = mconfig('addstats_max_stats');
        $sum_str = $str + $characterData[_CLMN_CHR_STAT_STR_];
        $sum_agi = $agi + $characterData[_CLMN_CHR_STAT_AGI_];
        $sum_vit = $vit + $characterData[_CLMN_CHR_STAT_VIT_];
        $sum_ene = $ene + $characterData[_CLMN_CHR_STAT_ENE_];
        $sum_com = $com + $characterData[_CLMN_CHR_STAT_CMD_];

        if ($sum_str > $max_stats) throw new Exception('[12]您的力量已超过限制的最大属性点，请重新输入。');
        if ($sum_agi > $max_stats) throw new Exception('[13]您的敏捷已超过限制的最大属性点，请重新输入。');
        if ($sum_vit > $max_stats) throw new Exception('[14]您的体力已超过限制的最大属性点，请重新输入。');
        if ($sum_ene > $max_stats) throw new Exception('[15]您的智力已超过限制的最大属性点，请重新输入。');
        if ($sum_com > $max_stats) throw new Exception('[16]您的统率已超过限制的最大属性点，请重新输入。');

        if (mconfig('addstats_price_zen')) {
            if ($characterData[_CLMN_CHR_ZEN_] < mconfig('addstats_price_zen')) throw new Exception('[17]您没有足够的金币。');
            $deductZen = $this->DeductZEN($group, $character_name, mconfig('addstats_price_zen'));
            if (!$deductZen) throw new Exception('[18]出现意外错误，请与我们的在线客服联系。');
        }

        $query = Connection::Database('Me_MuOnline', $group)->query("UPDATE " ._TBL_CHR_." SET 
			" . _CLMN_CHR_STAT_STR_ . " = " . _CLMN_CHR_STAT_STR_ . " + ?,
			" . _CLMN_CHR_STAT_AGI_ . " = " . _CLMN_CHR_STAT_AGI_ . " + ?,
			" . _CLMN_CHR_STAT_VIT_ . " = " . _CLMN_CHR_STAT_VIT_ . " + ?,
			" . _CLMN_CHR_STAT_ENE_ . " = " . _CLMN_CHR_STAT_ENE_ . " + ?,
			" . _CLMN_CHR_STAT_CMD_ . " = " . _CLMN_CHR_STAT_CMD_ . " + ?,
			" . _CLMN_CHR_LVLUP_POINT_ . " = " . _CLMN_CHR_LVLUP_POINT_ . " - ? 
			WHERE [Name] = ?", [$str, $agi, $vit, $ene, $com, $total_add_points, $character_name]);
        if (!$query) throw new Exception('[19]出现意外错误，请与我们的在线客服联系。');

        # 成功!
        alert('usercp/addstats','角色['.$character_name.']已加点成功！');
    }

    /**
     * 在线洗点
     * @param $group
     * @param $username
     * @param $character_name
     * @param $uid
     * @throws Exception
     */
    function setCharacterResetStats($group, $username, $character_name, $uid)
    {
        if (!check_value($uid)) throw new Exception('[0]出错了，请您重新输入！');
        if (!Validator::UnsignedNumber($uid)) throw new Exception('[1]出错了，请您重新输入！');
        if (!Token::checkToken('ResetStats'.$uid)) throw new Exception('[2]出错了，请您重新输入！');
        if (!check_value($group)) throw new Exception('[3]出错了，请您重新输入！');
        if (!check_value($username)) throw new Exception('[4]出错了，请您重新输入！');
        if (!check_value($character_name)) throw new Exception('[5]出错了，请您重新输入！');
        if (!Validator::UsernameLength($username)) throw new Exception('[6]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::AlphaNumeric($username)) throw new Exception('[7]出现意外错误，请与我们的在线客服联系。');
        if (!$this->_checkCharacterExists($group, $character_name)) throw new Exception('[8]您没有权限对该角色使用清洗属性点。');
        if (!$this->_checkCharacterBelongsToAccount($group, $character_name, $username)) throw new Exception('[9]您没有权限对该角色使用清洗属性点。');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('[10]您的账号已在线，请断开连接。');

        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);
        if (!is_array($characterData)) throw new Exception('[0]出错了，请您重新输入！');
        #验证角色类型是否存在
        global $custom;
        if(!array_key_exists($characterData['Class'],$custom['character_class'])) throw new Exception('[11]出现意外错误，请与我们的在线客服联系。');

        #等级要求
        if(($characterData['cLevel']+$characterData['mLevel']) < mconfig('resetstats_level')) throw new Exception('[12]使用该功能等级必须达到['.mconfig('resetstats_level').']级！');

        #验证洗点时间限制
        $time_query = "";
        if(mconfig('time_requirement')>0){
            #初始化字段
            Connection::Database('MuOnline', $group)->query_fetch_single("if not exists (select * from syscolumns where name = 'invite_ID' and id = object_id('Character')) alter table [Character] add [reset_stats_time] smalldatetime NULL",[$character_name]);
            #获取时间
            $reset_time = Connection::Database('MuOnline', $group)->query_fetch_single("SELECT [reset_stats_time] FROM [Character] WHERE [Name] = ?",[$character_name]);
            if (!is_array($reset_time)) throw new Exception('[12]出现意外错误，请与我们的在线客服联系。');
            if($reset_time['reset_stats_time']){
                $nextToDay = strtotime(date("Y-m-d H:i:s", strtotime($reset_time['reset_stats_time']."+".mconfig('time_requirement')." day")));
                $nextDay = date("Y-m-d H:i:s",$nextToDay);//下一次时间戳
                if(time() < $nextToDay) throw new Exception('[13]该角色的洗点时间限制尚未冷却，请在['.$nextDay.']后再来。');
            }
            $time_query = "[reset_stats_time] = '".date('Y-m-d H:i:s')."', ";
        }

        #检测身上是否有物品
        if(mconfig('check_equipment')) {
            if($characterData['Inventory']){
                if(!$this->checkCharacterEquipmentItems($characterData['Inventory']))
                    throw new Exception("[14]检测到该角色装备栏存在装备，请先取下所有装备再来。");
            }
        }

        #扣除金币费用
        if (mconfig('resetstats_price_zen')) {
            if ($characterData[_CLMN_CHR_ZEN_] < mconfig('resetstats_price_zen')) throw new Exception('[15]您没有足够的金币。');
            $deductZen = $this->DeductZEN($group, $character_name, mconfig('resetstats_price_zen'));
            if (!$deductZen) throw new Exception('[16]您没有足够的金币。');
        }

        # 数据
        $chr_str = $characterData[_CLMN_CHR_STAT_STR_];
        $chr_agi = $characterData[_CLMN_CHR_STAT_AGI_];
        $chr_vit = $characterData[_CLMN_CHR_STAT_VIT_];
        $chr_ene = $characterData[_CLMN_CHR_STAT_ENE_];
        $chr_cmd = $characterData[_CLMN_CHR_STAT_CMD_];

        #统计点数
        $levelUp_points = $chr_str + $chr_agi + $chr_vit + $chr_ene;

        #圣导统率
        $cmd_query = '';
        if(mconfig('resetstats_use_cmd') && $chr_cmd > 0){
            $levelUp_points = $levelUp_points + $chr_cmd;
            $cmd_query = "["._CLMN_CHR_STAT_CMD_. "] = " . $custom['character_class'][$characterData['Class']]['base_stats']['cmd'].", ";
        }

        $update_query = "UPDATE [" . _TBL_CHR_ . "] SET 
            [" . _CLMN_CHR_STAT_STR_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['str'].",
	        [" . _CLMN_CHR_STAT_AGI_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['agi'].",
		    [" . _CLMN_CHR_STAT_VIT_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['vit'].",
		    [" . _CLMN_CHR_STAT_ENE_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['ene'].",
		    ". $cmd_query . $time_query ."["._CLMN_CHR_LVLUP_POINT_ . "] = [" . _CLMN_CHR_LVLUP_POINT_ . "] + ".$levelUp_points."
			WHERE [Name] = '".$character_name."'";

        $update = Connection::Database('MuOnline', $group)->query($update_query);
        if (!$update) throw new Exception('[17]出现意外错误，请与我们的在线客服联系。');
        # 成功
        alert('usercp/resetstats','您角色的属性点已成功清洗!');
    }

    /**
     * 角色转身
     * @param $group
     * @param $username
     * @param $character_name
     * @param $id
     * @param $userid
     * @throws Exception
     */
    function setCharacterReset($group, $username, $character_name, $userid, $id)
    {
        if(!Token::checkToken('reset'.$id)) throw new Exception('[1]出错了，请您重新尝试。');
        if (!check_value($id)) throw new Exception('[2]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($username)) throw new Exception('[2]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($character_name)) throw new Exception('[3]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::Number($userid)) throw new Exception('[4]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::UsernameLength($username)) throw new Exception('[5]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::AlphaNumeric($username)) throw new Exception('[6]出现意外错误，请与我们的在线客服联系。');
        if (!$this->_checkCharacterExists($group, $character_name)) throw new Exception('[7]您没有权限使用角色转身。');
        if (!$this->_checkCharacterBelongsToAccount($group, $character_name, $username)) throw new Exception('[8]您没有权限使用角色转身。');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('[9]您的账号已在线，请断开连接。');

        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);
        if ($characterData[_CLMN_CHR_LVL_] < mconfig('resets_required_level')) throw new Exception('[10]您的角色不符合转身等级要求，请达到等级再来。');

        if (mconfig('resets_price_zen')) {
            if ($characterData[_CLMN_CHR_ZEN_] < mconfig('resets_price_zen')) throw new Exception('[11]您没有足够的金币。');
            $deductZen = $this->DeductZEN($group, $character_name, mconfig('resets_price_zen'));
            if (!$deductZen) throw new Exception('[12]您没有足够的金币。');
        }
        #检测多少转
        $ResetCount = Connection::Database("MuOnline",$group)->query_fetch_single("SELECT ["._CLMN_CHR_RSTS_."] FROM [Character] WHERE [Name] = ?",[$character_name]);
        if(!is_array($ResetCount) || !$ResetCount[_CLMN_CHR_RSTS_]) $ResetCount[_CLMN_CHR_RSTS_] = 0;
        if($ResetCount[_CLMN_CHR_RSTS_] >= mconfig('resets_max')) throw new Exception("该角色已经达到最大转身次数，无需再转身。");

        $update = Connection::Database('MuOnline', $group)->query("UPDATE ["._TBL_CHR_."] SET " . _CLMN_CHR_LVL_ . " = 1," . _CLMN_CHR_RSTS_ . " = " . _CLMN_CHR_RSTS_ . " + 1 WHERE " . _CLMN_CHR_NAME_ . " = ?", [$character_name]);
        if (!$update) throw new Exception('[13]出现意外错误，请与我们的在线客服联系。');

        #奖励属性点
        if(mconfig('resets_stats_point')){
            $awardPoint = (int)mconfig('resets_point') + ((int)mconfig('resets_stats_point')*($ResetCount[_CLMN_CHR_RSTS_]+1));
            global $custom;
            $query = "UPDATE ["._TBL_CHR_."] SET
            [" . _CLMN_CHR_STAT_STR_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['str'].",
	        [" . _CLMN_CHR_STAT_AGI_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['agi'].",
		    [" . _CLMN_CHR_STAT_VIT_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['vit'].",
		    [" . _CLMN_CHR_STAT_ENE_ . "] = ".$custom['character_class'][$characterData['Class']]['base_stats']['ene'].",
		    ["._CLMN_CHR_LVLUP_POINT_. "] = "._CLMN_CHR_LVLUP_POINT_." + ?
              WHERE [" . _CLMN_CHR_NAME_ . "] = ?";
            $update1 = Connection::Database('MuOnline', $group)->query($query, [$awardPoint,$character_name]);
            if (!$update1) throw new Exception('[14]出现意外错误，请与我们的在线客服联系。');
            message('success', '您已获得['.$awardPoint.']点属性点奖励。');
        }
        #奖励货币
        if (mconfig('resets_credits_reward')) {
            try {
                $creditSystem = new CreditSystem();
                $creditSystem->setConfigId((int)mconfig('credit_type'));
                $configSettings = $creditSystem->showConfigs(true);
                switch ($configSettings['config_user_col_id']) {
                    case 'userid':
                        $creditSystem->setIdentifier($_SESSION['userid']);
                        break;
                    case 'username':
                        $creditSystem->setIdentifier($_SESSION['username']);
                        break;
                    case 'character':
                        $creditSystem->setIdentifier($character_name);
                        break;
                    default:
                        throw new Exception("[货币系统]无效的标识符。");
                }
                $creditSystem->addCredits($group,(int)mconfig('resets_credits_reward'));

                message('success', '您已获得['.mconfig('resets_credits_reward').']'.getPriceType(mconfig('credit_type')).'奖励。');
            } catch (Exception $ex) {
                message('error',$ex->getMessage());
            }
        }
        # 成功
        message('success', '您的角色已成功转身!');
    }


    /**
     * 领取转身奖励
     * @throws Exception
     */
    function receiveResetAward(){
        if (!Token::checkToken('reset'.$_REQUEST["id"])) throw new Exception('[1]出错了，请您重新尝试。');
        if (!check_value($_REQUEST['award_id'])) throw new Exception('[2]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($_REQUEST['character'])) throw new Exception('[3]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($_SESSION['group'])) throw new Exception('[4]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($_SESSION['username'])) throw new Exception('[5]出现意外错误，请与我们的在线客服联系。');
        $data = $this->web->query_fetch_single("select * from [X_TEAM_RESET_AWARD] WHERE [ID] = ?",[$_REQUEST['award_id']]);
        if(!is_array($data) || empty($data)) throw new Exception('[6]出错了，请您重新尝试。');
        if(!class_exists('Plugin\equipment')) throw new Exception('[7]未启用透视物品插件，请联系技术人员+。');
        $equipment = new \Plugin\equipment();
        $itemQuery = $equipment->generateBoxQuery($_SESSION['userid'],$_SESSION['username'],$_REQUEST['character'],$data['award_item']);
        $muonline = Connection::Database("MuOnline",$_SESSION['group']);
        try{
            $this->web->beginTransaction();
            $muonline->beginTransaction();
            for ($i=0;$i<count($itemQuery);$i++){
                $muonline->query($itemQuery[$i]);
            }
            $this->web->query("INSERT INTO [X_TEAM_RESET_AWARD_LOG] ([award_id],[servercode],[username],[character],[receive_time]) VALUES (?,?,?,?,?)",[$_REQUEST['award_id'],getServerCodeForGroupID($_SESSION['group']),$_SESSION['username'],$_REQUEST['character'],logDate()]);
            $this->web->commit();
            $muonline->commit();
            alert("usercp/reset","恭喜您，角色[".$_REQUEST['character']."]成功领取[".$data['award_name']."]转身奖励！");
        }catch (Exception $exception){
            $this->web->rollBack();
            $muonline->rollBack();
            throw new Exception($exception->getMessage());
        }
    }
    /**
     * 清洗红名
     * @param $group
     * @param $username
     * @param $character_name
     * @param $uid
     * @throws Exception
     */
    function setCharacterClearPK($group, $username, $character_name, $uid)
    {
        if (!Validator::Number($uid)) throw new Exception('[0]出现意外错误，请与我们的在线客服联系。');
        if(!Token::checkToken('clearpk'.$uid)) throw new Exception('[1]出错了，请您重新输入！');
        if (!check_value($username)) throw new Exception('[2]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($character_name)) throw new Exception('[3]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::UsernameLength($username)) throw new Exception('[4]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::AlphaNumeric($username)) throw new Exception('[5]出现意外错误，请与我们的在线客服联系。');
        if (!$this->_checkCharacterExists($group, $character_name)) throw new Exception('[6]您没有权限对该角色使用清洗红名。');
        if (!$this->_checkCharacterBelongsToAccount($group, $character_name, $username)) throw new Exception('[7]您没有权限对该角色使用清洗红名。');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('[8]您的账号已在线，请断开连接。');

        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);
        if (mconfig('clearpk_price_zen')) {
            if ($characterData[_CLMN_CHR_ZEN_] < mconfig('clearpk_price_zen')) throw new Exception('[9]您没有足够的金币。');
            $deductZen = $this->DeductZEN($group, $character_name, mconfig('clearpk_price_zen'));
            if (!$deductZen) throw new Exception('[10]您没有足够的金币。');
        }

        $update = Connection::Database('MuOnline', $group)->query("UPDATE " . _TBL_CHR_ . " SET " . _CLMN_CHR_PK_LEVEL_ . " = 3," . _CLMN_CHR_PK_TIME_ . " = 0 WHERE " . _CLMN_CHR_NAME_ . " = ?", array($character_name));
        if (!$update) throw new Exception('[11]出现意外错误，请与我们的在线客服联系。');

        // SUCCESS
        alert('usercp/clearpk','您的角色已成功清洗红名!');
    }

    /**
     * 角色自救
     * 将角色移动到大陆酒吧
     * @param $group
     * @param $username
     * @param $character_name
     * @param $uid
     * @throws Exception
     */
    function setCharacterUnstick($group, $username, $character_name, $uid)
    {
        if (!Validator::Number($uid)) throw new Exception('[0]出现意外错误，请与我们的在线客服联系。');
        if(!Token::checkToken('Unstick'.$uid)) throw new Exception('[1]出错了，请您重新输入！');
        if (!check_value($username)) throw new Exception('[2]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($character_name)) throw new Exception('[3]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::UsernameLength($username)) throw new Exception('[4]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::AlphaNumeric($username)) throw new Exception('[5]出现意外错误，请与我们的在线客服联系。');
        if (!$this->_checkCharacterExists($group, $character_name)) throw new Exception('[6]您无权自救这个角色的位置。');
        if (!$this->_checkCharacterBelongsToAccount($group, $character_name, $username)) throw new Exception('[7]您无权自救这个角色的位置。');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('[8]您的账号已在线，请断开连接。');

        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);

        if (mconfig('unstick_price_zen')) {
            if ($characterData[_CLMN_CHR_ZEN_] < mconfig('unstick_price_zen')) throw new Exception('[9]您没有足够的金币。');
            $deductZen = $this->DeductZEN($group, $character_name, mconfig('unstick_price_zen'));
            if (!$deductZen) throw new Exception('[10]您没有足够的金币。');
        }

        // 将角色移动到勇者大陆酒吧（默认编码）
        $update = $this->moveCharacter($group, $character_name, 0, 125, 125);
        if (!$update) throw new Exception('[11]出现意外错误，请与我们的在线客服联系。');

        // 成功
        $_POST = [];
        alert('usercp/unstick','您的角色已成功移动至大陆酒吧!');
    }

    /**
     * 清理大师技能
     * @param $group
     * @param $username
     * @param $character_name
     * @param $uid
     * @throws Exception
     */
    function setCharacterClearMaster($group, $username, $character_name, $uid)
    {
        if (!Validator::Number($uid)) throw new Exception('[0]出现意外错误，请与我们的在线客服联系。');
        if(!Token::checkToken('clearMaster'.$uid)) throw new Exception('[1]出错了，请您重新输入！');
        if (!check_value($username)) throw new Exception('[2]出现意外错误，请与我们的在线客服联系。');
        if (!check_value($character_name)) throw new Exception('[3]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::UsernameLength($username)) throw new Exception('[4]出现意外错误，请与我们的在线客服联系。');
        if (!Validator::AlphaNumeric($username)) throw new Exception('[5]出现意外错误，请与我们的在线客服联系。');
        if (!$this->_checkCharacterExists($group, $character_name)) throw new Exception('[6]出现意外错误，请与我们的在线客服联系。');
        if (!$this->_checkCharacterBelongsToAccount($group, $character_name, $username)) throw new Exception('[7]出现意外错误，请与我们的在线客服联系。');
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('[8]您的账号已在线，请断开连接。');

        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);
        # 验证大师等级是否达标
        if ($characterData['mLevel'] < mconfig('clearst_required_level')) throw new Exception('[9]您的大师等级必须达到['.mconfig('clearst_required_level').']以上才可使用该功能。');

        if (mconfig('clearst_enable_zen_requirement')) {
            if ($characterData[_CLMN_CHR_ZEN_] < mconfig('clearst_price')) throw new Exception('[10]您没有足够的金币。');
            $deductZen = $this->DeductZEN($group, $character_name, mconfig('clearst_price'));
            if (!$deductZen) throw new Exception('[11]您没有足够的金币。');
        }else{
            #减积分
            $creditSystem = new CreditSystem();
            $creditSystem->setConfigId((int)mconfig('clearst_credit_type'));
            $configSettings = $creditSystem->showConfigs(true);
            switch ($configSettings['config_user_col_id']) {
                case 'username':
                    $creditSystem->setIdentifier($username);
                    break;
                case 'userid':
                    $creditSystem->setIdentifier($_SESSION['userid']);
                    break;
                case 'character':
                    $creditSystem->setIdentifier($character_name);
                    break;
                default:
                    throw new Exception("[货币系统]无效的标识符。");
            }
            $creditSystem->subtractCredits($group,(int)mconfig('clearst_price'));
        }

        // 清洗大师技能数据
        $update = $this->resetMasterPoint($group, $character_name);
        if (!$update) throw new Exception('[12]'.'出现意外错误，请与我们的在线客服联系。');

        // 清除技能数据
        $update_2 = $this->resetMagicList($group, $character_name, $characterData['Class']);
        if (!$update_2) throw new Exception('[13]'.'出现意外错误，请与我们的在线客服联系。');

        // 成功
        alert('usercp/clearMaster','您的角色大师技能已经清洗成功!');
    }

    /**
     * 账号找到角色名[Character]
     * @param $group
     * @param $username
     * @return array|void
     * @throws Exception
     */
    function getCharacterNameForUsername($group, $username)
    {
        if (!check_value($username)) return;
        if (!Validator::UsernameLength($username)) return;
        if (!Validator::AlphaNumeric($username)) return;

        $result = Connection::Database('MuOnline', $group)->query_fetch("SELECT [" . _CLMN_CHR_NAME_ ."] FROM [" . _TBL_CHR_ . "] WHERE " . _CLMN_CHR_ACCID_ . " = ?", [$username]);
        if (!is_array($result)) return;
        foreach ($result as $row) {
            if (!check_value($row[_CLMN_CHR_NAME_])) continue;
            $return[] = $row[_CLMN_CHR_NAME_];
        }

        if (empty($return)) return;
        return $return;
    }

    /**
     * 从账号角色表中获取角色信息[AccountCharacter]
     * @param $group
     * @param $username
     * @return array|void
     * @throws Exception
     */
    function getAccountCharacterNameForAccount($group, $username){
        if (!check_value($username)) return;
        if (!Validator::UsernameLength($username)) return;
        if (!Validator::AlphaNumeric($username)) return;
        switch ($this->serverFiles) {
            case "mudevs":
            case "igcn":
                $query = "SELECT GameID1,GameID2,GameID3,GameID4,GameID5,GameID6,GameID7,GameID8,GameID9,GameID10 FROM AccountCharacter WHERE Id = ?";
                break;
            default:
                $query = "SELECT GameID1,GameID2,GameID3,GameID4,GameID5 FROM AccountCharacter WHERE Id = ?";
                break;
        }
        $result = Connection::Database('MuOnline', $group)->query_fetch_single($query, [$username]);
        if (!is_array($result)) return;
        return $result;
    }

    /**
     * 角色名找到角色数据[Character]
     * @param $group
     * @param $character_name
     * @param bool $all
     * @return mixed|void|null
     * @throws Exception
     */
    function getCharacterDataForCharacterName($group, $character_name, $all = false)
    {
        if (!check_value($group)) return;
        if (!check_value($character_name)) return;
        switch ($this->serverFiles) {
                #大师不同表,家族不用表
            case "xteam":
            case "louis":
            case "igcn":
            case "egames":
            case "haoyi":
                $query = "SELECT 
                            CharacterSystem.AccountID,
                            CharacterSystem.Name,
                            CharacterSystem.cLevel,
                            CharacterSystem.Class,
                            CharacterSystem.LevelUpPoint,
                            CharacterSystem.Strength,
                            CharacterSystem.Dexterity,
                            CharacterSystem.Vitality,
                            CharacterSystem.Energy,
                            CharacterSystem.Leadership,
                            CONVERT(varchar(max),CharacterSystem.MagicList,2) AS MagicList,
                            CONVERT(varchar(max),CharacterSystem.Inventory,2) AS Inventory,
                            CharacterSystem.Money,
                            CharacterSystem.MapNumber,
                            CharacterSystem.PkLevel,
                            CharacterSystem.PkCount,
                            CharacterSystem.CtlCode,
                            CharacterSystem."._CLMN_CHR_RSTS_.",
                            MasterSystem."._CLMN_ML_LVL_." as mLevel,
                            MasterSystem."._CLMN_ML_POINT_." as mPoint,
                            GensSystem."._CLMN_GENS_TYPE_." AS GenFamily,
                            GensSystem."._CLMN_GENS_LEVEL_." AS GenLevel,
                            GensSystem."._CLMN_GENS_POINT_." as GensContribution
                            FROM Character AS CharacterSystem
                            LEFT JOIN ["._TBL_GENS_."] AS GensSystem
                            ON CharacterSystem.Name = GensSystem."._CLMN_GENS_NAME_."
                            collate Chinese_PRC_CI_AS
                            LEFT JOIN ["._TBL_MASTERLVL_."] AS MasterSystem 
                            ON CharacterSystem.Name = MasterSystem."._CLMN_ML_NAME_."
                            collate Chinese_PRC_CI_AS
                            WHERE CharacterSystem.Name = ?";
                break;
            case "mudevs":
                $query = "SELECT 
                            CharacterSystem.AccountID,
                            CharacterSystem.Name,
                            CharacterSystem.cLevel,
                            CharacterSystem.Class,
                            CharacterSystem.LevelUpPoint,
                            CharacterSystem.Strength,
                            CharacterSystem.Dexterity,
                            CharacterSystem.Vitality,
                            CharacterSystem.Energy,
                            CharacterSystem.Leadership,
                            CONVERT(varchar(max),CharacterSystem.MagicList,2) AS MagicList,
                            CONVERT(varchar(max),CharacterSystem.Inventory,2) AS Inventory,
                            CharacterSystem.Money,
                            CharacterSystem.MapNumber,
                            CharacterSystem.PkLevel,
                            CharacterSystem.PkCount,
                            CharacterSystem.CtlCode,
                            CharacterSystem."._CLMN_CHR_RSTS_.",
                            MasterSystem."._CLMN_ML_LVL_." as mLevel,
                            MasterSystem."._CLMN_ML_POINT_." as mPoint,
                            GensSystem."._CLMN_GENS_TYPE_." AS GenFamily,
                            GensRewardSystem."._CLMN_GENS_LEVEL_." AS GenLevel,
                            GensSystem."._CLMN_GENS_POINT_." as GensContribution                            
                            FROM Character AS CharacterSystem
                            LEFT JOIN ["._TBL_MASTERLVL_."] AS MasterSystem 
                            ON CharacterSystem.Name = MasterSystem."._CLMN_ML_NAME_."
                            collate Chinese_PRC_CI_AS
                            LEFT JOIN ["._TBL_GENS_."] AS GensSystem 
                            ON CharacterSystem.Name = GensSystem."._CLMN_GENS_NAME_."
                            collate Chinese_PRC_CI_AS
                            LEFT JOIN Gens_Reward AS GensRewardSystem 
                            ON CharacterSystem.Name = GensRewardSystem.Name
                            collate Chinese_PRC_CI_AS
                            WHERE CharacterSystem.Name = ?";
                break;
            default:
                return;
        }

        $result = Connection::Database('MuOnline', $group)->query_fetch_single($query,[$character_name]);
        if (!is_array($result)) return;
        return $result;
    }


    /**
     * 用户名找到角色数据
     * @param $group
     * @param $username
     * @return mixed|void|null
     * @throws Exception
     */
    function getCharacterDataForUsername($group,$username){
        if (!check_value($group)) return;
        if (!check_value($username)) return;
        switch ($this->serverFiles) {
            case "mudevs":
                $query = "SELECT 
                            CharacterSystem.AccountID,
                            CharacterSystem.Name,
                            CharacterSystem.cLevel,
                            CharacterSystem.LevelUpPoint,
                            CharacterSystem.Class,
                            CharacterSystem.Strength,
                            CharacterSystem.Dexterity,
                            CharacterSystem.Vitality,
                            CharacterSystem.Energy,
                            CONVERT(varchar(max),CharacterSystem.MagicList,2) AS MagicList,
                            CONVERT(varchar(max),CharacterSystem.Inventory,2) AS Inventory,
                            CharacterSystem.Money,
                            CharacterSystem.MapNumber,
                            CharacterSystem.PkLevel,
                            CharacterSystem.PkCount,
                            CharacterSystem.CtlCode,
                            CharacterSystem.Leadership,
                            MasterSystem."._CLMN_ML_LVL_." as mLevel,
                            MasterSystem."._CLMN_ML_POINT_." as mPoint,
                            GensSystem."._CLMN_GENS_TYPE_." as GenFamily,
                            GensRewardSystem."._CLMN_GENS_LEVEL_." as GenLevel,
                            GensSystem."._CLMN_GENS_POINT_." as GensContribution
                            FROM Character AS CharacterSystem
                            LEFT JOIN ["._TBL_MASTERLVL_."] AS MasterSystem 
                            ON CharacterSystem.Name = MasterSystem."._CLMN_ML_NAME_."
                            collate Chinese_PRC_CI_AS
                            LEFT JOIN ["._TBL_GENS_."] AS GensSystem 
                            ON CharacterSystem.Name = GensSystem."._CLMN_GENS_NAME_."
                            collate Chinese_PRC_CI_AS
                            LEFT JOIN Gens_Reward AS GensRewardSystem 
                            ON CharacterSystem.Name = GensRewardSystem.Name
                            collate Chinese_PRC_CI_AS
                            WHERE CharacterSystem.AccountID = ?";
                break;
            case "igcn":
            case "egames":
            case "haoyi":
                $query = "SELECT 
                            CharacterSystem.AccountID,
                            CharacterSystem.Name,
                            CharacterSystem.cLevel,
                            CharacterSystem.LevelUpPoint,
                            CharacterSystem.Class,
                            CharacterSystem.Strength,
                            CharacterSystem.Dexterity,
                            CharacterSystem.Vitality,
                            CharacterSystem.Energy,
                            CONVERT(varchar(max),CharacterSystem.MagicList,2) AS MagicList,
                            CONVERT(varchar(max),CharacterSystem.Inventory,2) AS Inventory,
                            CharacterSystem.Money,
                            CharacterSystem.MapNumber,
                            CharacterSystem.PkLevel,
                            CharacterSystem.PkCount,
                            CharacterSystem.CtlCode,
                            CharacterSystem.Leadership,
                            MasterSystem."._CLMN_ML_LVL_." as mLevel,
                            MasterSystem."._CLMN_ML_POINT_." as mPoint,
                            GensSystem."._CLMN_GENS_TYPE_." as GenFamily,
                            GensSystem."._CLMN_GENS_LEVEL_." as GenLevel,
                            GensSystem."._CLMN_GENS_POINT_." as GensContribution
                            FROM Character AS CharacterSystem
                            LEFT JOIN ["._TBL_MASTERLVL_."] AS MasterSystem 
                            ON CharacterSystem.Name = MasterSystem."._CLMN_ML_NAME_."
                            collate Chinese_PRC_CI_AS
                            LEFT JOIN ["._TBL_GENS_."] AS GensSystem
                            ON CharacterSystem.Name = GensSystem."._CLMN_GENS_NAME_." 
                            collate Chinese_PRC_CI_AS
                            WHERE CharacterSystem.AccountID = ?";
                break;
            default:
                return;
        }

        $result = Connection::Database('MuOnline', $group)->query_fetch($query,[$username]);
        if (!is_array($result)) return;
        return $result;
    }

    /**
     * 角色所属账号
     * @param $group
     * @param $character_name
     * @param $username
     * @return bool|void
     * @throws Exception
     */
    function _checkCharacterBelongsToAccount($group, $character_name, $username)
    {
        if (!check_value($character_name)) return;
        if (!check_value($username)) return;
        if (!Validator::UsernameLength($username)) return;
        if (!Validator::AlphaNumeric($username)) return;
        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);
        if (!is_array($characterData)) return;
        if ($characterData[_CLMN_CHR_ACCID_] != $username) return;
        return true;
    }

    /**
     * 角色是否存在
     * @param $group
     * @param $character_name
     * @return bool|void
     * @throws Exception
     */
    function _checkCharacterExists($group, $character_name)
    {
        if (!check_value($group)) return;
        if (!check_value($character_name)) return;
        $check = Connection::Database('MuOnline', $group)->query_fetch_single("SELECT " . _CLMN_CHR_NAME_ . " FROM " . _TBL_CHR_ . " WHERE " . _CLMN_CHR_NAME_ . " = ?", [$character_name]);
        if (!is_array($check)) return false;
        return true;
    }

    /**
     * 扣除MU币
     * @param $group
     * @param $character_name
     * @param $zen_amount
     * @return bool|void
     * @throws Exception
     */
    function DeductZEN($group, $character_name, $zen_amount)
    {
        if (!check_value($character_name)) return;
        if (!check_value($zen_amount)) return;
        if (!Validator::UnsignedNumber($zen_amount)) return;
        if ($zen_amount < 1) return;
        if (!$this->_checkCharacterExists($group, $character_name)) return;
        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);
        if (!is_array($characterData)) return;
        if ($characterData[_CLMN_CHR_ZEN_] < $zen_amount) return;
        $deduct = Connection::Database('MuOnline', $group)->query("UPDATE " . _TBL_CHR_ . " SET " . _CLMN_CHR_ZEN_ . " = " . _CLMN_CHR_ZEN_ . " - ? WHERE " . _CLMN_CHR_NAME_ . " = ?", array($zen_amount, $character_name));
        if (!$deduct) return;
        return true;
    }

    /**
     * 移动角色
     * @param $group
     * @param $character_name
     * @param int $map
     * @param int $x
     * @param int $y
     * @return bool|void
     * @throws Exception
     */
    function moveCharacter($group, $character_name, $map = 0, $x = 125, $y = 125)
    {
        if (!check_value($character_name)) return;
        $move = Connection::Database('MuOnline', $group)->query("UPDATE " . _TBL_CHR_ . " SET " . _CLMN_CHR_MAP_ . " = ?, " . _CLMN_CHR_MAP_X_ . " = ?, " . _CLMN_CHR_MAP_Y_ . " = ? WHERE " . _CLMN_CHR_NAME_ . " = ?", array($map, $x, $y, $character_name));
        if (!$move) return;
        return true;
    }

    /**
     * 获取最后登陆的角色
     * @param $group
     * @param $username
     * @return mixed|void
     * @throws Exception
     */
    function getAccountCharacterIDC($group, $username)
    {
        if (!check_value($group)) return;
        if (!check_value($username)) return;
        if (!Validator::UsernameLength($username)) return;
        if (!Validator::AlphaNumeric($username)) return;
        $data = Connection::Database('MuOnline', $group)->query_fetch_single("SELECT "._CLMN_GAMEIDC_." FROM ". _TBL_AC_." WHERE "._CLMN_AC_ID_." = ?", [$username]);
        if (!is_array($data)) return;
        return $data[_CLMN_GAMEIDC_];
    }

    /**
     * @param $group
     * @param $username
     * @return mixed|string|void
     * @throws Exception
     */
    function getSessionCharacterName($group,$username){
        if (!check_value($group)) return;
        if (!check_value($username)) return;
        if (!Validator::UsernameLength($username)) return;
        if (!Validator::AlphaNumeric($username)) return;
        $char_name = $this->getAccountCharacterIDC($group,$username);
        if(!$char_name) return '';
        $data = Connection::Database("MuOnline", $group)->query_fetch_single("SELECT Name FROM ["._TBL_CHR_."] WHERE "._CLMN_CHR_ACCID_." = ? AND "._CLMN_CHR_NAME_." = ? ",[$username,$char_name]);
        if(!is_array($data)) return '';
        return $data['Name'];
    }

    /**
     * 生成角色类头像
     * @param int $code
     * @param bool $alt
     * @param bool $img_tags
     * @param null $width
     * @param string $class
     * @return string
     * @throws Exception
     */
    function GenerateCharacterClassAvatar($code = 0, $alt = true, $img_tags = true, $width=null, $class='rounded')
    {
        return getPlayerClassAvatar($code, $img_tags, $alt, $class,$width);
    }


    /**
     * 重置大师技能
     * @param $group
     * @param $character_name
     * @return bool|void
     * @throws Exception
     */
    function resetMasterPoint($group, $character_name)
    {
        if (!check_value($character_name)) return;
        if (!$this->_checkCharacterExists($group, $character_name)) return;
        $characterData = $this->getCharacterDataForCharacterName($group, $character_name);
        if(!is_array($characterData)) return;
        $mPoint = $characterData['mLevel'] * (int)mconfig('clearst_master_point');
        if(!$mPoint) return;
        $reset = Connection::Database('MuOnline', $group)->query("UPDATE "._TBL_MASTERLVL_." SET "._CLMN_ML_POINT_." = ? WHERE "._CLMN_ML_NAME_." = ?", [$mPoint,$character_name]);
        if (!$reset) return;
        return true;
    }

    /**
     * 清洗技能
     * @param $group
     * @param $character_name
     * @param $character_class
     * @return bool|void
     * @throws Exception
     */
    function resetMagicList($group, $character_name, $character_class)
    {
        if (!check_value($group)) return;
        if (!check_value($character_name)) return;
        if (!check_value($character_class)) return;
        if (!$this->_checkCharacterExists($group, $character_name)) return;
        $check = Connection::Database("Web")->query_fetch("SELECT [ID],[Class],[Name],CONVERT(varchar(max),[MagicList],2) AS [MagicList],[status] FROM [X_TEAM_CLEAR_MASTER]");
        $Skill = NULL;
        if(is_array($check)){
            foreach ($check as $data){
                if($data['Class'] == $character_class) $Skill = $data['MagicList'];
            }
        }
        $reset = Connection::Database('MuOnline', $group)->query("UPDATE " . _TBL_CHR_ . " SET " . _CLMN_CHR_MAGIC_L_ . " = CONVERT(VARBINARY(MAX),?,2) WHERE " . _CLMN_CHR_NAME_ . " = ?", [$Skill,$character_name]);
        if (!$reset) return;
        return true;
    }

    /**
     * 从用户名获取在线数据
     * @param $group
     * @param $accountID
     * @return array|bool|void|null
     * @throws Exception
     */
    function getOnlineDataForUsername($group, $accountID){
        if (!check_value($group)) return;
        if (!check_value($accountID)) return;
        # 给个初始值防止登陆面板无数据!
        $data = [
            'ConnectStat' => '0',
            'IP' => '111.111.111.111',
            'ServerName' => '未定义',
            'ConnectTM' => '2021-01-01 00:00:00',
            'DisConnectTM' => '2021-01-01 00:00:00'
        ];
        $DatabaseData = Connection::Database('Me_MuOnline',$group)->query_fetch_single("SELECT ConnectStat,IP,ServerName,ConnectTM,DisConnectTM FROM "._TBL_MS_." WHERE memb___id = ?",[$accountID]);
        if(!is_array($DatabaseData)){
            return $data;
        }
        return array_replace_recursive($data,$DatabaseData);
    }

    /**
     * 获取角色所属战盟
     * @param $group
     * @param $name
     * @return mixed|void|null
     * @throws Exception
     */
    function getGuildNameForName($group, $name){
        if (!check_value($group)) return;
        if (!check_value($name)) return;
        $data = Connection::Database('MuOnline',$group)->query_fetch_single("SELECT "._CLMN_GUILDMEMB_NAME_." FROM "._TBL_GUILDMEMB_." WHERE ["._CLMN_GUILDMEMB_CHAR_."] = ?",[$name]);
        if (!$data) return;
        return $data['G_Name'];
    }

    /**
     * 获取战盟信息从角色名
     * @param $group
     * @param $gName
     * @return mixed|void|null
     * @throws Exception
     */
    function getGuildDataForGuildName($group, $gName){
        if (!check_value($group)) return;
        if (!check_value($gName)) return;
        $result = Connection::Database('MuOnline', $group)->query_fetch_single("SELECT CONVERT(varchar(max),G_Mark,2) AS G_Mark,G_Score,G_Master,G_Count,G_Notice,Number FROM Guild WHERE G_Name = ?", [$gName]);

        if (!is_array($result)) return;
        return $result;
    }

    /**
     * 获取战盟人数从战盟名
     * 直接返回人数
     * @param $group
     * @param $gName
     * @return mixed|void|null
     * @throws Exception
     */
    function getGuildMemberForGuildName($group,$gName){
        if (!check_value($group)) return;
        if (!check_value($gName)) return;
        $result = Connection::Database('MuOnline', $group)->query_fetch_single("SELECT count(G_Name) AS Member FROM GuildMember WHERE [G_Name] = ?", [$gName]);

        if (!is_array($result)) return;
        return $result['Member'];
    }

    /**
     * 从角色名获取角色类型
     * @param $name
     * @return mixed|void
     * @throws Exception
     */
    function getCharacterClassForName($name){
        if (!check_value($name)) return;
        $result = Connection::Database('MuOnline', $_SESSION['group'])->query_fetch_single("SELECT Class FROM Character WHERE Name = ?",[$name]);
        if (!is_array($result)) return;
        return $result['Class'];
    }

    /**
     * 检测角色背包是否有物品
     * @param $Inventory
     * @return bool
     */
    function checkCharacterEquipmentItems($Inventory){
        if (!preg_match('/[a-fA-F0-9]/',$Inventory)) return false;
        $Inventory = str_split($Inventory, ITEM_SIZE);
        if(!is_array($Inventory)) return false;
        for ($i=0;$i<12;$i++){
            if($Inventory[$i] != str_pad("F",ITEM_SIZE,"F")){
                return false;
                break;
            }
        }

        if($this->serverFiles == "igcn"){
            if($Inventory[236] != str_pad("F",ITEM_SIZE,"F")
                || $Inventory[237] != str_pad("F",ITEM_SIZE,"F")
                || $Inventory[238] != str_pad("F",ITEM_SIZE,"F"))
                return false;
        }
        return true;
    }

    /**
     * 检测角色是否封停
     * @param $group
     * @param $char_name
     * @return int
     * @throws Exception
     */
    function checkCharacterIsBan($group,$char_name){
        if(!check_value($char_name)) return 0;
        if(!check_value($char_name)) return 0;
        $result = Connection::Database('MuOnline', $group)->query_fetch_single("SELECT [CtlCode] FROM [Character] WHERE [Name] = ?",[$char_name]);
        if (!is_array($result)) return 0;
        if($result['CtlCode']) return 0;
        return 1;
    }
}