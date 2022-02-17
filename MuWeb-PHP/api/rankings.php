<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
define('access', 'api');

include('../includes/Init.php');
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(json_encode(['code' => '40004','msg'=> 'Invalid argument',"data" => []]));
$path = parse_url($_SERVER['HTTP_REFERER']);
if(empty($path['path']) || !isset($path['path'])) exit(json_encode(['code' => '40004','msg'=> 'Invalid argument',"data" => []]));
loadModuleConfigs('rankings');
switch ($path['path']){
    case "/rankings/level":
        #获取角色数据
        $ranking_data = LoadCacheData('rankings_level.cache');
        if(!is_array($ranking_data) || !$ranking_data) exit(json_encode(['code' => '20001','msg'=> 'none',"data" => []]));
        $i=1;
        $ranking_data = arraySortByKey($ranking_data,16,false);
        foreach ($ranking_data as $rankings){
            if(Validator::UnsignedNumber(mconfig('show_level_group'))) if(mconfig('show_level_group') == $rankings[0]) continue;
            $data[] = [
                'no'            => $i,
                'name'          =>  playerProfile($rankings[0],$rankings[2]).onlineStatus($rankings[17]),
                'avatar'        =>  getPlayerClassAvatar($rankings[5],1,1,'','30'),
                'class'         =>  getPlayerClassName($rankings[5]),
                'LevelUpPoint'  =>  $rankings[4],
                'cLevel'        =>  $rankings[3],
                'mLevel'        =>  $rankings[16],
                'MapNumber'     =>  getMapName($rankings[11]),
                'point'         =>  $rankings[6] + $rankings[7] + $rankings[8] + $rankings[9] + $rankings[15],
                'CtlCode'       =>  $rankings[14],
                'PkLevel'       =>  getPkLevel($rankings[12]),
                'PkCount'       =>  $rankings[13],
                'ConnectStat'   =>  $rankings[17],
                'servercode'    =>  getGroupNameForServerCode($rankings[0]),
            ];
            $i++;
        }
        exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$data]));
        break;
    case "/rankings/gens":
        $ranking_data = LoadCacheData('rankings_gens.cache');
        if(!is_array($ranking_data) || !$ranking_data) exit(json_encode(['code' => '20001','msg'=> 'none',"data" => []]));
        $ranking_data = arraySortByKey($ranking_data,9,false);
        $i=1;
        foreach ($ranking_data as $rankings) {
            if(Validator::UnsignedNumber(mconfig('show_level_group'))) if(mconfig('show_level_group') == $rankings[0]) continue;
            $data[] = [
                'no'            =>  $i,
                "name"          =>  playerProfile($rankings[0],$rankings[2]).onlineStatus($rankings[10]),
                "avatar"         => getPlayerClassAvatar($rankings[4],1,1,'','30'),
                'MapNumber'     =>  getMapName($rankings[5]),
                'gens'          => ($rankings[7] == 1) ? "多普瑞恩" : "巴纳尔特",
                'GenFamily'        =>  getImgForGensTypeId($rankings[7],$rankings[8]),
                'GensContribution' =>  getGensRank($rankings[9]),
                'GensPoint'        =>  (int)$rankings[9],
                "servercode"       =>  getGroupNameForServerCode($rankings[0]),
            ];
            $i++;
        }
        exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$data]));
        break;
    case "/rankings/guilds":
        $ranking_data = LoadCacheData('rankings_guilds.cache');
        if(!is_array($ranking_data) || !$ranking_data) exit(json_encode(['code' => '20001','msg'=> 'none',"data" => []]));
        $ranking_data = arraySortByKey($ranking_data,5,false);
        $i=1;
        foreach ($ranking_data as $rankings) {
            if(Validator::UnsignedNumber(mconfig('show_level_group'))) if(mconfig('show_level_group') == $rankings[0]) continue;
            $data[] = [
                'no'            =>  $i,
                "servercode"    =>  getGroupNameForServerCode($rankings[0]),
                "number"        =>  $rankings[5],
                "score"         =>  (int)$rankings[3],
                'name'          =>  playerProfile($rankings[0],$rankings[2]),
                'gName'         =>  guildProfile($rankings[0],$rankings[1]),
                'logo'          =>  getGuildLogo($rankings[4],30),
            ];
            $i++;
        }
        exit(json_encode(["code"=>"10000","msg"=>"Success","data"=>$data]));
        break;
    default:
        exit(json_encode(['code' => '20001','msg'=> 'Invalid argument',"data" => []]));
        break;
}


