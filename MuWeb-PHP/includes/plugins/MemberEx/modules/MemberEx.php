<?php
/**
 * [MemberEx]模块页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">兑换会员</li>
        </ol>
    </nav>
    <?php

try {
    $MemberEx = new \Plugin\MemberEx();
    $tConfig = $MemberEx->loadConfig();
    #当前会员等级
    $memberLevel = $MemberEx->getMemberLevel($_SESSION['group'],$_SESSION['username']);
    #下一级
    $nextLevel = ($memberLevel >= $tConfig['max_level']) ? $tConfig['max_level'] : $memberLevel + 1;
    $data = $MemberEx->getMemberExList($nextLevel);
    try{
        if(check_value($_POST['submit'])) {
            if($MemberEx->count < $data['items_number']) throw new Exception("您所拥有的升级物品不足，请确保仓库中存有足够的物品数量。");
            $MemberEx->setMemberEx($_SESSION['group'],$_SESSION['username']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }

    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">兑换会员</div>
        <div class="card-body">
            <h5>会员价格展示表</h5>
            <table class="table table-bordered table-hover text-center">
                <thead>
                <tr>
                    <th>会员名称</th>
                    <th>会员等级</th>
                    <th>需求物品</th>
                    <th>物品数量</th>
                    <th>描述</th>
                </tr>
                </thead>
                <tbody>
                <?$list = $MemberEx->getMemberExList()?>
                <?IF(is_array($list)){?>
                    <?foreach ($list as $data){?>
                    <tr>
                        <td><strong><?=$data['vip_name']?></strong></td>
                        <td><?=$data['vip_code']?></td>
                        <td><strong><?=$data['items_name']?></strong></td>
                        <td><strong><?=$data['items_number']?></strong>件</td>
                        <td><?=$data['Description']?></td>
                    </tr>
                    <?}?>
                    <?}else{?>
                        <tr>
                            <td>暂无可会员升级列表菜单</td>
                        </tr>
                    <?}?>
                </tbody>
            </table>
            <div class="mt-3">
                <p class="alert alert-info">升级会员请确定您仓库中存在有该等级的物品要求，并且保持游戏离线状态。</p>
            </div>
            <div class="mt-3">
                <p class="alert alert-info">会员最高等级为<?=$tConfig['max_level']?>级，仅支持逐个升级，每次升级所收取的费用可能不一样。</p>
            </div>
            <form class="form-horizontal mt-3" action="" method="post">

                <div class="form-group row justify-content-md-center">
                    <label for="new_name" class="col-sm-4 col-form-label text-right">
                        当前等级
                    </label>
                    <div class="col-sm-5">
                        <input id="level" type="text" name="level" value="<?=$memberLevel?>" disabled="disabled" class="form-control">
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted">*您当前会员等级</small>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="next_level" class="col-sm-4 col-form-label text-right">
                        需求物品
                    </label>
                    <div class="col-sm-5">
                        <input id="next_level" type="text" name="next_level" value="<?=$MemberEx->count?> 件<?=$data['items_name']?>" disabled="disabled" class="form-control">
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted">*仓库中现有</small>
                </div>
                <?if($memberLevel >= $tConfig['max_level']){?>
                <div class="form-group row justify-content-md-center">
                    <small class="col-sm-4 text-center form-inline text-danger">您的会员等级已达到最大等级，无需购买。</small>
                </div>
                <?}else{?>
                    <div class="form-group row justify-content-md-center">
                        <label for="next_level" class="col-sm-4 col-form-label text-right">
                            下一级
                        </label>
                        <div class="col-sm-5">
                            <input id="next_level" type="text" name="next_level" value="<?=$nextLevel?>" disabled="disabled" class="form-control">
                        </div>
                        <small class="col-sm-3 text-left form-inline text-muted">升级后的等级。</small>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <input type="hidden" name="key" value="<?=Token::generateToken('MemberEx')?>"/>
                        <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                            升级
                        </button>
                    </div>
                <?}?>

            </form>

            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">兑换会员</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}