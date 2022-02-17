<?php
/**
 * 反黑客系统设置
 */
// 初始化:
//     固定:
$crlf=chr(13).chr(10);  #换行符;
//$iTime秒内可以访问$iMaxVisit次,超过则等待$iPenalty秒
$iTime=3;       # 读秒时间
$iMaxVisit=10;  # 最大访问次数 
$iPenalty= 15;  # 等待的时间
# 警告讯息:
$message='刷新过快,请在%s秒后再尝试访问';
$ipLogDir = dirname(__DIR__).'/logs/';

$log = 1; #是否生成访问限制日志(用处不大,可以不开)

//---------------------- 初始化结束 ---------------------------------------

# 获取文件时间:
$ipFile = $_SERVER["REMOTE_ADDR"].".log";  // -3表示4096个可能的文件
$oldTime = 0;
if (file_exists($ipLogDir.$ipFile)) $oldTime = filemtime($ipLogDir.$ipFile);
#更新时间:
$time=time();
if ($oldTime<$time) $oldTime=$time;
$newTime=$oldTime+$iTime;

# 检查是人还是机器人:
if ($newTime>=$time+$iTime*$iMaxVisit)
{
    $waitTime = $iPenalty+1;
    # 阻止访客:
    touch($ipLogDir.$ipFile,$time+$iTime*($iMaxVisit-1)+$iPenalty);
    header("HTTP/1.0 503 Service Temporarily Unavailable");
    header("Connection: close");
    header("Content-Type: text/html");
    echo '<html lang="zh-cn"><head>';
        echo '<title>超载禁告</title>';
        echo '<meta http-equiv="REFRESH" content="'.$waitTime.'; url=/"></head><body>';
    echo '<style type="text/css">
    @import url("https://fonts.googleapis.com/css?family=Roboto:100,300,400&display=swap");* {
        box-sizing: border-box;
    }

    *::before, *::after {
        box-sizing: border-box;
    }

    body {
        font-family: \'Roboto\', sans-serif;
        font-size: 1rem;
        line-height: 1.5;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        min-height: 100vh;
        background: #fff;
        overflow: hidden;
    }

    #container {
        position: relative;
        -webkit-transform: scale(0.725);
        transform: scale(0.725);
    }

    .divider {
        position: absolute;
        z-index: 2;
        top: 65px;
        left: 200px;
        width: 50px;
        height: 15px;
        background: #fff;
    }

    .loading-text {
        position: relative;
        font-size: 3.75rem;
        font-weight: 300;
        white-space: nowrap;
    }

    .loading-text::before {
        position: absolute;
        content: "";
        z-index: 1;
        top: 40px;
        left: 115px;
        width: 6px;
        height: 6px;
        background: #000;
        border-radius: 50%;
        -webkit-animation: dotMove 1800ms cubic-bezier(0.25, 0.25, 0.75, 0.75) infinite;
        animation: dotMove 1800ms cubic-bezier(0.25, 0.25, 0.75, 0.75) infinite;
    }

    .loading-text .letter {
        display: inline-block;
        position: relative;
        color: #000;
        letter-spacing: 8px;
    }

    .loading-text .letter:nth-child(1) {
        -webkit-transform-origin: 100% 70%;
        transform-origin: 100% 70%;
        -webkit-transform: scale(1, 1.275);
        transform: scale(1, 1.275);
    }

    .loading-text .letter:nth-child(1)::before {
        position: absolute;
        content: "";
        top: 22px;
        left: 0;
        width: 14px;
        height: 36px;
        background: #fff;
        -webkit-transform-origin: 100% 0;
        transform-origin: 100% 0;
        -webkit-animation: lineStretch 1800ms cubic-bezier(0.25, 0.25, 0.75, 0.75) infinite;
        animation: lineStretch 1800ms cubic-bezier(0.25, 0.25, 0.75, 0.75) infinite;
    }

    .loading-text .letter:nth-child(5) {
        -webkit-transform-origin: 100% 70%;
        transform-origin: 100% 70%;
        -webkit-animation: letterStretch 1800ms cubic-bezier(0.25, 0.23, 0.73, 0.75) infinite;
        animation: letterStretch 1800ms cubic-bezier(0.25, 0.23, 0.73, 0.75) infinite;
    }

    .loading-text .letter:nth-child(5)::before {
        position: absolute;
        content: "";
        top: 15px;
        left: 2px;
        width: 9px;
        height: 15px;
        background: #fff;
    }

    @-webkit-keyframes dotMove {
        0%, 100% {
            -webkit-transform: rotate(180deg) translate(-110px, -10px) rotate(-180deg);
            transform: rotate(180deg) translate(-110px, -10px) rotate(-180deg);
        }

        50% {
            -webkit-transform: rotate(0deg) translate(-111px, 10px) rotate(0deg);
            transform: rotate(0deg) translate(-111px, 10px) rotate(0deg);
        }
    }

    @keyframes dotMove {
        0%, 100% {
            -webkit-transform: rotate(180deg) translate(-110px, -10px) rotate(-180deg);
            transform: rotate(180deg) translate(-110px, -10px) rotate(-180deg);
        }

        50% {
            -webkit-transform: rotate(0deg) translate(-111px, 10px) rotate(0deg);
            transform: rotate(0deg) translate(-111px, 10px) rotate(0deg);
        }
    }

    @-webkit-keyframes letterStretch {
        0%, 100% {
            -webkit-transform: scale(1, 0.35);
            transform: scale(1, 0.35);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }

        8%, 28% {
            -webkit-transform: scale(1, 2.125);
            transform: scale(1, 2.125);
            -webkit-transform-origin: 100% 67%;
            transform-origin: 100% 67%;
        }

        37% {
            -webkit-transform: scale(1, 0.875);
            transform: scale(1, 0.875);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }

        46% {
            -webkit-transform: scale(1, 1.03);
            transform: scale(1, 1.03);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }

        50%, 97% {
            -webkit-transform: scale(1);
            transform: scale(1);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }
    }

    @keyframes letterStretch {
        0%, 100% {
            -webkit-transform: scale(1, 0.35);
            transform: scale(1, 0.35);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }

        8%, 28% {
            -webkit-transform: scale(1, 2.125);
            transform: scale(1, 2.125);
            -webkit-transform-origin: 100% 67%;
            transform-origin: 100% 67%;
        }

        37% {
            -webkit-transform: scale(1, 0.875);
            transform: scale(1, 0.875);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }

        46% {
            -webkit-transform: scale(1, 1.03);
            transform: scale(1, 1.03);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }

        50%, 97% {
            -webkit-transform: scale(1);
            transform: scale(1);
            -webkit-transform-origin: 100% 75%;
            transform-origin: 100% 75%;
        }
    }

    @-webkit-keyframes lineStretch {
        0%, 45%, 70%, 100% {
            -webkit-transform: scaleY(0.125);
            transform: scaleY(0.125);
        }

        49% {
            -webkit-transform: scaleY(0.75);
            transform: scaleY(0.75);
        }

        50% {
            -webkit-transform: scaleY(0.875);
            transform: scaleY(0.875);
        }

        53% {
            -webkit-transform: scaleY(0.5);
            transform: scaleY(0.5);
        }

        60% {
            -webkit-transform: scaleY(0);
            transform: scaleY(0);
        }

        68% {
            -webkit-transform: scaleY(0.18);
            transform: scaleY(0.18);
        }
    }

    @keyframes lineStretch {
        0%, 45%, 70%, 100% {
            -webkit-transform: scaleY(0.125);
            transform: scaleY(0.125);
        }

        49% {
            -webkit-transform: scaleY(0.75);
            transform: scaleY(0.75);
        }

        50% {
            -webkit-transform: scaleY(0.875);
            transform: scaleY(0.875);
        }

        53% {
            -webkit-transform: scaleY(0.5);
            transform: scaleY(0.5);
        }

        60% {
            -webkit-transform: scaleY(0);
            transform: scaleY(0);
        }

        68% {
            -webkit-transform: scaleY(0.18);
            transform: scaleY(0.18);
        }
    }

    @media (min-width: 48rem) {
        #container {
            -webkit-transform: scale(0.725);
            transform: scale(0.725);
        }
    }

    @media (min-width: 62rem) {
        #container {
            -webkit-transform: scale(0.85);
            transform: scale(0.85);
        }
    }
</style>';
    echo '<div id="container">
        <div class="divider"></div>
        <div class="loading-text">
        <span class="letter">L</span>
        <span class="letter">o</span>
        <span class="letter">a</span>
        <span class="letter">d</span>
        <span class="letter">i</span>
        <span class="letter">n</span>
        <span class="letter">g</span>
        </div>
    <center><b style="font-size: 22px;">'.@vsprintf($message,[$iPenalty]).'</b></center></div></body></html>'.$crlf;
    //---------------------------------------------------------
    # 日志:
    if($log){
        $fp=@fopen($ipLogDir.$ipFile,"a");
        if ($fp!==FALSE)
        {
            $useragent='<unknown user agent>';
            if (isset($_SERVER["HTTP_USER_AGENT"])) $useragent=$_SERVER["HTTP_USER_AGENT"];
            @fputs($fp,'['.date("Y-m-d H:i:s").']['.$_SERVER["REMOTE_ADDR"].']['.$useragent.']'.$crlf);
        }
        @fclose($fp);
    }
    //---------------------------------------------------------
    exit();
}
//
#     修改文件时间:
touch($ipLogDir.$ipFile,$newTime);


