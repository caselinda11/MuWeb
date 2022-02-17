<?php
/**
 * 插件安装
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
                    <li class="breadcrumb-item active">导入插件</li>
                </ol>
            </div>
            <h4 class="page-title">导入插件</h4>
        </div>
    </div>
</div>
<?php
try {
    if (check_value($_POST['submit'])) {
        if ($_FILES["file"]["error"] > 0) {
            message('error', '上传文件时出错!');
        } else {
            $Plugin = new Plugins();
            $Plugin->importPlugin($_FILES);
        }
    }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
    ?>
    <div class="card">
        <div class="card-header">选择文件</div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-group">

                    <input type="file" name="file" id="file" class="dropify"/>
                </div>
                <div class="text-center m-t-15">
                    <button type="submit" name="submit" value="submit"
                            class="btn btn-success waves-effect waves-light col-md-2">安装
                    </button>
                </div>

            </form>
        </div>
    </div>