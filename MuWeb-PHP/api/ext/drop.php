<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
define('access', 'api');

try{
    include('../../includes/Init.php');
//    // if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
//    $_GET['length'] = 25;
    $drop = new \Plugin\drop();
    $tConfig = $drop->loadConfig();
    if(!$tConfig['active']) exit(json_encode(['code' => '20001','msg'=> 'none',"data" => []]));
    //连接本地的 Redis 服务
	 $redis = new Redis();
    $redis->connect($tConfig['redis_ip'], $tConfig['redis_port']);
	
    if($tConfig['redis_pass']) $redis->auth($tConfig['redis_pass']);#密码验证
	#获取列表
    $list = $redis->lRange('drop-list', 0, -1);
    if(!$list || !is_array($list)) exit(json_encode(['code' => '20001','msg'=> 'none',"data" => []]));
    $count = count($list); #总数
	
	
    #全局搜索
    $search = trim($_GET['search']['value']);
    #处理全局搜索
//    if(strlen($search) > 0) {
//
//    }
    #类型搜索
    $typeSearch = trim($_GET['columns'][3]['search']['value']);
    #处理类型搜索
//    if(strlen($typeSearch) > 0) {
//
//    }
    #处理后的数据总数
    $recordsFiltered = $count;
    #处理分页
    $list = array_slice($list,intval($_GET['start']),$_GET['length']);
    $items = new \Plugin\equipment();
    foreach ($list as $key => $content){
        $exc = explode(",",substr(strstr(strstr($content,"Ex:"),")",1),4));
        $type = intval(strstr(substr(strstr(strstr($content,"编号:"),")"),2),")",1));
        $INDEX = $items->itemSolve($type);
        if(!is_array($INDEX)) exit(json_encode(['error' => '['.$key.']INDEX 解析错误。']));
        $itemOption = $items->getItem($INDEX[0],$INDEX[1]);
//        debug($itemOption);
        if(!is_array($itemOption)) exit(json_encode(['error' => '['.$key.']ITEM OPTION 解析错误。']));
        $itemType = $items->itemType($INDEX[0],$INDEX[1],$type,$itemOption['Slot']);
        if(!is_array($itemType)) exit(json_encode(['error' => '['.$key.']ITEM TYPE 解析错误。']));
        $itemName = substr(strstr(strstr(strstr($content,"编号:"),")",1),"("),1);
        $lucky = intval(substr(strstr(strstr(substr(strstr(substr(strstr(substr(strstr(strstr(strstr($content,"编号:"),")"),"Ex:",1),2),"("),1),"("),1),"("),")",1),1));
        $set = intval(substr(strstr(strrchr($content,"("),")",1),1));
        $htmlItemName = '<div class="text-white">'.$itemName.'</div>';
        if($lucky) $htmlItemName = '<div class="LuckyItem">'.$itemName.'</div>';
        if($set) $htmlItemName = '<div class="MasteryBonusText">'.$itemName.'</div>';
        if($exc[1] || $exc[2] || $exc[3] || $exc[4] || $exc[5] || $exc[6]) $htmlItemName = '<div class="ExcItemName">'.$itemName.'</div>';
        if($itemType[0] == 6) $htmlItemName = '<div class="Custom3">'.$itemName.'</div>';
        if($items->IsSocket) $htmlItemName = '<div class="SocketItemName">'.$itemName.'</div>';
        $img = file_exists(dirname(__DIR__,2).'/public/items/'.$INDEX[0].'/'.$INDEX[1].'.gif') ? __PATH_PUBLIC__.'items/'.$INDEX[0].'/'.$INDEX[1].'.gif' : __PATH_PUBLIC__.'items/empty.gif';
        $data[] = [
            'id'        => intval($_GET['start'])+$key+1,
            'type'      => $itemType[1],
//            'username'  => substr(strstr(strstr($content,"["),"]",1),1),
            'item_img'  => $img,
            'char_name' => strstr(substr(strstr($content,"]"),2),"]",1),
            'html_item_name' => $htmlItemName,
            'item_name' => $itemName,
//            'level'     => intval(strstr(substr(strstr(strstr(strstr(strstr(strstr($content,"编号:"),")"),"Ex:",1),"("),")"),2),")",1)),
//            'skill'     => intval(substr(strstr(strstr(substr(strstr(substr(strstr(strstr(strstr($content,"编号:"),")"),"Ex:",1),2),"("),1),"("),")",1),1)),
//            'lucky'     => $lucky,
//            'option'    => intval(substr(strstr(strrchr(strstr(strstr(strstr($content,"编号:"),")"),"Ex:",1),"("),")",1),1)),
//            'durability'=> intval($exc[0]),
//            'exc-1'       => intval($exc[1]), #卓越1J(+生)
//            'exc-2'       => intval($exc[2]), #等级20(+魔)
//            'exc-3'       => intval($exc[3]), #攻2%(-伤)
//            'exc-4'       => intval($exc[4]), #速度+7(反射)
//            'exc-5'       => intval($exc[5]), #杀怪回血(防10)
//            'exc-6'       => intval($exc[6]), #杀怪回魔(+钱)
            'map'       => getMapName(intval(substr(strstr(strstr($content,"("),")",1),1))),
            'location'  => '('.str_replace("/",",",strstr(substr(strstr($content,")"),1)," 丢弃",1)).')',
//            'set'       => $set,
            'serial'    => intval(str_replace("编号:","",strstr(strstr($content,"编号:")," (",1))),
            'time'      => logDate(strstr($content," [",1)),
        ];
    }
    #排序方式
//    if("desc" == $_GET['order'][0]['dir']){$data = array_reverse($data);}

    #拼接要返回的数据
    exit(json_encode([
        "draw" => intval($_GET['draw']),
        "recordsTotal" => intval($count),
        "recordsFiltered" => intval($recordsFiltered),
        "data"      => $data,
    ]));
}catch (Exception $exception){
    exit(json_encode($exception->getMessage()));
}

