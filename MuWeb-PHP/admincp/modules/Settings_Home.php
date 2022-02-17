<?php
/**
 * 主页设置
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
                    <li class="breadcrumb-item active">赞助设置</li>
                </ol>
            </div>
            <h4 class="page-title">赞助设置</h4>
        </div>
    </div>
</div>
<?php
$file = __PATH_INCLUDES_CONFIGS__."settings_home.txt";
$content = getTextContent($file);

if($_POST['submit']){
    //	写入文件
    setTextContent($file,$_POST['content']);
    redirect(2, 'admincp/?module=Settings_Home',1);
}
?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    <strong>[提示]</strong>可直接手动编辑文本格式<code>include/config/Settings_home.txt
</div>
<div class="card">
    <div class="card-body">
        <form role="form" method="post">
            <div class="form-group">
                <label for="content"></label>
                <textarea id="content" name="content"><?=$content; ?></textarea>
            </div>
            <div class="form-group row justify-content-md-center">
                <button type="submit" name="submit" value="ok" class="btn btn-success col-md-4">发布</button>
            </div>
        </form>
    </div>
</div>
<script src="ckeditor/ckeditor.js"></script>
<script type="text/javascript">//<![CDATA[
    CKEDITOR.replace('content', {
        height: 500,
        language: 'zh-cn',
        uiColor: '#f1f1f1'
    });
    //]]></script>