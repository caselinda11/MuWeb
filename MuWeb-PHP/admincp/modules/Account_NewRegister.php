<?php
/**
 * 后台最新注册
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
                    <li class="breadcrumb-item active">最新注册</li>
                </ol>
            </div>
            <h4 class="page-title">最新注册 - [显示最近50条]</h4>
        </div>
    </div>
</div>
<?php
try {
        $account = new Account();
        $newRegData = $account->getWebRegistrationData(15);
        if(!is_array($newRegData)) throw new Exception('网站数据库中暂无数据！');
?>
<div class="card mb-3">
    <div class="card-header">最近注册</div>
    <div class="card-body">
        <table id="datatable" class="table text-center">
            <thead>
            <tr>
                <th>账号</th>
                <th>大区</th>
                <th>日期</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?foreach ($newRegData as $data){?>
                <?$gID = $account->getUserGIDForUsername(getGroupIDForServerCode($data['servercode']),$data['account'])?>
                <tr>
                <td><?=$data['account']?></td>
                <td><?=getGroupNameForServerCode($data['servercode'])?></td>
                <td><?=$data['CreateTime']?></td>
                <td><a href="<?=admincp_base("Account_Info&group=".$data['servercode']."&id=".$gID);?>" class="btn btn-outline-secondary waves-effect">账号详细信息</a></td>
                </tr>
            <?}?>
            </tbody>
        </table>
    </div>
</div>
<?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
