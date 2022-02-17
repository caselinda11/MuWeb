<?php
/**
 * 安装程序第六步
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(!defined('access') or !access or access != 'install') die();

try {
    if(check_value($_POST['submit'])) {
        try{
            if(!check_value($_POST['GAME_DB_CODE'])) throw new Exception('您必须填写分区代号!');
            if(!Validator::Number($_POST['GAME_DB_CODE'])) throw new Exception('您输入的代号必须为纯数字!');
            if(!check_value($_POST['GAME_DB_HOST'])) throw new Exception('您必须填写游戏数据库地址!');
            if(!check_value($_POST['GAME_DB_NAME_1'])) throw new Exception('您必须填写游戏数据库[1]!');
            if(!check_value($_POST['GAME_DB_USER'])) throw new Exception('您必须填写数据库用户名!');
            if(!check_value($_POST['GAME_DB_PASS'])) throw new Exception('您必须填写数据库密码!');
            $db2 = (check_value($_POST['GAME_DB_NAME_2']) ? true : false);

            # 配置文件转码
            $serverConfig = [
                    'SERVER_GROUP'       => (int)$_POST['GAME_DB_CODE'],
                    'SERVER_NAME'        => $_POST['GAME_NAME'],
                    'SERVER_DB_NAME'     => $_POST['GAME_DB_NAME_1'],
                    'SERVER_DB2_NAME'    => $_POST['GAME_DB_NAME_2'],
                    'SQL_USE_2_DB'       => (bool)$db2,
                    'SERVER_IP'          => $_POST['GAME_DB_HOST'],
                    'SERVER_DB_USER'    => $_POST['GAME_DB_USER'],
                    'SERVER_DB_PASS'     => $_POST['GAME_DB_PASS'],
                    'SERVER_DB_POST'     => (int)1433,
                    'SERVER_JS_POST'     => (int)55970,
                    'order'              => (int)1,
            ];
            $newCfg[] = $serverConfig;
            # 按顺序排序
            # http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
            usort($newCfg, function($a, $b) {
                return $a['order'] - $b['order'];
            });

            $Configs = json_encode($newCfg, JSON_PRETTY_PRINT);
            if($Configs == false) throw new Exception('无法编码对配置进行转码!');

            # 保存配置文件
            $cfgFile = fopen($serverPath, 'w');
            if(!$cfgFile) throw new Exception('无法打开网站程序分区配置文件!');
            $cfgUpdate = fwrite($cfgFile, $Configs);
            if(!$cfgUpdate) throw new Exception('无法保存网站程序分区配置文件!');
            fclose($cfgFile);

            try{
                $dbName = (check_value($_POST['GAME_DB_NAME_2']) ? $_POST['GAME_DB_NAME_2'] : $_POST['GAME_DB_NAME_1']);
                $db = new dB($_POST['GAME_DB_HOST'], $_SESSION['SQL_DB_PORT'], $dbName,$_POST['GAME_DB_USER'],$_POST['GAME_DB_PASS'],$_SESSION['SQL_PDO_DRIVER']);
                if($db->dead){
                    throw new Exception($db->error);
                }
                $db->query("if not exists (select 1 from syscolumns where name = 'servercode' and id = object_id('MEMB_INFO')) alter table MEMB_INFO add servercode int DEFAULT (0)");
            }catch (Exception $exception){
                echo '<div class="alert alert-danger" role="alert">'.$exception->getMessage().'</div>';
            }
            # 移至下一步
            $_SESSION['install_cstep']++;
            header('Location: install.php');
            die();
        } catch (Exception $ex) {
            echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';
        }
    }
?>
    <div class="progress mb-3">
        <div class="progress-bar progress-bar-striped bg-success progress-bar-animated" role="progressbar" style="width: 90%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">90%</div>
    </div>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>提示:</strong>
            如果未使用 数据库[2] 请留空！
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="card">
        <div class="card-header"><?global $install?><?=$install['step_list'][6][1]?></div>
        <div class="card-body">
            <form class="form-horizontal" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <label for="GAME_DB_CODE" class="col-sm-2 col-form-label text-right">分区代号</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="GAME_DB_CODE" name="GAME_DB_CODE" placeholder="必须是纯数字" onkeyup="this.value=this.value.replace(/\D/g,'')">
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="GAME_NAME" class="col-sm-2 col-form-label text-right">分区名称</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="GAME_NAME" name="GAME_NAME" placeholder="第一大区">
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="GAME_DB_NAME_1" class="col-sm-2 col-form-label text-right">
                        数据库[1]
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="GAME_DB_NAME_1" name="GAME_DB_NAME_1" value="MuOnline">
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="GAME_DB_NAME_2" class="col-sm-2 col-form-label text-right">
                        数据库[2]
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="GAME_DB_NAME_2" name="GAME_DB_NAME_2" placeholder="Me_MuOnline">
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="GAME_DB_HOST" class="col-sm-2 col-form-label text-right">
                        数据库地址
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="GAME_DB_HOST" name="GAME_DB_HOST" value="127.0.0.1">
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="GAME_DB_USER" class="col-sm-2 col-form-label text-right">
                        数据库账号
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="GAME_DB_USER" name="GAME_DB_USER" value="sa">
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="GAME_DB_PASS" class="col-sm-2 col-form-label text-right">
                        数据库密码
                    </label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="GAME_DB_PASS" name="GAME_DB_PASS" placeholder="">
                    </div>
                </div>

                <div class="form-group row justify-content-md-center">
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                        下一步
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php
} catch (Exception $ex) {
    echo '<div class="alert alert-danger" role="alert">'.$ex->getMessage().'</div>';
}