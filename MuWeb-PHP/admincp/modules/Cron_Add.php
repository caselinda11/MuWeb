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
                    <li class="breadcrumb-item active">定时任务</li>
                    <li class="breadcrumb-item active">定时任务计划</li>
                </ol>
            </div>
            <h4 class="page-title">定时任务计划</h4>
        </div>
    </div>
</div>
<?
try {
    $cron_times = commonCronTimes();

    if (check_value($_POST['add_cron'])) {
        addCron();
    }
    ?>
    <div class="card">
        <div class="card-header">创建定制任务程序</div>
        <div class="card-body mb-3">
            <div class="col-md-12">
                <form method="post">
                    <div class="row justify-content-center">
                        <div class="form-group col-md-3">
                            <label for="input_1" class="col-form-label">定时任务:</label>
                            <input type="text" class="form-control" id="input_1" name="cron_name"/>
                            <div class="form-text text-muted">仅作为后台名称显示作用</div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-md-3">
                            <label for="input_2" class="col-form-label">描述:</label>
                            <input type="text" class="form-control" id="input_2" name="cron_description"/>
                            <span class="form-text text-muted">仅作为后台显示描述作用</span>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-md-3">
                            <label for="input_3" class="col-form-label">文件:</label>
                            <select class="form-control" id="input_3" name="cron_file">
                                <?=listCronFiles();?>
                            </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-md-3">
                            <label for="input_4" class="col-form-label">间隔时间:</label>
                            <select class="form-control" id="input_4" name="cron_time">
                                <? if (is_array($cron_times)) {
                                    foreach ($cron_times as $seconds => $description) { ?>
                                        <option value="<?= $seconds; ?>"><?= $description; ?></option>
                                        <?
                                    }
                                } else { ?>
                                    <option value="300">5分钟</option>
                                <? } ?>
                            </select>
                            <span class="form-text text-muted">后台自动缓存数据的时间</span>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-md-3 mt-2">
                            <button type="submit" name="add_cron" value="Add" class="btn btn-success col-md-12">添加</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}