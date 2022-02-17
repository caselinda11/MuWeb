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
                        <li class="breadcrumb-item active">外观管理</li>
                        <li class="breadcrumb-item active">模版设置</li>
                    </ol>
                </div>
                <h4 class="page-title">模版主题</h4>
            </div>
        </div>
    </div>
<?php

try {
    try {
        if (check_value($_GET['id'])) {
            if(!check_value($_GET['id'])) throw new Exception('错误,请重新选择!');
            $TempLatePath = getDirectoryListFromPath(__PATH_TEMPLATES__);
            if (!key_exists($_GET['id'],$TempLatePath)) throw new Exception('无法识别美化,请检查该主题目录是否存在!');
            #主题目录
            $thisPath = __PATH_TEMPLATES__.$TempLatePath[$_GET['id']].'/';
            if (!file_exists($thisPath.'index.php')) throw new Exception('无法识别美化,请检查该主题目录是否存在!');
            $config = loadConfig('system');
            $config['website_template'] = $TempLatePath[$_GET['id']];
            # 转码
            $newSystemConfig = json_encode($config, JSON_PRETTY_PRINT);
            $cfgFile = fopen(__PATH_INCLUDES_CONFIGS__ . 'system.json', 'w');
            if (!$cfgFile) throw new Exception('打开配置文件时出现错误！');

            fwrite($cfgFile, $newSystemConfig);
            fclose($cfgFile);
            message('success', '主题切换成功!!');
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    $fileDir = getDirectoryListFromPath(__PATH_TEMPLATES__);
    if(!is_array($fileDir)) throw new Exception("无法识别美化目录。");
    ?>
    <div class="row">
    <?if(is_array($fileDir)){
    foreach ($fileDir as $id=>$dirName){
        $path = __PATH_TEMPLATES__.$dirName.'/templates.jpg';
        $publicPath = __BASE_URL__.'templates/'.$dirName.'/templates.jpg';
        $imgFile = (file_exists($path)) ? $publicPath : 'assets/images/small/img-4.jpg';
        #使用中
        $config = webConfigs();
        $active = ($config['website_template'] == $dirName) ? 'style="box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);"' : '';
        $btn = ($config['website_template'] == $dirName) ? '<a href="" class="btn btn-danger waves-effect waves-light col-md-12">使用中</a>' : '<a href="'.admincp_base("Settings_TempLate&id=".$id).'" class="btn btn-success waves-effect waves-light col-md-12">切换</a>';
        ?>

        <div class="col-md-4 col-lg-4 col-xl-3">
            <!-- Simple card -->
            <div class="card" <?=$active?>>
                <div style="text-align: center;overflow: hidden;width: 100%;height: 250px;">
                    <img class="card-img img-fluid" src="<?=$imgFile?>" alt="Card image cap">
                </div>
                <div class="card-img-overlay">
                    <h4 class="card-title text-white font-20 mt-0"><?=$dirName?></h4>
                </div>
                <div class="card-body">
                    <div class="card-avatar">
                        <a class="card-thumbnail card-inner" href="#">
                            <img class="rounded-circle img-thumbnail img-fluid" src="assets/images/users/avatar-1.jpg" alt="Teddy Wilson">
                        </a>
                    </div>
                    <h6 class="card-title">由 mason X 提供</h6>
                    <p class="card-text"></p>
                    <?=$btn?>
                </div>
            </div>
        </div>
        <?
    }
    }
?>
    </div>
<?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}

