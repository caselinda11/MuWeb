<?php
define('access', 'api');

#调试模式
$bug = 0; #0关闭,1开启
#===========================================================
#下面是青蛙(双)赠送的
$POINT[2]   = 1000;     #满额赠送限制
$SED_JF[2]  = 0.1;      #赠送积分比例 0=不赠送,(公式: 赠送点数=充值金额*赠送比例)
$Field[2]   = "YB";     #赠送元宝字段类型(必须是账号表中的字段)
$SED_YB[2]  = 0.2;      #赠送元宝比例 0=不赠送,(公式: 赠送点数=充值金额*赠送比例)

#充值送VIP
$VIP[2]         = 1;		#开关0/1
$WHERE_1[2]     = 2000;	    #第一个一次性充值条件金额
$SEND_VIP_1[2]  = 2;		#赠送VIP等级
$WHERE_2[2]     = 5000;	    #第二个一次性充值条件金额
$SEND_VIP_2[2]  = 3;		#赠送VIP等级
$SEND_GAMES[2] = 1;         #发送游戏公告
#-----------------------------------------------------------
#下面是蛤蟆(单)赠送的
$POINT[1]   = 1000;     #满额赠送限制
$SED_JF[1]  = 0.1;      #赠送积分比例 0=不赠送,(公式: 赠送点数=充值金额*赠送比例)
$Field[1]   = "YB";     #赠送元宝字段类型(必须是账号表中的字段)
$SED_YB[1]  = 0.0;      #赠送元宝比例 0=不赠送,(公式: 赠送点数=充值金额*赠送比例)

