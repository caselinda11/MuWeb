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
                        <li class="breadcrumb-item active">设置网站公告</li>
                    </ol>
                </div>
                <h4 class="page-title">设置网站公告</h4>
            </div>
        </div>
    </div>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>使用说明</strong>
            设置网站公告，网站将弹出一个窗口提示网站公告
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
$file = __PATH_INCLUDES_CONFIGS__."web_code.txt";
$content = getTextContent($file);
try {
    if($_POST['submit']){
        //	写入文件
        setTextContent($file,$_POST['message']);
        redirect(2, 'admincp/?module=Send_WebPost',1);
    }
?>
    <div class="card">
        <div class="card-header">设置网站公告</div>
        <div class="card-body">
            <form role="form" method="post">
                <div class="form-group row justify-content-md-center">
                    <div class="col-md-8">
                        <label class="sr-only" for="message"></label>
                        <textarea class="form-control" id="message" name="message" style="height: 500px;"><?=$content?></textarea>
                    </div>
                </div>
            <div class="form-group row justify-content-md-center">
                <button type="submit" name="submit" value="ok" class="btn btn-success col-md-4">发布</button>
            </div>
            </form>
        </div>
    </div>
<?php
}catch (Exception $ex){
    message('error', $ex->getMessage());
}