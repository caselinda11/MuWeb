<?php
/**
 * 文件说明
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
                        <li class="breadcrumb-item active">发送游戏公告</li>
                    </ol>
                </div>
                <h4 class="page-title">发送游戏公告</h4>
            </div>
        </div>
    </div>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>使用说明</strong>
        使用前你必须确保-> 网站设置->分区配置 中的JS端口号对应您的分区JS。<br>
        （如果网站是跨服务器你必须在joinServer目录的[AllowableIpList.txt]中写明您的网站服务器IP地址）<br>
         (如果你是muemu服务端，joinServer则无效，请把上述中的js对DataServer操作。)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php

try {
if(check_value($_POST['submit'])) {
    try {
        $server = localServerGroupConfigs(getGroupIDForServerCode($_POST['group']));
        if (!is_array($server) || empty($server) || !$server) throw new Exception("分区数据获取失败!");
        if(!check_value($_POST['message'])) throw new  Exception("请输入发送的消息内容");
        sendMessageGames($_POST['title'],$_POST['message'],$server['SERVER_IP'],$server['SERVER_JS_POST']);
    } catch (Exception $exception) {
        message('error', $exception->getMessage());
    }
}
?>
    <div class="card">
        <div class="card-header">发送游戏公告</div>
        <div class="card-body">
            <form class="form-horizontal mt-3" action="" method="post">
                <div class="form-group row justify-content-md-center">
                    <div class="col-md-6">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label for="group" class="input-group-text">游戏大区</label>
                            </div>
                            <select class="form-control" name="group" id="group">
                                <? foreach (getServerGroupList() as $group=> $item){ ?>
                                    <option value="<?=$group?>"><?=$item?></option>
                                <?}?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <div class="col-md-6">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <label for="title" class="input-group-text">公告标题</label>
                            </div>
                            <input type="text" class="form-control" id="title" name="title" value="网站公告">
                        </div>
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <div class="col-md-6">
                        <label class="sr-only" for="message"></label>
                        <textarea class="form-control" id="message" name="message" rows="3" style="height: 100px;"></textarea>
                    </div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <button class="btn btn-success col-md-3" type="submit" name="submit" value="submit">发送</button>
                </div>
            </form>
        </div>
    </div>
<?php
}catch (Exception $ex){
    message('error', $ex->getMessage());
}