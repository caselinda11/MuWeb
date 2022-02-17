<?php
/**
 * 账号管理
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
                        <li class="breadcrumb-item active">用户管理</li>
                        <li class="breadcrumb-item active">账号管理</li>
                    </ol>
                </div>
                <h4 class="page-title">账号管理</h4>
            </div>
        </div>
    </div>
<? try {
    global $serverGrouping;
    if ($_POST['submit']){
        $selectNumber = (int)$_POST['number'];
        if(!check_value($_POST['group'])){
            $use_username = '';
            if(check_value($_POST['username'])) $use_username = " AND memb___id = '".$_POST['username']."'";
            foreach ($serverGrouping AS $key=>$item) {
                $query = "SELECT TOP ".$selectNumber." memb_guid,memb___id,memb_name,addr_deta,mail_addr FROM "._TBL_MI_." WHERE  servercode = ? ".$use_username;
                $database = Connection::Database('Me_MuOnline', $key);
                $databaseData[$key] = $database->query_fetch($query,[$item['SERVER_GROUP']]);
                if(empty($databaseData)) continue;
            }
            unset($_POST['group']);
            unset($_POST['username']);
        }else{
            if (!check_value($_POST['group'])) throw new Exception('请正确选择分区!');
            $use_username = '';
            if(check_value($_POST['username'])) $use_username = " AND memb___id =".$_POST['username'];
            $common = new common();
            $code = getGroupIDForServerCode($_POST['group']);
            $group = $_POST['group'];

            $query = "SELECT TOP ".$selectNumber." memb_guid,memb___id,memb_name,addr_deta,mail_addr FROM " . _TBL_MI_ ." WHERE  servercode = ?".$use_username;
            $database = Connection::Database('Me_MuOnline', $code);
            $databaseData[$code] = $database->query_fetch($query,[$group]);
            unset($_POST['group']);
            unset($_POST['username']);

        }
    }
    ?>
    <div class="card">
        <div class="card-header">搜索</div>
        <div class="card-body mb-3">
            <div class="col-md-12">
                <form action="" method="post" role="form">
                    <div class="row justify-content-center">
                        <div class="input-group col-md-3 mt-2">
                            <div class="input-group-prepend"><label class="input-group-text" for="group">大区</label></div>
                            <select class="form-control" name="group" id="group">
                                <option value="">所有大区搜索</option>
                                <?foreach (getServerGroupList() as $key=> $item){?>
                                    <option value="<?=$key;?>"><?=$item;?></option>
                                <?}?>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="input-group col-md-3 mt-2">
                            <div class="input-group-prepend"><label class="input-group-text" for="number">数量</label></div>
                            <select class="form-control" name="number" id="number">
                                <option value="100">100</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                                <option value="2000">2000</option>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="input-group col-md-3 mt-2">
                            <div class="input-group-prepend"><label class="input-group-text" for="username">账号</label></div>
                            <input type="text" class="form-control" name="username" placeholder="可留空"/>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="input-group col-md-3 mt-2">
                            <button class="btn btn-primary col-md-12" type="submit" name="submit" value="Search">Go!</button>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3  font-14">
                使用所有大区搜索将消耗一定的资源
            </footer>
        </div>
    </div>
    <?
    if(!empty($databaseData)){
        #过滤数组
        $newData = array_filter($databaseData);?>
        <div class="card">
            <div class="card-header">[<?=$_POST['username']?>]账号信息</div>
            <div class="card-body">
                <table class="table text-center" id="datatable">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>账号</th>
                        <th>昵称</th>
                        <th>身份证</th>
                        <th>邮箱</th>
                        <th>所属大区</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?foreach ($newData AS $key=>$sdata){?>
                        <?foreach ($sdata AS $data){?>
                            <tr>
                                <td><?=$data['memb_guid']?></td>
                                <td><?=$data['memb___id']?></td>
                                <td><?=$data['memb_name']?></td>
                                <td><?=$data['addr_deta']?></td>
                                <td><?=$data['mail_addr']?></td>
                                <td><?=getGroupNameForGroupID($key)?></td>
                                <td><a href="<?=admincp_base("Account_Info&group=".getServerCodeForGroupID($key)."&id=".$data['memb_guid']);?>" class="btn btn-outline-secondary waves-effect">账号详细信息</a></td>
                            </tr>
                        <?}?>
                    <?}?>
                    </tbody>

                </table>
            </div>
        </div>
    <?}?>
    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}