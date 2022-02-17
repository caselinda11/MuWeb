<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('ITEM_SIZE',32);

define('_TBL_MI_', 'MEMB_INFO');
	define('_CLMN_USERNM_', 'memb___id');
	define('_CLMN_PASSWD_', 'memb__pwd');
	define('_CLMN_MEMBID_', 'memb_guid');
	define('_CLMN_EMAIL_', 'mail_addr');
	define('_CLMN_BLOCCODE_', 'bloc_code');
	define('_CLMN_SNONUMBER_', 'sno__numb');
	define('_CLMN_MEMBNAME_', 'memb_name');
	define('_CLMN_CTLCODE_', 'ctl1_code');
    define('_CLMN_GROUP_','servercode');

define('_TBL_MS_', 'MEMB_STAT');
	define('_CLMN_CONNSTAT_', 'ConnectStat');
	define('_CLMN_MS_MEMBID_', 'memb___id');
	define('_CLMN_MS_GS_', 'ServerName');
	define('_CLMN_MS_IP_', 'IP');
	define('_CLMN_MS_ONLINEHRS_', 'OnlineHours');
	
define('_TBL_AC_', 'AccountCharacter');
	define('_CLMN_AC_ID_', 'Id');
	define('_CLMN_GAMEIDC_', 'GameIDC');
	define('_CLMN_WHEXPANSION_', 'ExtWarehouse');
	
define('_TBL_CHR_', 'Character');
	define('_CLMN_CHR_NAME_', 'Name');
	define('_CLMN_CHR_ACCID_', 'AccountID');
	define('_CLMN_CHR_CLASS_', 'Class');
	define('_CLMN_CHR_ZEN_', 'Money');
	define('_CLMN_CHR_LVL_', 'cLevel');
	define('_CLMN_CHR_RSTS_', 'ResetCount');
	define('_CLMN_CHR_GRSTS_', 'MasterResetCount');
	define('_CLMN_CHR_LVLUP_POINT_', 'LevelUpPoint');
	define('_CLMN_CHR_STAT_STR_', 'Strength');
	define('_CLMN_CHR_STAT_AGI_', 'Dexterity');
	define('_CLMN_CHR_STAT_VIT_', 'Vitality');
	define('_CLMN_CHR_STAT_ENE_', 'Energy');
	define('_CLMN_CHR_STAT_CMD_', 'Leadership');
	define('_CLMN_CHR_PK_KILLS_', 'PkCount');
	define('_CLMN_CHR_PK_LEVEL_', 'PkLevel');
	define('_CLMN_CHR_PK_TIME_', 'PkTime');
	define('_CLMN_CHR_MAP_', 'MapNumber');
	define('_CLMN_CHR_MAP_X_', 'MapPosX');
	define('_CLMN_CHR_MAP_Y_', 'MapPosY');
	define('_CLMN_CHR_MAGIC_L_', 'MagicList');

define('_TBL_MASTERLVL_', 'MasterSkillTree');
	define('_CLMN_ML_NAME_', 'Name');
	define('_CLMN_ML_LVL_', 'MasterLevel');
    define('_CLMN_ML_EXP_', 'ML_EXP');
    define('_CLMN_ML_NEXP_', 'ML_NEXTEXP');
	define('_CLMN_ML_POINT_', 'MasterPoint');
	define('_CLMN_ML_SKILL_', 'MasterSkill');

define('_TBL_MUCASTLE_DATA_', 'MuCastle_DATA');
	define('_CLMN_MCD_GUILD_OWNER_', 'OWNER_GUILD');
	define('_CLMN_MCD_MONEY_', 'MONEY');
	define('_CLMN_MCD_TRC_', 'TAX_RATE_CHAOS');
	define('_CLMN_MCD_TRS_', 'TAX_RATE_STORE');
	define('_CLMN_MCD_THZ_', 'TAX_HUNT_ZONE');
	
define('_TBL_GUILD_', 'Guild');
	define('_CLMN_GUILD_NAME_', 'G_Name');
	define('_CLMN_GUILD_LOGO_', 'G_Mark');
	define('_CLMN_GUILD_SCORE_', 'G_Score');
	define('_CLMN_GUILD_MASTER_', 'G_Master');
	define('_CLMN_GUILD_NOTICE_', 'G_Notice');
	define('_CLMN_GUILD_UNION_', 'G_Union');
	
