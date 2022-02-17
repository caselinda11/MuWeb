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
                    <li class="breadcrumb-item active">充值日志</li>
                </ol>
            </div>
            <h4 class="page-title">充值日志</h4>
        </div>
    </div>
</div>
<?
try {
    $log = Connection::Database("Web")->query_fetch("SELECT * FROM [PayLog] ORDER BY [TransDate] DESC");
    if(!is_array($log)) throw new Exception("暂无充值日志。");
?>

        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">青蛙大区</div>
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="col-3 align-self-center">
                                <div class="round">
                                    <i class="mdi mdi-eye"></i>
                                </div>
                            </div>
                            <div class="col-9 align-self-center text-right">
                                <div class="m-l-10">
                                    <h5 class="mt-0">今日充值 18090元</h5>
                                    <p class="mb-0 text-muted">
                                        较比昨天
                                        <span class="badge bg-soft-success">
                                            <i class="mdi mdi-arrow-up"></i>2.35%
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height:3px;">
                            <div class="progress-bar  bg-success" role="progressbar" style="width: 35%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    <!--end card-body-->
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">蛤蟆大区</div>
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="col-3 align-self-center">
                                <div class="round">
                                    <i class="mdi mdi-eye"></i>
                                </div>
                            </div>
                            <div class="col-9 align-self-center text-right">
                                <div class="m-l-10">
                                    <h5 class="mt-0">今日充值 18090元</h5>
                                    <p class="mb-0 text-muted">
                                        较比昨天
                                        <span class="badge bg-soft-success">
                                            <i class="mdi mdi-arrow-up"></i>2.35%
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height:3px;">
                            <div class="progress-bar  bg-success" role="progressbar" style="width: 35%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">累计充值</div>
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="col-3 align-self-center">
                                <div class="round">
                                    <i class="mdi mdi-cart"></i>
                                </div>
                            </div>
                            <div class="col-9 align-self-center text-right">
                                <div class="m-l-10">
                                    <div>
                                        <span class="mt-0">青蛙总额 <strong>18090</strong> 元</span>
                                    </div>
                                    <div>
                                        <span class="mt-0">蛤蟆总额 <strong>18090</strong> 元</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height:3px;">
                            <div class="progress-bar  bg-success" role="progressbar" style="width: 100%;" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">蛤蟆大区</div>
                    <div class="card-body">

                    </div>
                </div>
            </div>
        </div>

    <div class="card">
        <div class="card-header">充值记录</div>
        <table class="table table-striped table-bordered table-hover text-center">
            <thead>
                <th>订单号</th>
                <th>账号</th>
                <th>区服</th>
                <th>货币</th>
                <th>金额</th>
                <th>点数</th>
                <th>赠送积分</th>
                <th>赠送元宝</th>
                <th>充值时间</th>
            </thead>
            <tbody>
            <?if(is_array($log)){?>
                <?foreach ($log as $data){?>
                    <tr>
                        <td><?=$data['OrdId']?></td>
                        <td><?=$data['memb___id']?></td>
                        <td><?=$data['Area']?></td>
                        <td><?=$data['TypeId']?></td>
                        <td><?=$data['Money']?></td>
                        <td><?=$data['Point']?></td>
                        <td><?=$data['Send_jf'] ? $data['Send_jf'] : 0?></td>
                        <td><?=$data['Send_yb'] ? $data['Send_jf'] : 0?></td>
                        <td><?=$data['TransDate']?></td>
                    </tr>
                <?}?>
            <?}?>
            </tbody>
        </table>
    </div>
<?
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
