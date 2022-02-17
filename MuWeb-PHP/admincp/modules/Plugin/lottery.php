<?php
/**
 * 在线商城插件后台模块
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
                        <li class="breadcrumb-item active">插件系统</li>
                        <li class="breadcrumb-item active">在线夺宝</li>
                    </ol>
                </div>
                <h4 class="page-title">在线夺宝</h4>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="float-right mb-2">
                <a class="btn btn-success btn-lg" href="<?=admincp_base('/Plugin/lottery_shop'); ?>">稀有商城设置</a>
                <a class="btn btn-warning btn-lg" href="<?=admincp_base('Plugin/lottery_log'); ?>">夺宝日志</a>
            </div>
        </div>
    </div>
<?php
    function submit()
    {
            $xmlPath = __PATH_LOTTERY_ROOT__ . 'config.xml';
            $xml = simplexml_load_file($xmlPath);
            $xml->active = $_POST['active'];
            $xml->Crystal_Name = $_POST['Crystal_Name'];
            $xml->Crystal_rate = $_POST['Crystal_rate'];
            $xml->credit_type = $_POST['credit_type'];
            $xml->price = $_POST['price'];
            $xml->many_number = $_POST['many_number'];
            $xml->many_price = $_POST['many_price'];

        $xml->reward_item_name_1  = $_POST['reward_item_name_1'];
        $xml->reward_item_name_2  = $_POST['reward_item_name_2'];
        $xml->reward_item_name_3  = $_POST['reward_item_name_3'];
        $xml->reward_item_name_4  = $_POST['reward_item_name_4'];
        $xml->reward_item_name_5  = $_POST['reward_item_name_5'];
        $xml->reward_item_name_6  = $_POST['reward_item_name_6'];
        $xml->reward_item_name_7  = $_POST['reward_item_name_7'];
        $xml->reward_item_name_8  = $_POST['reward_item_name_8'];
        $xml->reward_item_name_9  = $_POST['reward_item_name_9'];
        $xml->reward_item_name_10 = $_POST['reward_item_name_10'];
        $xml->reward_item_name_11 = $_POST['reward_item_name_11'];
        $xml->reward_item_name_12 = $_POST['reward_item_name_12'];
        $xml->reward_item_name_13  = $_POST['reward_item_name_13'];

        $xml->reward_item_code_1  = $_POST['reward_item_code_1'];
        $xml->reward_item_code_2  = $_POST['reward_item_code_2'];
        $xml->reward_item_code_3  = $_POST['reward_item_code_3'];
        $xml->reward_item_code_4  = $_POST['reward_item_code_4'];
        $xml->reward_item_code_5  = $_POST['reward_item_code_5'];
        $xml->reward_item_code_6  = $_POST['reward_item_code_6'];
        $xml->reward_item_code_7  = $_POST['reward_item_code_7'];
        $xml->reward_item_code_8  = $_POST['reward_item_code_8'];
        $xml->reward_item_code_9  = $_POST['reward_item_code_9'];
        $xml->reward_item_code_10 = $_POST['reward_item_code_10'];
        $xml->reward_item_code_11 = $_POST['reward_item_code_11'];
        $xml->reward_item_code_12 = $_POST['reward_item_code_12'];
        $xml->reward_item_code_13  = $_POST['reward_item_code_13'];

        $xml->reward_item_option_1  = $_POST['reward_item_option_1'];
        $xml->reward_item_option_2  = $_POST['reward_item_option_2'];
        $xml->reward_item_option_3  = $_POST['reward_item_option_3'];
        $xml->reward_item_option_4  = $_POST['reward_item_option_4'];
        $xml->reward_item_option_5  = $_POST['reward_item_option_5'];
        $xml->reward_item_option_6  = $_POST['reward_item_option_6'];
        $xml->reward_item_option_7  = $_POST['reward_item_option_7'];
        $xml->reward_item_option_8  = $_POST['reward_item_option_8'];
        $xml->reward_item_option_9  = $_POST['reward_item_option_9'];
        $xml->reward_item_option_10 = $_POST['reward_item_option_10'];
        $xml->reward_item_option_11 = $_POST['reward_item_option_11'];
        $xml->reward_item_option_12 = $_POST['reward_item_option_12'];
        $xml->reward_item_option_13  = $_POST['reward_item_option_13'];

        $xml->reward_item_success_rate_1  = $_POST['reward_item_success_rate_1'];
        $xml->reward_item_success_rate_2  = $_POST['reward_item_success_rate_2'];
        $xml->reward_item_success_rate_3  = $_POST['reward_item_success_rate_3'];
        $xml->reward_item_success_rate_4  = $_POST['reward_item_success_rate_4'];
        $xml->reward_item_success_rate_5  = $_POST['reward_item_success_rate_5'];
        $xml->reward_item_success_rate_6  = $_POST['reward_item_success_rate_6'];
        $xml->reward_item_success_rate_7  = $_POST['reward_item_success_rate_7'];
        $xml->reward_item_success_rate_8  = $_POST['reward_item_success_rate_8'];
        $xml->reward_item_success_rate_9  = $_POST['reward_item_success_rate_9'];
        $xml->reward_item_success_rate_10 = $_POST['reward_item_success_rate_10'];
        $xml->reward_item_success_rate_11 = $_POST['reward_item_success_rate_11'];
        $xml->reward_item_success_rate_12 = $_POST['reward_item_success_rate_12'];
        $xml->reward_item_success_rate_13  = $_POST['reward_item_success_rate_13'];

            $save = $xml->asXML($xmlPath);
            if ($save) {
                message('success', '配置修改成功！');
            } else {
                message('error', '保存时发生错误！');
            }
    }
    $vote = new Vote();
    $lottery = new \Plugin\lottery();

    try {
        if (check_value($_POST['submit'])) {

           for ($i=1;$i<=13;$i++){
                if(!$_POST['reward_item_name_'.$i]) {
                    throw new Exception('错误:: ['.$i.'] - 请输入第['.$i.']个物品的名称!');
                }
                if(!Validator::UnsignedNumber($_POST['reward_item_success_rate_'.$i])){
                    throw new Exception('错误:: ['.$i.'] - 请正确输入第['.$i.']个物品的成功率!');
                }
                if(!$_POST['reward_item_option_'.$i]){
                    throw new Exception('错误:: ['.$i.'] - 请输入第['.$i.']个物品的属性说明!');
                }
                if(!$_POST['reward_item_code_'.$i]){
                    throw new Exception('错误:: ['.$i.'] - 请输入第['.$i.']个物品的代码!');
                }
                if(!Validator::Items($_POST['reward_item_code_'.$i])){
                    throw new Exception('错误:: ['.$i.'] - 请正确输入第['.$i.']个物品的代码!');
                }
            }
           submit();
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
try {
    $moduleConfig = $lottery->loadConfig();
    $creditSystem = new CreditSystem();
//    $reward = $lottery->getLotteryConfig();
//    if(!$reward) message('error','无法获取奖励物品数据！','严重错误：');
    ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            在线夺宝
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td  colspan="4" width="60%"><strong>模块状态</strong>  <span class="text-muted">禁用/启用</td>
                        <td><?=enableDisableCheckboxes('active', $moduleConfig['active'], '启用', '禁用'); ?></td>
                    </tr>

                    <tr>
                        <td colspan="4"><strong>水晶名称</strong>  <span class="text-muted">定义一个用于兑换特殊物品的水晶名称</td>
                        <td><input type="text" name="Crystal_Name" value="<?=$moduleConfig['Crystal_Name']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>水晶概率</strong>  <span class="text-muted">此概率应与下列奖品配置的成功率相加总和为100</td>
                        <td><input type="text" name="Crystal_rate" value="<?=$moduleConfig['Crystal_rate']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>货币类型</strong>  <span class="text-muted">选择一种使用该功能的费用货币类型</span></td>
                        <td>
                            <?=$creditSystem->buildSelectInput("credit_type", $moduleConfig['credit_type'], "form-control"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>夺宝价格</strong>  <span class="text-muted">每次夺一次宝的单价</td>
                        <td><input type="text" name="price" value="<?=$moduleConfig['price']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>连抽设置</strong>  <span class="text-muted">设置五连抽或者十连抽</td>
                        <td><?=enableDisableCheckboxes('many_number', $moduleConfig['many_number'], '五连', '十连'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="4"><strong>连抽价格</strong>  <span class="text-muted">多连抽夺宝价格</td>
                        <td><input type="text" name="many_price" value="<?=$moduleConfig['many_price']?>" class="form-control"></td>
                    </tr>
                    <tr>
                        <th colspan="5" style="text-align: center"><strong>奖品配置</strong></th>
                    </tr>
                    <tr>
                        <th width="5%" style="text-align: center">No</th>
                        <th width="15%" style="text-align: center">物品名称</th>
                        <th width="25%" style="text-align: center">物品代码</th>
                        <th width="10%" style="text-align: center">成功率(%)</th>
                        <th style="text-align: center">物品说明<span class="text-muted">(*鼠标放到物品上显示的信息)</span></th>
                    </tr>
                    <?
                    for ($i=1;$i<=13;$i++){?>
                        <tr>
                            <td align="center"><?=$i?></td>
                            <td><input type="text" name="reward_item_name_<?=$i?>" value="<?=$moduleConfig['reward_item_name_'.$i]?>" class="form-control" /></td>
                            <td><input type="text" name="reward_item_code_<?=$i?>" value="<?=$moduleConfig['reward_item_code_'.$i]?>" class="form-control" /></td>
                            <td><input type="text" name="reward_item_success_rate_<?=$i?>" value="<?=$moduleConfig['reward_item_success_rate_'.$i]?>" class="form-control" /></td>
                            <td><textarea class="form-control" name="reward_item_option_<?=$i?>" rows="1"><?=$moduleConfig['reward_item_option_'.$i]?></textarea></td>
                        </tr>
                    <?
                    }?>
                    <tr>
                        <td colspan="5">
                            <div style="text-align:center">
                                <button type="submit" name="submit" value="submit" class="btn btn-success col-md-2">保存
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

<?php

}catch (Exception $exception){
        message('error',$exception->getMessage());
} ?>



