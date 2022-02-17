<?php
/**
 * 抽奖API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

define('access', 'api');

include('../../includes/Init.php');
/*防止恶意查询*/
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
try {
    if($_POST['uid'] <= 1 && $_POST['uid'] >= 2) throw new Exception("禁止非法提交！");
//    if ((!Token::checkToken("lottery"))) throw new Exception("抽奖出错，请重新操作。");
    $lottery = new \Plugin\lottery();
    $tConfig = $lottery->loadConfig('config');
    #多次抽奖
    $many = ($_POST['uid'] == 1) ? false : true;
    $manyNumber = $tConfig['many_number'] ? 5 : 10;
    if (!isLoggedIn()) throw new Exception('请先登陆账号!');
    if(!serverGroupIDExists($_SESSION['group'])) throw new Exception('禁止非法提交！');
    #货币余额
    $_GET['page'] = 'usercp';
    $_GET['subpage'] = 'lottery';
    $configCredits = $lottery->setCredit($_SESSION['group'],$_SESSION['username'],$many);
    #获取最后登陆的玩家名
    $Character = new Character();
    $CharacterName = $Character->getAccountCharacterIDC($_SESSION['group'],$_SESSION['username']);

    #prize表示奖项内容，v表示中奖几率(若数组中七个奖项的v的总和为100，如果v的值为1，则代表中奖几率为1%，依此类推)
    $prize_arr = [
        0 => ['id' => 1, 'prize' => $tConfig['Crystal_Name'], 'v' => $tConfig['Crystal_rate'],'code' => '水晶'], //水晶
        1 => ['id' => 2, 'prize' => $tConfig['reward_item_name_0'], 'v' => $tConfig['reward_item_success_rate_0'],'code' => $tConfig['reward_item_code_0']],
        2 => ['id' => 3, 'prize' => $tConfig['reward_item_name_1'], 'v' => $tConfig['reward_item_success_rate_1'],'code' => $tConfig['reward_item_code_1']],
        3 => ['id' => 4, 'prize' => $tConfig['reward_item_name_2'], 'v' => $tConfig['reward_item_success_rate_2'],'code' => $tConfig['reward_item_code_2']],
        4 => ['id' => 5, 'prize' => $tConfig['reward_item_name_3'], 'v' => $tConfig['reward_item_success_rate_3'],'code' => $tConfig['reward_item_code_3']],
        5 => ['id' => 6, 'prize' => $tConfig['reward_item_name_4'], 'v' => $tConfig['reward_item_success_rate_4'],'code' => $tConfig['reward_item_code_4']],
        6 => ['id' => 7, 'prize' => $tConfig['reward_item_name_5'], 'v' => $tConfig['reward_item_success_rate_5'],'code' => $tConfig['reward_item_code_5']],
        7 => ['id' => 8, 'prize' => $tConfig['reward_item_name_6'], 'v' => $tConfig['reward_item_success_rate_6'],'code' => $tConfig['reward_item_code_6']],
        8 => ['id' => 9, 'prize' => $tConfig['reward_item_name_7'], 'v' => $tConfig['reward_item_success_rate_7'],'code' => $tConfig['reward_item_code_7']],
        9 => ['id' => 10, 'prize' => $tConfig['reward_item_name_8'], 'v' => $tConfig['reward_item_success_rate_8'],'code' => $tConfig['reward_item_code_8']],
        10 => ['id' => 11, 'prize' => $tConfig['reward_item_name_9'], 'v' => $tConfig['reward_item_success_rate_9'],'code' => $tConfig['reward_item_code_9']],
        11 => ['id' => 12, 'prize' => $tConfig['reward_item_name_10'], 'v' => $tConfig['reward_item_success_rate_10'],'code' => $tConfig['reward_item_code_10']],
        12 => ['id' => 13, 'prize' => $tConfig['reward_item_name_11'], 'v' => $tConfig['reward_item_success_rate_11'],'code' => $tConfig['reward_item_code_11']],
        13 => ['id' => 14, 'prize' => $tConfig['reward_item_name_12'], 'v' => $tConfig['reward_item_success_rate_12'],'code' => $tConfig['reward_item_code_12']],
    ];

    foreach ($prize_arr as $k => $v) {
        $arr[$v['id']] = $v['v'];
    }
    if (empty($arr)) return;

    function getRand($proArr)
    {
        $data = '';
        $proSum = array_sum($proArr); //概率数组的总概率精度

        foreach ($proArr as $k => $v) { //概率数组循环
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $v) {
                $data = $k;
                break;
            } else {
                $proSum -= $v;
            }
        }
        unset($proArr);
        return $data;
    }

    #幸运值
    $luckNumber = $lottery->getCountLuckLotteryLog($_SESSION['username']);
    if($many){
        $prize_id = [];
        $prize_site = [];
        $prizeName = ':<br>';
        for ($i=0;$i<$manyNumber;$i++){
            $prize_id[$i] = getRand($arr); //根据概率获取奖品ID,1为水晶
            $luckNumber += 1;
            if($luckNumber >= 100){
                $luckNumber = $luckNumber % 100;
                $prize_id[$i] = 1; #必出水晶
            }
            foreach ($prize_arr as $k => $v){
                if ($v['id'] == $prize_id[$i]) {
                    $prize_site[$i] = $k;
                }
            }

            #中奖项
            $res[$i] = $prize_arr[$prize_id[$i] - 1];
            #生成记录
            if(!$lottery->setLotteryLog($_SESSION['group'],$_SESSION['username'],$res[$i]['code'])) return;

            #水晶数量
            $Crystal = $lottery->getCountLotteryLog($_SESSION['username']);
            $prizeName.= ($i+1).':['.$res[$i]['prize'].']<br>';
            #发送游戏公告
            $MessageGames = "恭喜玩家[".$CharacterName."]".$manyNumber."连抽获得[".$res[$i]['prize']."]！";
        }

        $data = [
            'prize_name'    => $prizeName,  #中奖物品名
            'prize_site'    => $prize_site[0],                  #前端奖项从-1开始
            'prize_id'      => $prize_id[0],                        #中奖ID
            'record_money'  => number_format($Crystal),         #水晶余额
            'credit_money'  => number_format($configCredits),   #货币余额
            'luck_money'    => $luckNumber,                     #幸运值
            'Crystal'       => $Crystal,
            'title'         => '在线夺宝',
            'message'       => $MessageGames,
        ];

    }else{
        $prize_id = getRand($arr); //根据概率获取奖品ID,1为水晶
        $luckNumber += 1;
        if($luckNumber >= 100){
            $luckNumber = $luckNumber % 100;
            $prize_id = 1; #必出水晶
        }
        foreach ($prize_arr as $k => $v) if ($v['id'] == $prize_id) {
            $prize_site = $k;
        }
        #中奖项
        $res = $prize_arr[$prize_id - 1];
        #生成记录
        if(!$lottery->setLotteryLog($_SESSION['group'],$_SESSION['username'],$res['code'])) return;

        #水晶数量
        $Crystal = $lottery->getCountLotteryLog($_SESSION['username']);
        #发送游戏公告
        $MessageGames = "恭喜玩家[".$CharacterName."]在线抽奖获得[".$res['prize']."]！";

        #return
        $data = [
            'prize_name'    => $res['prize'],  #中奖物品名
            'prize_site'    => $prize_site,    #前端奖项从-1开始
            'prize_id'      => $prize_id,      #中奖ID
            'record_money'  => number_format($Crystal),       #水晶余额
            'credit_money'  => number_format($configCredits),       #货币余额
            'luck_money'    => $luckNumber,       #幸运值
            'Crystal'       => $Crystal,
            'title'         => '在线夺宝',
            'message'       => $MessageGames,
        ];
    }

    echo json_encode($data);

}catch (Exception $exception){
    echo json_encode($exception->getMessage());
}