<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<?php
try {
    try {
        if (check_value($_POST['Submit'])) {
            debug(Post($_POST, 'http://127.0.0.1/api/pay.php'));
        }
    } catch (Exception $e) {
        message('error', $e->getMessage());
    }
    ?>
    <script type="text/javascript" src="<?=__PATH_PUBLIC_JS__?>jquery.qrcode.min.js"></script>
    <div class="card">

        <?
        $t = new trading();
//        $out_trade_no = "TOPAY".date('Ymdhis').mt_rand(100,1000);
//        $t->toPay($out_trade_no,"fukpim@hotmail.com","向福平","800","测试转账");
        ?>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" id="market-items" data-toggle="modal" data-target="#exampleModal">
            购买
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" data-backdrop="static" aria-labelledby="exampleModalLabelPay" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content" style="border:unset">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelPay">传说护手[30.00]</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="pay-content">
                        <div class="card bg-primary text-center " style="background:unset;box-shadow:unset">
                            <div class="text-white font-weight-bold" style="font-size:20px">打开支付宝[扫一扫]</div>
                            <div class="align-self-center bg-white" style="text-align:center;width: 220px;height: 220px">
                                <div class="app-qr p-2"></div>
                            </div>
                            <div class="text-white">您有<span class="time">2</span>分钟时间完成该笔订单交易</div>
                        </div>
                        <script>
                            $("#market-items").click(function(){
                                $(".app-qr").empty();
                                $.ajax({
                                    url: baseUrl + "api/trading.php",
                                    data:{},
                                    type:"POST",
                                    dataType:"JSON",
                                    success:function (data) {
                                        if(data.code === "10000"){
                                            $(".app-qr").qrcode({
                                                render:"canvas",
                                                width:200,
                                                height:200,
                                                text:data.url
                                            });
                                        }else{
                                            $("#exampleModalLabelPay").text("ERROR:"+data.code);
                                            $(".pay-content").text(data.msg);
                                        }
                                }});
                            });
                        </script>
                    </div>
                    <div class="modal-footer" style="justify-content: center;">
                        <button type="button" class="btn btn-success">已付款</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">不要了</button>
                    </div>
                </div>
            </div>
        </div>


        <form class="form-horizontal mt-3" action="" method="post">
            <div class="form-group row justify-content-md-center">
                <div class="col-md-5">账号<input type="text" class="form-control" value="ceshi4" name="User"></div>
                <div class="col-md-5">商户<input type="text" class="form-control" value="43" name="Pid"></div>
                <div class="col-md-5">KEY<input type="text" class="form-control"
                                                value="eebc51091195d7661c9f0576b40fb148" name="S"></div>
                <div class="col-md-5">订单<input type="text" class="form-control" value="1" name="Ordid"></div>
                <div class="col-md-5">货币<input type="text" class="form-control" value="102" name="TypeId"></div>
                <div class="col-md-5">区服<input type="text" class="form-control" value="831" name="Area"></div>
                <div class="col-md-5">金额<input type="text" class="form-control" value="1000" name="All"></div>
                <div class="col-md-5">比例<input type="text" class="form-control" value="1000" name="Point"></div>
                <br>
                <div class="col-md-12 mt-3 text-center">
                    <button type="submit" name="Submit" value="Submit" class="btn btn-success col-sm-5">
                        提交
                    </button>
                </div>
            </div>
        </form>
    </div>
    <?php
} catch (Exception $exception) {
    message('error', $exception->getMessage());
}
