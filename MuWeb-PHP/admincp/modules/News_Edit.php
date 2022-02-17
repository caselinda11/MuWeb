<?php
/**
 * 新闻编辑
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
                        <li class="breadcrumb-item"><a href="<?=admincp_base()?>">官方主页</a></li>
                        <li class="breadcrumb-item"><a href="<?=admincp_base()?>?module=News_Manage">新闻管理</a></li>
                        <li class="breadcrumb-item active">新闻编辑</li>
                    </ol>
                </div>
                <h4 class="page-title">新闻编辑</h4>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
<?php
try {
    $News = new News();
    loadModuleConfigs('news');

// 检查新闻缓存文件夹是否可写
    if (!$News->checkNewsDirWritable())  throw new Exception('新闻缓存文件夹不可写。');
        // 编辑新闻流程::
        if (check_value($_POST['news_submit'])) {
            try{
                $News->editNews($_REQUEST['id'], $_POST['news_title'], $_POST['title_color'], $_POST['sort'], $_POST['news_type'], $_POST['type_color'], $_POST['news_content'], $_POST['news_author'], 0, $_POST['news_date'], $_POST['status']);
                $News->cacheNews();
                $News->updateNewsCacheIndex();
                redirect(1, 'admincp/?module=News_Manage');
            }catch (Exception $e){
                message('error',$e->getMessage());
            }
        }

        // 加载新闻
        $editNews = $News->loadNewsData($_REQUEST['id']);

        if ($editNews) {
            ?>

                    <div class="card">
                        <div class="card-header">编辑新闻</div>
                        <div class="card-body">
                            <form role="form" method="post">
                                <input type="hidden" id="news_id" name="news_id" value="<?= $editNews['news_id']; ?>"/>

                                <div class="form-row align-items-center mb-3">
                                    <div class="col-md-4 input-group">
                                        <div class="input-group-prepend"><label class="input-group-text" for="news_title">新闻标题:</label></div>
                                        <input type="text" class="form-control" id="news_title" name="news_title" value="<?= $editNews['news_title']; ?>"/>

                                    </div>
                                    <div class="col-md-4 input-group">
                                        <div class="input-group-prepend"><label class="input-group-text" for="title_color">标题颜色:</label></div>
                                        <input type="text" name="title_color" id="title_color" class="title_color form-control" value="<?=$editNews['title_color']?>" style="border-top-left-radius: 0;border-bottom-left-radius: 0;"/>
                                    </div>
                                    <div class="col-md-2 input-group">
                                        <div class="input-group-prepend"><label class="input-group-text" for="sort">排序:</label></div>
                                        <input type="number" class="form-control" id="sort" name="sort" value="<?=$editNews['sort']; ?>"/>
                                    </div>
                                    <div class="col-md-2 input-group">
                                        <div class="input-group-prepend"><label class="input-group-text" >状态:</label></div>
                                        <select class="form-control" name="status" >
                                            <option value="0" <?=selected($editNews['status'],0)?>>隐藏</option>
                                            <option value="1" <?=selected($editNews['status'],1)?>>显示</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row align-items-center">
                                    <div class="col-md-3 input-group">
                                        <div class="input-group-prepend"><label class="input-group-text" for="news_type">新闻类型:</label></div>
                                        <select class="form-control" name="news_type" id="news_type">
                                            <? foreach (newsType() as $key => $value) { ?>
                                                <option value="<?=$key?>" <?=selected($editNews['news_type'],(string)$key)?>><?=$value?></option>
                                            <?}?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 input-group">
                                        <div class="input-group-prepend"><label class="input-group-text" for="type_color">类型颜色:</label></div>
                                        <input type="text" name="type_color" id="type_color" class="type_color form-control" value="<?= $editNews['type_color'] ?>" style="border-top-left-radius: 0;border-bottom-left-radius: 0;"/>
                                    </div>

                                    <div class="col-md-3 input-group">
                                        <div class="input-group-prepend"><label for="news_author" class="input-group-text">作者:</label></div>
                                        <input type="text" class="form-control" id="news_author" name="news_author" value="<?= $editNews['news_author']; ?>"/>
                                    </div>
                                    <div class="col-md-3 input-group">
                                        <div class="input-group-prepend"><label for="news_date" class="input-group-text">更新日期:</label></div>
                                        <input type="text" class="form-control" id="news_date" name="news_date" value="<?=$editNews['news_date']?>"/>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="news_content"></label>
                                    <textarea name="news_content" id="news_content"><?= $editNews['news_content']; ?></textarea>
                                </div>

                                <div class="form-group row justify-content-md-center">
                                    <button type="submit" class="btn btn-success col-md-4" name="news_submit" value="ok">更新</button>
                                </div>
                            </form>
                        </div>
                    </div>

            <script src="ckeditor/ckeditor.js"></script>
            <script type="text/javascript">//<![CDATA[
                CKEDITOR.replace('news_content', {
                    height: 500,
                    language: 'zh-cn',
                    uiColor: '#f1f1f1'
                });
                //]]></script>
            <?php
        } else {
            message('error', '无法加载新闻数据!');
        }
}catch (Exception $exception){
    message('error', $exception->getMessage());
}
?>