define('_TBL_GUILDMEMB_', 'GuildMember');
	define('_CLMN_GUILDMEMB_CHAR_', 'Name');
	define('_CLMN_GUILDMEMB_NAME_', 'G_Name');
	define('_CLMN_GUILDMEMB_LEVEL_', 'G_Level');
	define('_CLMN_GUILDMEMB_STATUS_', 'G_Status');
	
define('_TBL_MUCASTLE_RS_', 'MuCastle_REG_SIEGE');
	define('_CLMN_MCRS_GUILD_', 'REG_SIEGE_GUILD');
	
define('_TBL_GENS_', 'Gens_Rank');
	define('_CLMN_GENS_NAME_', 'Name');
	define('_CLMN_GENS_TYPE_', 'Family');
	define('_CLMN_GENS_POINT_', 'Contribution');
    define('_CLMN_GENS_LEVEL_', 'Rank');

/**
 * 自定义配置: 角色类型
 */
$custom['character_class'] = [
    #类型    [#名称          #缩写    #图片           [#属性点        #力量         #敏捷          #体力          #智力          #统率]]
    0	=> 	['法师', 		'DW',	'dw.jpg',		'base_stats' => ['str' => 18, 'agi' => 18, 'vit' => 15, 'ene' => 30, 'cmd' => 0]],
    1 	=> 	['魔导士', 		'SM',	'dw.jpg',		'base_stats' => ['str' => 18, 'agi' => 18, 'vit' => 15, 'ene' => 30, 'cmd' => 0]],
    3 	=> 	['神导师', 		'GM',	'dw.jpg',		'base_stats' => ['str' => 18, 'agi' => 18, 'vit' => 15, 'ene' => 30, 'cmd' => 0]],
    7 	=> 	['灵魂导师', 	'SW',	'dw.jpg',		'base_stats' => ['str' => 18, 'agi' => 18, 'vit' => 15, 'ene' => 30, 'cmd' => 0]],
    16 	=> 	['剑士', 		'DK',	'dk.jpg',		'base_stats' => ['str' => 28, 'agi' => 20, 'vit' => 25, 'ene' => 10, 'cmd' => 0]],
    17 	=> 	['骑士', 		'BK',	'dk.jpg',		'base_stats' => ['str' => 28, 'agi' => 20, 'vit' => 25, 'ene' => 10, 'cmd' => 0]],
    19 	=> 	['神骑士', 		'BM',	'dk.jpg',		'base_stats' => ['str' => 28, 'agi' => 20, 'vit' => 25, 'ene' => 10, 'cmd' => 0]],
    23 	=> 	['龙骑士', 		'DGK',	'dk.jpg',		'base_stats' => ['str' => 28, 'agi' => 20, 'vit' => 25, 'ene' => 10, 'cmd' => 0]],
    32 	=> 	['弓箭手', 		'ELF',	'elf.jpg',		'base_stats' => ['str' => 22, 'agi' => 25, 'vit' => 15, 'ene' => 20, 'cmd' => 0]],
    33 	=> 	['圣射手', 		'ME',	'elf.jpg',		'base_stats' => ['str' => 22, 'agi' => 25, 'vit' => 15, 'ene' => 20, 'cmd' => 0]],
    35 	=> 	['神射手', 		'HE',	'elf.jpg',		'base_stats' => ['str' => 22, 'agi' => 25, 'vit' => 15, 'ene' => 20, 'cmd' => 0]],
    39 	=> 	['贵族精灵', 	'NE',	'elf.jpg',		'base_stats' => ['str' => 22, 'agi' => 25, 'vit' => 15, 'ene' => 20, 'cmd' => 0]],
    48 	=> 	['魔剑士', 		'MG',	'mg.jpg',		'base_stats' => ['str' => 26, 'agi' => 26, 'vit' => 26, 'ene' => 16, 'cmd' => 0]],
    50 	=> 	['剑圣', 		'DM',	'mg.jpg',		'base_stats' => ['str' => 26, 'agi' => 26, 'vit' => 26, 'ene' => 16, 'cmd' => 0]],
    54 	=> 	['魔法骑士', 	'MK',	'mg.jpg',		'base_stats' => ['str' => 26, 'agi' => 26, 'vit' => 26, 'ene' => 16, 'cmd' => 0]],
    64 	=> 	['圣导师', 		'DL',	'dl.jpg',		'base_stats' => ['str' => 26, 'agi' => 20, 'vit' => 20, 'ene' => 15, 'cmd' => 25]],
    66 	=> 	['祭师', 		'LE',	'dl.jpg',		'base_stats' => ['str' => 26, 'agi' => 20, 'vit' => 20, 'ene' => 15, 'cmd' => 25]],
    70 	=> 	['帝国君王', 	'EL',	'dl.jpg',		'base_stats' => ['str' => 26, 'agi' => 20, 'vit' => 20, 'ene' => 15, 'cmd' => 25]],
    80 	=> 	['召唤术士', 	'SUM',	'sum.jpg',		'base_stats' => ['str' => 21, 'agi' => 21, 'vit' => 18, 'ene' => 23, 'cmd' => 0]],
    81 	=> 	['召唤导师', 	'BS',	'sum.jpg',		'base_stats' => ['str' => 21, 'agi' => 21, 'vit' => 18, 'ene' => 23, 'cmd' => 0]],
    83 	=> 	['召唤巫师', 	'DSM',	'sum.jpg',		'base_stats' => ['str' => 21, 'agi' => 21, 'vit' => 18, 'ene' => 23, 'cmd' => 0]],
    87 	=> 	['次元大师', 	'DS',	'sum.jpg',		'base_stats' => ['str' => 21, 'agi' => 21, 'vit' => 18, 'ene' => 23, 'cmd' => 0]],
    96 	=> 	['角斗士', 		'RF',	'rf.jpg',		'base_stats' => ['str' => 32, 'agi' => 27, 'vit' => 25, 'ene' => 20, 'cmd' => 0]],
    98 	=> 	['格斗大师', 	'FM',	'rf.jpg',		'base_stats' => ['str' => 32, 'agi' => 27, 'vit' => 25, 'ene' => 20, 'cmd' => 0]],
    102	=>	['拳王', 		'FB',	'rf.jpg',		'base_stats' => ['str' => 32, 'agi' => 27, 'vit' => 25, 'ene' => 20, 'cmd' => 0]],
    112	=>	['梦幻骑士', 	'GL',	'gl.jpg',		'base_stats' => ['str' => 30, 'agi' => 30, 'vit' => 25, 'ene' => 24, 'cmd' => 0]],
    114	=>	['魅影骑士', 	'ML',	'gl.jpg',		'base_stats' => ['str' => 30, 'agi' => 30, 'vit' => 25, 'ene' => 24, 'cmd' => 0]],
    118	=>	['光影骑士', 	'SL',	'gl.jpg',		'base_stats' => ['str' => 30, 'agi' => 30, 'vit' => 25, 'ene' => 24, 'cmd' => 0]],
];

