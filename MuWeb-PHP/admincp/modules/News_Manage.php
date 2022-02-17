<?php
/**
 * 新闻管理模块
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
					<li class="breadcrumb-item active">新闻管理</li>
				</ol>
			</div>
			<h4 class="page-title">新闻管理</h4>
		</div>
	</div>
</div>
<?php
try{
    $News = new News();
    if(!$News->checkNewsDirWritable()) throw new Exception("新闻缓存文件夹不可写!");
	# 新闻删除
	if(check_value($_REQUEST['delete'])) {
	    try{
            $deleteNews = $News->removeNews($_REQUEST['delete']);
            $News->cacheNews();
            $News->updateNewsCacheIndex();
            if($deleteNews) {
                redirect(1, 'admincp/?module=News_Manage');
            } else {
                message('error','无效的新闻ID!');
            }
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}

	#新闻状态
	if(check_value($_GET['edit']) && check_value($_GET['status'])) {
    try {
        $News->setId($_GET['edit']);
        if($News->setStatus($_GET['status']))
            redirect(1, 'admincp/?module=News_Manage');
        else
            message('error','无效的新闻ID!');
    } catch(Exception $ex) {
        message('error', $ex->getMessage());
		}
	}

	# 新闻缓存
	if(check_value($_REQUEST['cache']) && $_REQUEST['cache'] == 1) {
	    try {
		$cacheNews = $News->cacheNews();
		$News->updateNewsCacheIndex();
            if($cacheNews) {
                message('success','新闻缓存保存成功!');
            } else {
                message('error','没有要保存的新闻!');
            }
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
?>
    <div class="row">
        <div class="col-sm-12">
            <div class="float-right mb-2">
                <a class="btn btn-primary btn-lg" href="<?php echo admincp_base("News_Manage&cache=1");?>">更新缓存</a>
                <a class="btn btn-success btn-lg" href="<?php echo admincp_base("News_Add");?>">发布新闻</a>
            </div>
        </div>
    </div>

                    <div class="card">
                        <div class="card-header">新闻列表</div>
                        <div class="card-body">
<?  $news_list = $News->getNewsList();
	if(is_array($news_list)) {?>
                            <table class="table text-center table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>排序</th>
                                        <th>类型</th>
                                        <th>标题</th>
                                        <th>作者</th>
                                        <th>日期</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
            <? foreach($news_list as $row) {
			    $News->setId($row['news_id']); ?>
                                    <tr>
                                        <td width="100px"><?=$row['sort']?></td>
                                        <?foreach (newsType() as $key=>$value){
                                            if($key == $row['news_type']){?>
                                                <td><font color="<?=$row['type_color']?>">[<?=$value?>]</font></td>
                                            <?}?>
                                        <?}?>
                                        <td width="40%"><a href="<?=__BASE_URL__?>news/content/<?=$row['news_id']?>" target="_blank"><font color="<?=$row['title_color']?>"><?=$row['news_title']?></font></a></td>
                                        <td><?=$row['news_author']?></td>
                                        <td width="130px"><?=date("Y-m-d",strtotime($row['news_date']))?></td>
                                        <td><a href="<?=admincp_base("News_Manage&edit=".$row['news_id']."&status=".$row['status'])?>"><?=$row['status'] ? '<span class="ion-toggle-filled font-32 text-success" data-toggle="tooltip" data-placement="left" title="点击隐藏"></span>' : '<span class="ion-toggle font-32" data-toggle="tooltip" data-placement="left" title="点击显示"></span>'?></a> </td>
                                        <td>
                                            <a class="btn btn-primary btn-xs pull-right" href="<?=admincp_base("News_Edit&id=".$row['news_id'])?>">编辑</a>
                                            <a class="btn btn-danger btn-xs pull-right" href="<?=admincp_base("News_Manage&delete=".$row['news_id'])?>">删除</a>
                                        </td>
                                    </tr>
                            <?}?>
                                </tbody>
                            </table>
                    <?}?>
                        </div>
                    </div>
<?
}catch (Exception $exception){
    message('error',$exception->getMessage());
}


