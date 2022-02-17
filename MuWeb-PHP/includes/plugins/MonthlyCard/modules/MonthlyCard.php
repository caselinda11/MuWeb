<?php
/**
 * [MonthlyCard]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">月卡套餐</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $MonthlyCard = new \Plugin\MonthlyCard();
    $tConfig = $MonthlyCard->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    try{
        if(check_value($_POST['submit'])) {
            if($_POST['submit'] == 'submit_salary'){
                $MonthlyCard->setSalaryMonthlyCard($_SESSION['group'],$_SESSION['username']);
            }else{
                $MonthlyCard->setBuyMonthlyCard($_SESSION['group'],$_SESSION['username'],$_POST['ID']);
            }
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    $list = $MonthlyCard->getMonthlyCardBuyList($_SESSION['username']);
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">月卡套餐</div>
        <div class="card-body mb-3">
            <?IF(is_array($list)){?>
                <?$data = $MonthlyCard->getMonthlyCardList($list['buy_id']);?>
                <form class="form-horizontal mt-3" action="" method="post">
                    <div class="form-group row justify-content-md-center">
                        <label for="new_name" class="col-sm-4 col-form-label text-right">
                            项目名称
                        </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="new_name" name="new_name" maxlength="10" value="<?=$data['project_name']?>" disabled>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <label for="new_name" class="col-sm-4 col-form-label text-right">
                            剩余天数
                        </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="new_name" name="new_name" maxlength="10" value="<?=$list['buy_day']?>" disabled>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <label for="new_name" class="col-sm-4 col-form-label text-right">
                            今日可领
                        </label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control" id="new_name" name="new_name" maxlength="10" value="<?=$data['daily_salary']?><?=getPriceType($data['salary_type'])?>" disabled>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <div class="form-group row justify-content-md-center">
                        <input type="hidden" name="key" value="<?=Token::generateToken('SalaryCard')?>"/>
                        <button type="submit" name="submit" value="submit_salary" class="btn btn-success col-sm-4">
                            领取
                        </button>
                    </div>
                </form>
            <?}else{?>
                <div class="mb-3">
                    <table class="table table-bordered table-striped text-center">
                    <?$list = $MonthlyCard->getMonthlyCardList()?>
                    <?IF(is_array($list)){?>
                    <thead>
                    <tr>
                        <th>名称</th>
                        <th>售价</th>
                        <th>可领天数</th>
                        <th>每日可领</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?foreach ($list as $data){?>
                        <?if(!$data['status']) continue;?>
                    <tr>
                        <td><?=$data['project_name']?></td>
                        <td><strong><?=$data['price']?></strong><?=getPriceType($data['credit_type'])?></td>
                        <td><?=$data['day']?></td>
                        <td><strong><?=$data['daily_salary']?></strong><?=getPriceType($data['salary_type'])?></td>
                        <td>
                            <form class="form-horizontal mt-3" action="" method="post">
                                <input name="ID" TYPE="hidden" value="<?=$data['ID']?>"/>
                                <input type="hidden" name="key" value="<?=Token::generateToken('MonthlyCard'.$data['ID'])?>"/>
                                <button type="submit" name="submit" value="submit" class="btn btn-success">
                                    购买
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?}?>
                    </tbody>
                    <?}else{?>
                        <tr><th class="text-danger">暂无月卡配置！</th></tr>
                    <?}?>
                </table>
            </div>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>怎么领取？</li>
                    <p class="alert alert-info">选择一种套餐购买后，每日可进入此页面点击领取。</p>
                    <li>过期领取？</li>
                    <p class="alert alert-info">购买的套餐是多少天就可以领取多少天工资，每领一次-1天。</p>
                    <li>重复购买？</li>
                    <p class="alert alert-info">一旦购买成功，您必须领完该套装才可购买其他套餐。</p>
                    <li>工资发放。</li>
                    <p class="alert alert-info">每日的凌晨12点更新时间，每天限领一次，点击领取将自动发放至账号。</p>
                </ol>
            </div>
            <?}?>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">月卡套餐</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}