/**
 * 自定义配置: 角色统率
 * 使用统率的角色类
 */
$custom['character_cmd'] = [64, 66, 70];

/**
 * 自定义配置: 家族排名
 */
$custom['gens_ranks'] = [
    10000 => '元帅',
    6000 => '将官',
    3000 => '校官',
    1500 => '尉官',
    500 => '士官',
    499 => '士兵'
];

/**
 * 自定义配置: 家族排名类型
 */
$custom['gens_ranks_leadership'] = [
    '士官'    =>  [0,0],
    '公爵'    =>  [1,4],
    '侯爵'    =>  [5,9],
    '伯爵'    =>  [10,29],
    '子爵'    =>  [30,49],
    '男爵'    =>  [50,99],
    '指挥官'  =>  [100,199],
    '司令官'  =>  [200,299]
];

/**
 * 自定义配置: 红名状态
 */
$custom['pk_level'] = [
    0=>'<span class="text-primary">大侠</span>',
    1=>'<span class="text-primary">英雄</span>',
    2=>'<span class="text-primary">好人</span>',
    3=>'<span class="text-info">义士</span>',
    4=>'<span class="text-warning">无赖</span>',
    5=>'<span class="text-warning">恶人</span>',
    6=>'<span class="text-danger">魔头</span>',
    7=>'<span class="text-danger">初级魔头</span>',
    8=>'<span class="text-danger">中级魔头</span>',
    9=>'<span class="text-danger">终极魔头</span>'
];