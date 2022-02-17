<?php
/**
 * 事件API
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
define('access', 'api');

include('../includes/Init.php');
/*防止恶意查询*/
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);

# 时区
date_default_timezone_set('Asia/Shanghai');

$eventTimes = json_decode(file_get_contents('events.json'),true);


// 不要动下面所有数据
function getEventNextTime($eventSchedule) {
    $currentTime = date("H:i");
    foreach($eventSchedule as $time) {
        if($time > $currentTime) {
            return date("Y-m-d ") . $time;
        }
    }
    $tomorrow = date('d', strtotime('tomorrow'));
    return date("Y-m-$tomorrow ") . $eventSchedule[0];
}

function getEventPreviousTime($eventSchedule) {
    $currentTime = date("H:i");
    foreach($eventSchedule as $key => $time) {
        if($time > $currentTime) {
            $last = $key-1;
            if($last < 0) {
                $yesterday = date('d', strtotime('yesterday'));
                return date("Y-m-$yesterday ") . end($eventSchedule);
            }
            return date("Y-m-d ") . $eventSchedule[$last];
        }
    }
    return date("Y-m-d ") . end($eventSchedule);
}

function getWeeklyEventNextTime($day, $time) {
    $currentDay = strtolower(date("l"));
    $currentTime = date("H:i");
    if($currentDay == strtolower($day)) {
        if($currentTime < $time) {
            return date("Y-m-d H:i", strtotime('today '.$time.''));
        }
    }
    return date("Y-m-d H:i", strtotime('next '.$day.' '.$time.''));
}

function getWeeklyEventPreviousTime($day, $time) {
    $currentDay = strtolower(date("l"));
    $currentTime = date("H:i");
    if($currentDay == strtolower($day)) {
        if($currentTime > $time) {
            return date("Y-m-d H:i", strtotime('today '.$time.''));
        }
    }
    return date("Y-m-d H:i", strtotime('last '.$day.' '.$time.''));
}

$weekArray = ["周日","周一","周二","周三","周四","周五","周六"];

foreach($eventTimes as $eventId => $event) {
    $active = 0;
    $open = 0;
    if(!array_key_exists('day', $event)) {
        $lastTime = getEventPreviousTime($event['schedule']);
        $nextTime = getEventNextTime($event['schedule']);
    } else {
        $lastTime = getWeeklyEventPreviousTime($event['day'], $event['time']);
        $nextTime = getWeeklyEventNextTime($event['day'], $event['time']);
    }
     //先定义一个数组
    $nextTimeF = '['.$weekArray[date("w",strtotime($nextTime))].' - '.date("G:i", strtotime($nextTime)).']';
    $offset = strtotime($nextTime)-strtotime($lastTime);
    $timeLeft = strtotime($nextTime)-time();

    $result[$eventId] = [
        'event'     => $event['name'],
        'opentime'  => $event['opentime'],
        'duration'  => $event['duration'],
        'last'      => $lastTime,
        'next'      => $nextTime,
        'nextF'     => $nextTimeF,
        'offset'    => $offset,
        'timeleft'  => $timeLeft,
    ];
}

// 0xcb88617a
if(isset($_GET['event'])) {
    if(array_key_exists($_GET['event'], $result)) {
        $result = $result[$_GET['event']];
    }
}

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);