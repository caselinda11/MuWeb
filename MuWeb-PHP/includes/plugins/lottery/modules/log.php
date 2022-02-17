<?php
/**
 * 文件说明
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
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp/lotteryExtract">在线夺宝</a></li>
            <li class="breadcrumb-item active" aria-current="page">夺宝记录</li>
        </ol>
    </nav>
<?php
try{
    if (!isLoggedIn()) redirect(1, 'login');
    $lottery = new \Plugin\lottery();
    $tConfig = $lottery->loadConfig('config');
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    if(check_value($_REQUEST['state'])) if(!Validator::Number($_REQUEST['state'])) throw new  Exception('出错了，请您重新输入！');
    $data = $lottery->getLotteryLog(true,$_SESSION['username'],$_REQUEST['state']);
//    if(!is_array($data)) throw new Exception("暂无您的夺宝记录!");
    try{
        if(check_value($_POST['submit'])){
            if(!check_value($_POST['id'])) throw new  Exception('出错了，请您重新输入！');
            if(!Validator::Number($_POST['id'])) throw new  Exception('出错了，请您重新输入！');
            if(!Token::checkToken('Lottery'.$_POST['id'])) throw new  Exception('出错了，请您重新输入！');
            $lottery->setLotteryReceive($_SESSION['group'],$_SESSION['username'],$_POST['id']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    message('info','领取奖励前请确保仓库有足够的位置存放该物品！','提示: ');
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>夺宝记录</strong>
            <select id="state" onchange="window.location='?state='+this.value+''" class="form-control" style="width: 200px;">
                <option value="0" <?if($_REQUEST['state']==0) echo 'selected'?>>显示全部</option>
                <option value="1" <?if($_REQUEST['state']==1) echo 'selected'?>>显示未领取</option>
                <option value="2" <?if($_REQUEST['state']==2) echo 'selected'?>>显示已领取</option>
            </select>
        </div>

        <div class="">

                <table class="table table-striped table-hover text-center table-bordered">
                    <tbody>
                    <tr>
                        <th>中奖物品</th>
                        <th>中奖时间</th>
                        <th>领奖时间</th>
                        <th>状态</th>
                    </tr>
                    <?if(is_array($data)){?>
                        <?foreach ($data as $log){
                            $status = ($log['status']) ? '<button type="submit" value="submit" name="submit" class="btn btn-danger" disabled>已领</button>' : '<button type="submit" value="submit" name="submit" class="btn btn-success">领取</button>';
                            $disabled =  ($log['status']) ? 'disabled': '';
                            $receiveTime = ($log['receive_date']) ? date('Y-m-d h:i',strtotime($log['receive_date'])) : '';
                            if($log['win_code'] == '水晶') continue;
                            $item = new \Plugin\equipment();
                            $itemName = "<div><img src='".$item->ItemsUrl($log['win_code'])."' alt='' width='35' height='35' /></div>";
                            ?>
                        <form action="" method="post">
                            <tr>
                                <!--
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="checkboxVal" value="<?=$log['ID']?>" id="checkboxVal-<?=$log['ID']?>" class="custom-control-input j-batch_receive" <?=$disabled?>>
                                        <label for="checkboxVal-<?=$log['ID']?>" class="custom-control-label"></label>
                                    </div>
                                </td>
                                -->
                                <td><?=$itemName?></td>
                                <td><?=date('Y-m-d h:i',strtotime($log['date']))?></td>
                                <td><?=$receiveTime?></td>
                                <td>
                                    <input type="hidden" name="id" value="<?=$log['ID']?>" />
                                    <input type="hidden" name="key" value="<?=Token::generateToken('Lottery'.$log['ID'])?>" />
                                    <?=$status?>
                                </td>
                            </tr>
                        </form>
                        <?}?>
                    <?}else{?>
                        <tr><td colspan="4"><strong>暂无记录</strong></td></tr>
                    <?}?>
                    </tbody>
                </table>

        </div>
    </div>
    <!--
    <script>
        // 全选
        // $('input[name="selectAll"]').on("click",function(){
        //     if($(this).is(':checked')){
        //         $('input[name="checkboxVal"]').each(function(){
        //             if ( !$(this).attr('disabled') ){
        //                 $(this).prop("checked",true);
        //             }
        //         });
        //     }else{
        //         $('input[name="checkboxVal"]').each(function(){
        //             $(this).prop("checked",false);
        //         });
        //     }
        // });
        //
        // let isLoading = false;
        //
        // function submit(id) {

            // obj = document.getElementsByName("checkboxVal");
            // check_val = [];
            //
            // for (k in obj) {
            //     if (obj[k].checked) {
            //         check_val.push(obj[k].value);
            //     }
            // }
            //
            // var checkboxVal;
            // if( id === -1){
            //     checkboxVal = check_val;
            // }else {
            //     checkboxVal = id;
            // }
            //
            // if (checkboxVal =='') {
            //     modal_msg('您还没有选择要领取的对象。');
            //     return false
            // }

            if (isLoading === false) {
                // isLoading = true;
                // $('.j-batch_receive').addClass('disabled');
                // $('.j-batch_receive').html('批量领取中...');
                // console.log(checkboxVal);
                //axios.patch('<?//=__BASE_URL__?>//usercp/lotteryLog' + checkboxVal)
                //    .then(function (response) {
                //        isLoading = false;
                //        modal_url(response.data.message,'<?//=__BASE_URL__?>//usercp/lotteryLog?state=all');
                //    })
                //    .catch(function (error) {
                //        isLoading = false;
                //        $('.j-batch_receive').removeClass('disabled');
                //        $('.j-batch_receive').html('批量领取');
                //        $.each(error.response.data.errors, function (index, object) {
                //            modal_msg(object[0]);
                //            return false;
                //        });
                //    })
            }
        }
    </script>
    -->
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
