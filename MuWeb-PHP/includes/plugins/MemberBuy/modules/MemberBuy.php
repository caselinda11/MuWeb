<?php
/**
 * [我的插件]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">会员升级</li>
        </ol>
    </nav>
<?php
try {
    $MemberBuy = new \Plugin\MemberBuy();
    $tConfig = $MemberBuy->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    #当前会员等级
    $memberLevel = $MemberBuy->getMemberLevel($_SESSION['group'],$_SESSION['username']);
    #下一级
    $nextLevel = ($memberLevel >= $tConfig['max_level']) ? $tConfig['max_level'] : $memberLevel + 1;
    try{
        if(check_value($_POST['submit'])) {
            $MemberBuy->setMemberBuy($_SESSION['group'],$_SESSION['username']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">会员升级</div>
        <div class="card-body">
            <h5>会员价格展示表</h5>
            <table class="table table-bordered table-hover text-center">
                <thead>
                <tr>
                    <th>会员名称</th>
                    <th>会员等级</th>
                    <th>升级费用</th>
                    <th>描述</th>
                </tr>
                </thead>
                <tbody>
                <?$list = $MemberBuy->getMemberBuyList()?>
                <?IF(is_array($list)){?>
                    <?foreach ($list as $data){?>
                    <tr>
                        <td><strong><?=$data['vip_name']?></strong></td>
                        <td><?=$data['vip_code']?></td>
                        <td><strong><?=$data['price']?></strong><?=getPriceType($data['price_type'])?></td>
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
                <p class="alert alert-info">会员最高等级为<?=$tConfig['max_level']?>级，仅支持逐个升级，每次升级所收取的费用可能不一样。</p>
            </div>
            <form class="form-horizontal mt-3" action="" method="post">

                <div class="form-group row justify-content-md-center">
                    <label for="new_name" class="col-sm-4 col-form-label text-right">
                        当前等级
                    </label>
                    <div class="col-sm-4">
                        <input id="level" type="text" name="level" value="<?=$memberLevel?>" disabled="disabled" class="form-control">
                    </div>
                    <small class="col-sm-4 text-left form-inline text-muted">*您当前会员等级</small>
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
                        <div class="col-sm-4">
                            <input id="next_level" type="text" name="next_level" value="<?=$nextLevel?>" disabled="disabled" class="form-control">
                        </div>
                        <small class="col-sm-4 text-left form-inline text-muted">升级后的等级。</small>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <input type="hidden" name="key" value="<?=Token::generateToken('MemberBuy')?>"/>
                        <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-3">
                            升级
                        </button>
                    </div>
                <?}?>

            </form>

            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">会员升级</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}