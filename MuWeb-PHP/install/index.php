<?php
/**
 * 安装程序
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>

<html>
<head>
    <title>奇迹网站系统 - 服务器环境检查</title>
    <style>
        BODY {font-family: Arial, Helvetica, sans-serif; font-size: 12px;}
        .pass {color:green;font-weight:bold;font-size: 19px;}
        .fail {color:red;font-weight:bold;}
        li{font-size:16px;}
        ul{font-size:16px;}
        .btn{
            color: #fff;
            background-color: #4CAF50;
            border-color: #2c3e50;
            padding: 6px 70px;
            font-size: 27px;
            line-height: 1.42857143;
            border-radius: 3px;
            text-decoration: none;
        }
    </style>
</head>

<body style="text-align:center;">
<br>
<h1>奇迹网站系统 - 服务器环境检查</h1>
<hr>
<?php

$required_php_version = "7.0";
$required_loader = "10.2";
$results = [];

// PHP Version
if (version_compare($required_php_version, PHP_VERSION, ">")) {
    $results[] = "<li>奇迹网站系统需要PHP ".$required_php_version." 版本或以上，你的服务器PHP版本是".PHP_VERSION."</li>";
    $error = true;
}

//ionCube Loader Version
//if (function_exists('ioncube_loader_iversion')) {
//    $current_loader = ioncube_loader_version();
//    if (version_compare($required_loader, $current_loader, ">")) {
//        $results[] = "<li>奇迹网站系统需要装载ionCube ".$required_loader." 版本或以上，你的服务器ionCube版本是".$current_loader."</li>";
//        $error = true;
//    }
//} else {
//    $results[] = "<li>《'ionCube'》需要装载ionCube，请启用它。</li>";
//    $error = true;
//}

// Extension: curl
if(!extension_loaded("curl")) {
    $results[] = "<li>《'cURL'》PHP扩展是必需的，请先在php扩展中用它。</li>";
    $error = true;
}

// Extension: json
if(!extension_loaded("json")) {
    $results[] = "<li>《'Json'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}

// Extension: Sessions
if(!extension_loaded("session")) {
    $results[] = "<li>《'Session'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}

// Extension: Date
if(!extension_loaded("date")) {
    $results[] = "<li>《 'Date'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}
// Extension: hash
if(!extension_loaded("hash")) {
    $results[] = "<li>《 'hash'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}

// Extension: XML
if(!extension_loaded("xml")) {
    $results[] = "<li>《 'XML'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}

// Extension: SimpleXML
if(!extension_loaded("simplexml")) {
    $results[] = "<li>《'SimpleXML'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}

// Extension: PDO
if(!extension_loaded("pdo")) {
    $results[] = "<li>《'PDO'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
// Extension: PDO_MYSQL Windows Hosting
    if(!extension_loaded("pdo_sqlsrv")) {
        $results[] = "<li>《'pdo_sqlsrv'》PHP扩展是必需的，如果您使用的是Windows主机，请先在php扩展中启用它！</li>";
        $error = true;
    }
} else {
// Extension: pdo_dblib / Linux Hosting
    if(!extension_loaded("pdo_dblib")) {
        $results[] = "<li>《'pdo_dblib'》PHP扩展是必需的，如果您使用的是Linux主机，请先在php扩展中启用它！</li>";
        $error = true;
    }
}
// Extension: sockets
if(!extension_loaded("sockets")) {
    $results[] = "<li>《'sockets'》PHP扩展是必需的，请先在php扩展中启动它！</li>";
    $error = true;
}

if (isset($error)) {
    echo "<font size='5'>以下问题阻止您运行奇迹网站系统:</font><span class='fail'><ul>";
    foreach ($results as $message) {
        echo $message;
    }
    echo "</ul></span><br /><font size='5'>请更正这些问题，然后重试。</font><br />";
} else {
    if (isset($warning)) {
        echo "奇迹网站系统建议安装以下扩展：<span class='fail'><ul>";
        foreach ($results as $message) {
            echo $message;
        }
    }
    header('Location: install.php');
    die();
}

?>
</body>
</html>