#充值送VIP
$VIP[1]         = 0;		#开关0/1
$WHERE_1[1]     = 2000;	    #第一个一次性充值条件金额
$SEND_VIP_1[1]  = 2;		#赠送VIP等级
$WHERE_2[1]     = 5000;	    #第二个一次性充值条件金额
$SEND_VIP_2[1]  = 3;		#赠送VIP等级
$SEND_GAMES[1] = 1;         #发送游戏公告
//如果赠送额度有小数点采用四舍五入法则
#===========================================================
try {
    include('../../includes/Init.php');
#----------------------------------------验证器------------------------------------------

    /*防止恶意查询*/
    if ($ip = Validator::getIP()) {
        if ($_GET) Validator::filter('GET', $_GET, "'|(and|or)\\b.+?(>|<|=|in|like)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)");
        if ($_POST) Validator::filter('POST', $_POST, "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)");
        if ($_COOKIE) Validator::filter('COOKIE', $_COOKIE, "\\b(and|or)\\b.{1,6}?(=|>|<|\\bin\\b|\\blike\\b)|\\/\\*.+?\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT|UPDATE.+?SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE).+?FROM|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)");
    } else exit;

#----------------------------------------配置文件------------------------------------------

//所有的配置都在这里增删改
    $s_IP = "106.15.88.81";           #限制来访者IP (106.15.88.81)官方
    $Pid = 43;                     #商户Id
    #配置文件地址：http://mupay.1000uc.com/user/user_xml/43.xml
    #用户名自定义KEY 签名用
    $S_Key = 'APm!PDw3%ed#1TMd28AjFaJBmeOrdZ';
    #用户名长度规则 默认4~10
    $length_min = config('username_min_len');
    $length_max = config('username_max_len');
    #定义Paylog表所在数据库连接的 源地址 用户名 密码 目标数据库名称 记录订单信息方便查询和防止重复提交
    $db_order = ["db_source", "db_user", 'db_pass', "db_name"];

//定义822区 如果该区服数据库不在本地 注意telnet<<<<<<<<<<<
    #添加所有区服DB信息 822
    #  对应 http://pay.qiquanwl.com/user/user_xml/43.xml 中  单职业5倍
    #  Area->Id 每个区服都定义一下 以及以下三个要素
    $DB_AREA = ["db_source", "db_user", 'db_pass', "db_name"];
    $DB_TABLE = "MEMB_INFO";                #822区对应账号表名称比如这个MEMB_INFO
    $DB_ACCOUNT = "memb___id";              #822区对应账号表用户名字段 比如这个memb___id
    $DB_ID_CARD = "sno__numb";              #822区对应账号表身份证字段 比如这个addr_deta
//定义822区 end

//定义游戏币 102 对应 http://pay.qiquanwl.com/user/user_xml/43.xml 中  积分 Type->Id
    $db_point_tab = "MEMB_INFO";             #对应账号表名称比如这个MEMB_INFO
    $db_point_acc = "memb___id";             #对应账号表用户名字段比如这个memb___id
    $db_point_val = "JF";                    #对应账号表表示积分的字段比如这个JF
//end
#----------------------------------------INC------------------------------------------
    if($bug) $s_IP = "127.0.0.1";           #调试
    if ($ip != $s_IP) exit;             #拒绝来访服务器IP地址以外的IP
    $PST = $_POST;                      #切换$_GET用来调试方便
    if ($PST['Submit'] != 'Submit') exit;
    $post_username = trim($PST['User']);    #玩家账号
    $post_Pid = trim($PST['Pid']);          #商户ID
    $post_Area = trim($PST['Area']);        #区服ID
    $post_S = trim($PST['S']);              #MD5加密
    $post_OrdID = trim($PST['Ordid']);      #订单号
    $post_TypeId = trim($PST['TypeId']);    #货币编号ID
    $post_All = trim($PST['All']);          #支付总金额
    $post_Point = trim($PST['Point']);      #获得的货币点数
    $common = new common();
    if (!check_value($post_username)) exit('1005');
    if (!Validator::UsernameLength($post_username)) exit('1005');
    if (!Validator::AlphaUsername($post_username)) exit('1005');
    global $serverGrouping;
    if (!Validator::UnsignedNumber($post_Area)) exit('1004');
    $group = getGroupIDForServerCode($post_Area);
    if (!check_value($group)) exit('1004');
    $muOnline = Connection::Database("Me_MuOnline", $group); #游戏数据库
    $web = Connection::Database("Web"); #网站数据库
    #奇数or偶数
    $serverType = ($post_Area%2) ? 1 : 2;

    if (!$post_OrdID) { #没有订单号订单号#验证身份证入口
        if (!Validator::UsernameLength($post_username)) exit('1005');
        if (!Validator::AlphaUsername($post_username)) exit('1005');
        if ($Pid != $post_Pid) exit('1002');
        $DB_AREA_NAME = $DB_AREA;
        if (!$DB_AREA_NAME) exit('1003');

        if (!$DB_TABLE || !$DB_ACCOUNT || !$DB_ID_CARD) exit('1004');
        if($bug) debug(md5($post_Pid . '|' . $post_Area . '|' . $post_username . '|' . $S_Key));
        if (strtolower($post_S) != strtolower(md5($post_Pid . '|' . $post_Area . '|' . $post_username . '|' . $S_Key))) exit('1001');//签名错误
        //拼接sql 用户查询出账号表中等于该用户名对应的身份证号
        $sql = "select " . $DB_ID_CARD . " from " . $DB_TABLE . " where " . $DB_ACCOUNT . " = '" . $post_username . "'";
        $rs = $muOnline->query_fetch_single($sql);

        //debug($rs); //调试看看输出内容再确定
        if (!$rs[$DB_ID_CARD]) {
            exit('1006');
        } else {
            exit($rs[$DB_ID_CARD]);
        }

    } else {
        #有订单号[Ordid]的入口表示入点
        if (!Validator::UsernameLength($post_username)) exit;
        if (!Validator::AlphaUsername($post_username)) exit;
        if ($Pid != $post_Pid) exit('2002');
        $DB_AREA_NAME = $DB_AREA;
        if (!$DB_AREA_NAME) exit('2003');

        if (!$db_point_tab || !$db_point_acc || !$db_point_val) exit('2004');
        if (!$db_order) exit('2008');
        #验证签名
        if($bug) debug(md5($post_OrdID . '|' . $post_Pid . '|' . $post_Area . '|' . $post_TypeId . '|' . $post_username . '|' . $post_All . '|' . $post_Point . '|' . $S_Key));
        if (strtolower($post_S) != strtolower(md5($post_OrdID . '|' . $post_Pid . '|' . $post_Area . '|' . $post_TypeId . '|' . $post_username . '|' . $post_All . '|' . $post_Point . '|' . $S_Key))) exit('2001');//签名错误
        $sql = "select [memb___id] from [PayLog] where [Ordid] = '".$post_OrdID."'";
        #debug($sql);
        $rs = $web->query_fetch_single($sql);
        #已有该订单号，避免重复
        if ($rs['memb___id']) {
            exit('2005');
        }else{

            try{
                $muOnline->beginTransaction();
                $web->beginTransaction();
                #操作货币
                $sql1 = "UPDATE ".$db_point_tab." SET ".$db_point_val." = ".$db_point_val." + ? WHERE ".$db_point_acc." = ?";
                $muOnline->query($sql1,[$post_Point,$post_username]);
                $sendJf = 0;
                $sendYb = 0;
                if($post_All >= $POINT[$serverType]){
                    $sendJf = ceil($post_Point*$SED_JF[$serverType]);
                    $sendYb = ceil($post_Point*$SED_YB[$serverType]);
                    $sql2 = "UPDATE ".$db_point_tab." SET ".$db_point_val." = ".$db_point_val." + ?,".$Field[$serverType]." = ".$Field[$serverType]." + ? WHERE ".$db_point_acc." = ?";
                    $muOnline->query($sql2,[$sendJf,$sendYb,$post_username]);
                    $send = [$post_username,$post_Area,$post_OrdID,date('Y-m-d H:i:s'),$post_TypeId,$post_All,$post_Point];
                }

                #日志
                $sql0 = "INSERT INTO [PayLog] ([memb___id],[Area],[Ordid],[TransDate],[TypeId],[Money],[Point],[Send_jf],[Send_yb])VALUES(?,?,?,?,?,?,?,?,?)";
                $query = [$post_username,$post_Area,$post_OrdID,date('Y-m-d H:i:s'),$post_TypeId,$post_All,$post_Point,$sendJf,$sendYb];
                $web->query($sql0,$query);

                #赠送VIP
                if($VIP[$serverType]){
                    $VIP_LEVEL = 0;
                    $NOW_VIP = $muOnline->query_fetch_single("SELECT [vip] FROM ".$db_point_tab." WHERE ".$db_point_acc." = ?",[$post_username]);
                    if(is_array($NOW_VIP)) $VIP_LEVEL = $NOW_VIP['vip'];
                    if($post_All >= $WHERE_1[$serverType]){
                        if($VIP_LEVEL < $SEND_VIP_1[$serverType]){
                            $VIP_LEVEL = $SEND_VIP_1[$serverType];
                        }
                        if($post_All >= $WHERE_2[$serverType]){
                            if($VIP_LEVEL < $SEND_VIP_2[$serverType]){
                                $VIP_LEVEL = $SEND_VIP_2[$serverType];
                            }
                        }

                        $muOnline->query("UPDATE ".$db_point_tab." SET [vip] = ? WHERE ".$db_point_acc." = ?",[$VIP_LEVEL,$post_username]);
                    }
                }
                if($SEND_GAMES[$serverType]){
                    $char = new Character();
                    $last_char_name = $char->getAccountCharacterIDC($group,$post_username);
                    $account = $last_char_name ? $last_char_name : $post_username;
                    global $serverGrouping;
                    if($serverGrouping[$group]['SERVER_JS_POST']){
                        sendMessageGames("充值公告","玩家【".$account."】充值人民币".$post_All."元成功！",$serverGrouping[$group]['SERVER_IP'],$serverGrouping[$group]['SERVER_JS_POST']);
                    }
                }
                $muOnline->commit();
                $web->commit();
                exit('1000');//充值成功成功
            }catch (Exception $exception){
                $muOnline->rollBack();
                $web->rollBack();
                exit('2007');
            }
        }
    }
}catch (Exception $exception){
    exit('1004');
}
