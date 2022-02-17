<?php
/**
 * 分区管理
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group float-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item">
                            <a href="<?=admincp_base()?>">官方主页</a>
                        </li>
                        <li class="breadcrumb-item active">网站设置</li>
                        <li class="breadcrumb-item active">分区管理</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    分区管理</h4>
            </div>
        </div>

    </div>
<?php
try {
    # 加载分区配置文件
    $newCfg = loadConfig('server');
    if(check_value($_POST['submit'])){
        switch ($_POST['submit']){
            case "combine": #合区
                try {
                    if(!is_array($newCfg)) throw new Exception('服务器分区配置为空');
                    if(!check_value($_POST['new_group'])) throw new Exception('ID无效，禁止非法操作！');
                    if(!check_value($_POST['old_group'])) throw new Exception('ID无效，禁止非法操作！');
                    if($_POST['new_group'] == $_POST['old_group']) throw new Exception('仅剩一个大区无法使用合区功能。');
                    $Group = getGroupIDForServerCode($_POST['new_group']);
                    $oldGroup = getGroupIDForServerCode($_POST['old_group']);
                    if(!array_key_exists($Group, $newCfg)) throw new Exception('ID无效，禁止非法操作！');
                    if(!array_key_exists($oldGroup, $newCfg)) throw new Exception('ID无效，禁止非法操作！');
                    #操作数据库
                    $muOnline = Connection::Database("Me_MuOnline",$Group);
                    $Web = Connection::Database("Web");
                    $webConfig = loadConfig();
                    $combineQuery = $Web->query_fetch("SELECT [Query] FROM [".X_TEAM_COMBINE_SERVER."]");
//                    if(!is_array($combineQuery))  throw new Exception('您的版本不支持此项功能，请联系作者升级。');
                    if($_POST['old_group'] == 'all') {
                        try {
                            $muOnline->beginTransaction();
                            $Web->beginTransaction();
                            $muOnline->query("UPDATE [MEMB_INFO] SET [servercode] = ?", [$_POST['new_group']]);
                            $Web->query("UPDATE [X_TEAM_ACCOUNT] SET [servercode] = ?", [$_POST['new_group']]);
                            $Web->query("UPDATE [X_TEAM_BANS] SET [servercode] = ?", [$_POST['new_group']]);
                            $Web->query("UPDATE [X_TEAM_BAN_LOG] SET [servercode] = ?", [$_POST['new_group']]);
                            $Web->query("UPDATE [X_TEAM_VOTE_LOGS] SET [servercode] = ?", [$_POST['new_group']]);
                            $Web->query("UPDATE [X_TEAM_TOAST] SET [servercode] = ?", [$_POST['new_group']]);
                            if(is_array($combineQuery)){
                                foreach ($combineQuery as $query) {
                                    $Web->query("IF EXISTS(Select 1 From Sysobjects Where Name='" . $query['Query'] . "') begin UPDATE [" . $query['Query'] . "] SET [servercode] = ? end", [$_POST['new_group']]);
                                }
                            }
                            $muOnline->commit();
                            $Web->commit();
                        } catch (Exception $ex) {
                            $muOnline->rollBack();
                            $Web->rollBack();
                            message('error', $ex->getMessage());
                        }
                        #保留分区
                        $keepConfig[] = $newCfg[$_POST['new_group']];
                        # 转码
                        $Json = json_encode($keepConfig, JSON_PRETTY_PRINT);
                        # 保存更改
                        $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'server.json', 'w+');
                        if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题.');
                        fwrite($cfgFile, $Json);
                        fclose($cfgFile);
                        message('success', '所有大区合并成功，请勿做其他操作，1秒后刷新此页面。');
                        redirect(2,'admincp/?module=Settings_Group',1);
                    }else {
                        try {
                            $muOnline->beginTransaction();
                            $Web->beginTransaction();
                            $muOnline->query("UPDATE [MEMB_INFO] SET [servercode] = ? WHERE [servercode] = ?", [$_POST['new_group'], $_POST['old_group']]);

                            $Web->query("UPDATE [X_TEAM_ACCOUNT] SET [servercode] = ? WHERE [servercode] = ?", [$_POST['new_group'], $_POST['old_group']]);
                            $Web->query("UPDATE [X_TEAM_BANS] SET [servercode] = ? WHERE [servercode] = ?", [$_POST['new_group'], $_POST['old_group']]);
                            $Web->query("UPDATE [X_TEAM_BAN_LOG] SET [servercode] = ? WHERE [servercode] = ?", [$_POST['new_group'], $_POST['old_group']]);
                            $Web->query("UPDATE [X_TEAM_VOTE_LOGS] SET [servercode] = ? WHERE [servercode] = ?", [$_POST['new_group'], $_POST['old_group']]);
                            $Web->query("UPDATE [X_TEAM_TOAST] SET [servercode] = ? WHERE [servercode] = ?", [$_POST['new_group'], $_POST['old_group']]);
                            if(is_array($combineQuery)) {
                                foreach ($combineQuery as $query) {
                                    $Web->query("IF EXISTS(Select 1 From Sysobjects Where Name='" . $query['Query'] . "') begin UPDATE [" . $query['Query'] . "] SET [servercode] = ? WHERE [servercode] = ? end", [$_POST['new_group'], $_POST['old_group']]);
                                }
                            }
                            $muOnline->commit();
                            $Web->commit();
                        } catch (Exception $exception) {
                            $muOnline->rollBack();
                            $Web->rollBack();
                            message('error', $exception->getMessage());
                        }
                        #删除分区
                        unset($newCfg[$oldGroup]);
                        $delConfig = array_values($newCfg);
                        # 转码
                        $Json = json_encode($delConfig, JSON_PRETTY_PRINT);
                        # 保存更改
                        $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'server.json', 'w+');
                        if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题.');
                        fwrite($cfgFile, $Json);
                        fclose($cfgFile);

                        message('success', '所选大区合并成功！');
                    }

                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }

                break;
            case "add": #添加新区
                try {
                    if(!check_value($_POST['order'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['group'])) throw new Exception('请填写所有表单字段。');
                    if(is_array($newCfg)){
                        foreach ($newCfg as $item){
                            if($_POST['group'] == $item['SERVER_GROUP']) throw new Exception('请勿重复设置分区号');
                        }
                    }
                    if(!check_value($_POST['name'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['db'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['database_ip'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['database_user'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['database_pass'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['database_post'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['games_js'])) throw new Exception('请填写所有表单字段。');
                    $db2 = (check_value($_POST['db2']) ? true : false);

                    try{
                        global $config;
                        $dbName = (check_value($_POST['db2']) ? $_POST['db2'] : $_POST['db']);
                        $db = new dB($_POST['database_ip'], $_POST['database_post'], $dbName,$_POST['database_user'],$_POST['database_pass'],$config['SQL_PDO_DRIVER']);
                        #给游戏库添加[servercode]分区字段。
                        $db->query("if not exists (select 1 from syscolumns where name = 'servercode' and id = object_id('MEMB_INFO')) alter table MEMB_INFO add servercode int DEFAULT (0)");
                    }catch (Exception $exception){
                        echo '<div class="alert alert-danger" role="alert">'.$exception->getMessage().'</div>';
                    }
                    # 建立新的元素数据数组
                    $newElementData = [
                        "SERVER_GROUP"      =>  (int)$_POST['group'],
                        "SERVER_NAME"       =>  $_POST['name'],
                        "SERVER_DB_NAME"    =>  $_POST['db'],
                        "SERVER_DB2_NAME"   =>  $_POST['db2'],
                        "SQL_USE_2_DB"      =>  $db2,
                        "SERVER_IP"         =>  $_POST['database_ip'],
                        "SERVER_DB_USER"    =>  $_POST['database_user'],
                        "SERVER_DB_PASS"    =>  $_POST['database_pass'],
                        "SERVER_DB_POST"    =>  (int)$_POST['database_post'],
                        "SERVER_JS_POST"    =>  (int)$_POST['games_js'],
                        "order"             =>  (int)$_POST['order'],
                    ];
                    # 修改数组
                    $newCfg[] = $newElementData;
                    # 按顺序排序
                    # http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
                    usort($newCfg, function($a, $b) {
                        return $a['order'] - $b['order'];
                    });
                    # 转码
                    $Json = json_encode($newCfg, JSON_PRETTY_PRINT);
                    # 保存
                    $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'server.json', 'w');
                    if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题');
                    fwrite($cfgFile, $Json);
                    fclose($cfgFile);
                    $_POST = [];
                    message('success', '分区新增成功!');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "edit": #编辑分区
                try {
                    if(!is_array($newCfg)) throw new Exception('配置为空');
                    if(!check_value($_POST['id'])) throw new Exception('ID无效，禁止非法操作！');
                    if(!array_key_exists($_POST['id'], $newCfg)) throw new Exception('ID无效，禁止非法操作！');
                    if(is_array($newCfg)){
                        foreach ($newCfg as $key=>$item){
                            if($_POST['id'] == $key) continue;
                            if($_POST['group'] == $item['SERVER_GROUP']) throw new Exception('请勿重复设置分区号');
                        }
                    }

                    $db2 = (check_value($_POST['db2']) ? true : false);

                    #操作数据库
                    $muOnline = Connection::Database("Me_MuOnline",$_POST['id']);
                    $Web = Connection::Database("Web");
                    $webConfig = loadConfig();
                    $combineQuery = $Web->query_fetch("SELECT [Query] FROM [".X_TEAM_COMBINE_SERVER."]");
//                    if(!is_array($combineQuery))  throw new Exception('您的版本不支持此项功能，请联系作者升级。');
                    try{
                        $muOnline->beginTransaction();
                        $Web->beginTransaction();
                        $muOnline->query("UPDATE [MEMB_INFO] SET [servercode] = ? WHERE [servercode] = ?",[$_POST['group'],$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("UPDATE [X_TEAM_ACCOUNT] SET [servercode] = ? WHERE [servercode] = ?",[$_POST['group'],$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("UPDATE [X_TEAM_BANS] SET [servercode] = ? WHERE [servercode] = ?",[$_POST['group'],$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("UPDATE [X_TEAM_BAN_LOG] SET [servercode] = ? WHERE [servercode] = ?",[$_POST['group'],$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("UPDATE [X_TEAM_VOTE_LOGS] SET [servercode] = ? WHERE [servercode] = ?",[$_POST['group'],$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("UPDATE [X_TEAM_TOAST] SET [servercode] = ? WHERE [servercode] = ?",[$_POST['group'],$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        if(is_array($combineQuery)){
                            foreach ($combineQuery as $query) {
                                $Web->query("IF EXISTS(Select 1 From Sysobjects Where Name='".$query['Query']."') begin UPDATE [".$query['Query']."] SET [servercode] = ? WHERE [servercode] = ? end", [$_POST['group'],$newCfg[$_POST['id']]['SERVER_GROUP']]);
                            }
                        }
                        $muOnline->commit();
                        $Web->commit();
                    } catch(Exception $exception) {
                        $muOnline->rollBack();
                        $Web->rollBack();
                        message('error', $exception->getMessage());
                    }
                    $elementId = $_POST['id'];
                    # 建立新的元素数据数组
                    $newElementData = [
                        "SERVER_GROUP"      =>  (int)$_POST['group'],
                        "SERVER_NAME"       =>  $_POST['name'],
                        "SERVER_DB_NAME"    =>  $_POST['db'],
                        "SERVER_DB2_NAME"   =>  $_POST['db2'],
                        "SQL_USE_2_DB"      =>  $db2,
                        "SERVER_IP"         =>  $_POST['database_ip'],
                        "SERVER_DB_USER"    =>  $_POST['database_name'],
                        "SERVER_DB_PASS"    =>  $_POST['database_pass'],
                        "SERVER_DB_POST"    =>  (int)$_POST['database_post'],
                        "SERVER_JS_POST"    =>  (int)$_POST['games_js'],
                        "order"             =>  (int)$_POST['order'],
                    ];

                    $newCfg[$elementId] = $newElementData;

                    # 按顺序排序
                    # http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
                    usort($newCfg, function($a, $b) {
                        return $a['order'] - $b['order'];
                    });

                    # 编码
                    $Json = json_encode($newCfg, JSON_PRETTY_PRINT);

                        # 保存更改
                    $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'server.json', 'w');
                    if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题');
                    fwrite($cfgFile, $Json);
                    fclose($cfgFile);
                    $_POST = [];
                    message('success', '更改已成功保存!');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "delete": #删除分区
                try {
                    if(!is_array($newCfg)) throw new Exception('服务器分区配置为空');
                    if(!check_value($_POST['id'])) throw new Exception('ID无效，禁止非法操作！');
                    if(!array_key_exists($_POST['id'], $newCfg)) throw new Exception('ID无效，禁止非法操作！');
                    if($_POST['id'] == 0) throw new Exception('就最后一个大区了，你还删除它干嘛！用编辑吧！');

                    #操作数据库
//                    $muOnline = Connection::Database("Me_MuOnline",$_POST['id']);
                    $Web = Connection::Database("Web");
                    $webConfig = loadConfig();
                    $combineQuery = $Web->query_fetch("SELECT [Query] FROM [".X_TEAM_COMBINE_SERVER."]");
//                    if(!is_array($combineQuery))  throw new Exception('您的版本不支持此项功能，请联系作者升级。');
                    try{
//                        $muOnline->beginTransaction();
                        $Web->beginTransaction();
//                        $muOnline->exec("DELETE FROM [MEMB_INFO] WHERE [servercode] = ?",[$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("DELETE FROM [X_TEAM_ACCOUNT] WHERE servercode = ?",[$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("DELETE FROM [X_TEAM_BANS] WHERE servercode = ?",[$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("DELETE FROM [X_TEAM_BAN_LOG] WHERE servercode = ?",[$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("DELETE FROM [X_TEAM_VOTE_LOGS] WHERE servercode = ?",[$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        $Web->query("DELETE FROM [X_TEAM_TOAST] WHERE servercode = ?",[$newCfg[$_POST['id']]['SERVER_GROUP']]);
                        if(is_array($combineQuery)){
                            foreach ($combineQuery as $query) {
                                $Web->query("IF EXISTS(Select 1 From Sysobjects Where Name='".$query['Query']."') begin DELETE FROM [".$query['Query']."] WHERE servercode = ? end",[$newCfg[$_POST['id']]['SERVER_GROUP']]);
                            }
                        }
//                        $muOnline->commit();
                        $Web->commit();
                    } catch(Exception $exception) {
//                        $muOnline->rollBack();
                        $Web->rollBack();
                        message('error', $exception->getMessage());
                    }


                    unset($newCfg[$_POST['id']]);
                    $delConfig = array_values($newCfg);
                    # 转码
                    $Json = json_encode($delConfig, JSON_PRETTY_PRINT);
                    # 保存更改
                    $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'server.json', 'w+');
                    if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题.');
                    fwrite($cfgFile, $Json);
                    fclose($cfgFile);
                    $_POST = [];
                    message('success', '删除成功!');

                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "Reset_website": #重置分区
                try {
                    if(!is_array($newCfg)) throw new Exception('服务器分区配置为空');
                    if(!check_value($_POST['group'])) throw new Exception('请选择您要重置的大区。');
                    $Web = Connection::Database("Web");
                    $combineQuery = $Web->query_fetch("SELECT Query FROM ".X_TEAM_COMBINE_SERVER);
//                    if(!is_array($combineQuery))  throw new Exception('您的版本不支持此项功能，请联系作者升级。');
                    $Group = getGroupIDForServerCode($_POST['group']);
                    try {
                        if ($_POST['group'] == 'all'){
                            $Web->query("DELETE FROM [X_TEAM_ACCOUNT]");
                            $Web->query("DELETE FROM [X_TEAM_BANS]");
                            $Web->query("DELETE FROM [X_TEAM_BAN_LOG]");
                            $Web->query("DELETE FROM [X_TEAM_VOTE_LOGS]");
                            $Web->query("DELETE FROM [X_TEAM_CREDITS_LOGS]");
                            $Web->query("DELETE FROM [X_TEAM_TOAST]");
                            foreach ($combineQuery as $query) {
                                $Web->query("IF EXISTS(Select 1 From Sysobjects Where Name='".$query['Query']."') begin DELETE FROM [".$query['Query']."] end");
                            }
                            Connection::Database("Me_MuOnline")->query("if not exists (select 1 from syscolumns where name = 'servercode' and id = object_id('MEMB_INFO')) alter table MEMB_INFO add servercode int DEFAULT (0)");
                            message('success', '所有大区重置成功！');

                        }elseif(array_key_exists($Group, $newCfg)){
                            $Web->query("DELETE FROM [X_TEAM_ACCOUNT] WHERE servercode = ?",[$_POST['group']]);
                            $Web->query("DELETE FROM [X_TEAM_BANS] WHERE servercode = ?",[$_POST['group']]);
                            $Web->query("DELETE FROM [X_TEAM_BAN_LOG] WHERE servercode = ?",[$_POST['group']]);
                            $Web->query("DELETE FROM [X_TEAM_VOTE_LOGS] WHERE servercode = ?",[$_POST['group']]);
                            $Web->query("DELETE FROM [X_TEAM_TOAST] WHERE servercode = ?",[$_POST['group']]);
                            if(is_array($combineQuery)){
                                foreach ($combineQuery as $query) {
                                    $Web->query("IF EXISTS(Select 1 From Sysobjects Where Name='".$query['Query']."') begin DELETE FROM [".$query['Query']."] WHERE servercode = ? end,"[$_POST['group']]);
                                }
                            }
                            Connection::Database("Me_MuOnline")->query("if not exists (select 1 from syscolumns where name = 'servercode' and id = object_id('MEMB_INFO')) alter table MEMB_INFO add servercode int DEFAULT (0)");
                            message('success', '指定大区重置成功！');
                        }else{
                            message('error', '禁止非法提交,请确保数据的正确性!');
                        }
                    }catch(Exception $exception) {
                        message('error', $exception->getMessage());
                    }
                }catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            default:
                message('error', '禁止非法提交,请确保数据的正确性!');
                break;
        }
    }
    if(!is_array($newCfg)) throw new Exception('服务器分区配置文件为空！');
?>
    <div class="row">
            <div class="col-sm-11"><?=message('info', '是与您的服务器对应的。【数据表说明：账号表[MEMB_INFO]->[servercode]字段】','分区号 ');?>
            </div>
            <div class="col-sm-1">
            <div class="float-right">
                <a class="btn btn-danger text-white mb-1 col-md-12"  data-toggle="modal" data-target="#Reset-website">大区重置</a>
                <a class="btn btn-success text-white mb-1 col-md-12"  data-toggle="modal" data-target="#combine">一键合区</a>
            </div>
            </div>
    </div>


    <div class="card">
        <div class="card-header">分区管理</div>
        <div class="card">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                    <tr>
                        <th>排序</th>
                        <th>分区号</th>
                        <th>名称</th>
                        <th>数据库</th>
                        <th>数据库(ME)</th>
                        <th>IP</th>
                        <th>用户名</th>
                        <th>密码</th>
                        <th>Js(Ds)端口</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    if(is_array($newCfg)){
                    foreach($newCfg as $id => $server){
                    ?>
                        <form action="?module=Settings_Group" method="post">
                            <tr>
                                <td width="80px">
                                    <input type="hidden" class="form-control" name="id" value="<?=$id;?>" />
                                    <input type="text" class="form-control" name="order" value="<?=$server['order']; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')"/>
                                </td>
                                <td width="80px">
                                    <input type="text" class="form-control" name="group" id="group" value="<?=$server['SERVER_GROUP']; ?>" onkeyup="this.value=this.value.replace(/\D/g,'')"/>
                                </td>
                                <td><input type="text" class="form-control" name="name" value="<?=$server['SERVER_NAME']; ?>" /></td>

                                <td><input type="text" class="form-control" name="db" value="<?=$server['SERVER_DB_NAME']; ?>" /></td>
                                <td><input type="text" class="form-control" name="db2" value="<?=$server['SERVER_DB2_NAME']; ?>" placeholder="没有请留空"/></td>
                                <td>
                                    <input type="text" class="form-control" name="database_ip" value="<?=$server['SERVER_IP']; ?>" />
                                    <input type="hidden" name="database_post" value="<?=$server['SERVER_DB_POST'];?>" />
                                </td>
                                <td><input type="text" class="form-control" name="database_name" value="<?=$server['SERVER_DB_USER']; ?>" /></td>
                                <td><input type="text" class="form-control" name="database_pass" value="<?=$server['SERVER_DB_PASS']; ?>" /></td>
                                <td><input type="text" class="form-control" name="games_js" value="<?=$server['SERVER_JS_POST']; ?>" /></td>
                                <td width="10%">
                                    <div class="btn-group col-md-12">
                                        <button type="submit" name="submit"  value="edit"  class="btn btn-sm btn-info"><span class="ti-pencil"></span></button>
                                        <button type="submit" name="submit"  value="delete" class="btn btn-sm btn-danger"><span class="ti-trash"></span></button>
                                    </div>
                                </td>
                            </tr>
                        </form>
                        <?}?>
                    <?}?>
                    </tbody>
                </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">添加分区</div>
        <div class="card">
            <form action="?module=Settings_Group" method="post">
                <table class="table table-bordered table-striped text-center table-hover">
                    <thead>
                    <tr>
                        <th>优先级</th>
                        <th>分区号</th>
                        <th>名称</th>
                        <th>数据库</th>
                        <th>数据库(ME)</th>
                        <th>IP</th>
                        <th>用户名</th>
                        <th>密码</th>
                        <th>Js(Ds)端口</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="80px"><input class="form-control" type="text" name="order" id="order" value="10" onkeyup="this.value=this.value.replace(/\D/g,'')"/></td>
                            <td width="80px"><input class="form-control" type="text" name="group" id="group" onkeyup="this.value=this.value.replace(/\D/g,'')"/></td>
                            <td><input class="form-control" type="text" name="name" /></td>
                            <td><input class="form-control" type="text" name="db" value="MuOnline" /></td>
                            <td><input class="form-control" type="text" name="db2" placeholder="Me_MuOnline"/></td>
                            <td><input class="form-control" type="text" name="database_ip" value="127.0.0.1" /></td>
                            <td><input class="form-control" type="text" name="database_user" value="sa" /></td>
                            <td><input class="form-control" type="text" name="database_pass" /></td>
                            <td><input class="form-control" type="text" name="games_js" value="55970" /></td>
                            <td>
                                <input class="form-control" type="hidden" name="database_post" value="1433"/>
                                <button type="submit" name="submit"  value="add" class="btn btn-sm btn-info"><span class="ion-plus-round"></span></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <?if(is_array($newCfg)){?>
    <div class="modal fade" id="combine" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">[!!]在线合区</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form  action="?module=Settings_Group" class="form-horizontal mt-3" method="post">
                    <div class="modal-body">
                        <div style="padding: 1.25rem 1.25rem .25rem 1.25rem;margin-bottom: 1.25rem;border: 1px solid #E91E63;border-left-width: .25rem;border-radius: .25rem;">
                            <p>说明：该合区功能仅供支持单库多区，多库多区不支持。</p>
                            <p>说明：合并大区前只需关闭待合并大区服务器即可。</p>
                            <p class="text-danger">*所有大区：待合并大区如果选择所有大区即代表把所有大区都合并到总区</p>
                        </div>
                        <div class="form-group row justify-content-md-center">
                            <label for="new_group" class="col-sm-4 col-form-label text-right form-text">
                                选择<span class="text-danger">总</span>区
                            </label>
                            <div class="col-sm-5">
                                <select class="form-control" name="new_group" id="new_group">
                                    <? foreach (getServerGroupList() as $group=>$item){ ?>
                                        <option value="<?=$group?>"><?=$item?></option>
                                    <?}?>
                                </select>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        <div class="form-group row justify-content-md-center">
                            <label for="old_group" class="col-sm-4 col-form-label text-right form-text">
                                待合并区
                            </label>
                            <div class="col-sm-5">
                                <select class="form-control" name="old_group" id="old_group">
                                    <option value="all" selected>所有大区</option>
                                    <? foreach (getServerGroupList() as $group=> $item){ ?>
                                        <option value="<?=$group?>" selected><?=$item?></option>
                                    <?}?>
                                </select>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                        <div class="form-group row justify-content-md-center">会把此大区的账号合并到上面的总区</div>
                    </div>
                <div class="modal-footer" style="justify-content: center;">
                    <button type="submit" name="submit" value="combine" class="btn btn-danger">确定</button>
                    <button class="btn btn-secondary" data-dismiss="modal">关闭</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <?}?>
    <div class="modal fade" id="Reset-website" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">[!!]重置大区</h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form  action="?module=Settings_Group" class="form-horizontal mt-3" method="post">
                    <div class="modal-body">
                        <div style="padding: 1.25rem 1.25rem .25rem 1.25rem;margin-bottom: 1.25rem;border: 1px solid #E91E63;border-left-width: .25rem;border-radius: .25rem;">
                            <p><strong>说明：如果您还原了游戏数据库，请使用此功能进行重置。</strong></p>
                            <p><strong>使用此功能前请认真阅读下面的提示信息。</strong></p>
                            <p> <strong>谨慎</strong>：<br>请谨慎操作此功能，如果您使用了一些插件，插件中包含了账号关联的数据都会清空，一旦重置数据是无法恢复的。</p>
                            <p> <strong>会清空</strong>：<br>重置网站<span class="text-danger">会清空</span>网站数据库中所有账号的数据，但<span class="text-danger">不会重置游戏库</span>。</p>
                            <p> <strong>会保留</strong>：<br>重置网站<span class="text-danger">会保留</span>网站数据库中的游戏设置，除了账号信息外的所有。</p>
                        </div>
                    </div>
                    <div style="padding: 1.25rem 0 .25rem 0;margin-left: 15px;margin-right: 15px;margin-bottom: 1.25rem;border: 1px solid #E91E63;border-left-width: .25rem;border-radius: .25rem;">
                    <div class="form-group row justify-content-md-center">
                        <label for="group" class="col-sm-4 col-form-label text-right form-text">
                            重置大区
                        </label>
                        <div class="col-sm-5">
                            <select class="form-control" name="group" id="group">
                                    <option value="" selected>请选择大区</option>
                                    <option value="all">所有大区</option>
                                <? foreach (getServerGroupList() as $group=> $item){ ?>
                                    <option value="<?=$group?>"><?=$item?></option>
                                <?}?>
                            </select>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    </div>
                    <div class="modal-footer" style="justify-content: center;">
                        <button type="submit" name="submit" value="Reset_website" class="btn btn-danger">重置网站</button>
                        <button class="btn btn-secondary" data-dismiss="modal">关闭窗口</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
?>