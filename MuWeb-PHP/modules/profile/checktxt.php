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
            <li class="breadcrumb-item active" aria-current="page">文本检测</li>
        </ol>
    </nav>
<?php
if(class_exists('Plugin\equipment')) {
    try {
        function checkThisFile($file){
            if(empty($file)) return '<span class="badge badge-danger badge-pill">加载失败</span>';
            return '<span class="badge badge-success badge-pill">加载成功</span>';
        }
        $array=[
            ['祝福大天使属性加成文件','CustomAbsoluteBufferEX.txt'],
            ['卓越属性加成文件','CustomExcellentOption.txt'],
            ['附魔物品文件','CustomItemBuffer.txt'],
            ['附魔物品EX文件','CustomItemBufferEX.txt'],
            ['附魔物品EX属性文件 - 属性文本','TEXT_CustomItemBufferEX.txt'],
            ['翅膀附魔文件','CustomWingBuffer.txt'],
            ['翅膀附魔文件 - 属性文本','TEXT_CustomWingBuffer.txt'],
            ['翅膀附魔Lv2文件','CustomWingBuffer_LV2.txt'],
            ['380属性(PVP)与其他物品属性文件','ItemAddOption.txt'],
            ['全套属性','ItemCompleteSetUpEX.txt'],
            ['套装文件','itemsetoption(Kor).txt'],
            ['套装文件','itemsettype(Kor).txt'],
            ['技能文件','skill(Kor).txt'],
            ['镶嵌物品文件','SocketType.txt'],
        ];

        $itemClass = new \Plugin\equipment();
        ?>
    <div class="card mb-3">
        <div class="accordion" id="accordionExample">
            <?
                for ($i=0;$i<count($array);$i++){
                $custom[$i] = $itemClass->localServerTXT($array[$i][1]);
                ?>
            <div class="card mb-1">
                <div class="card-header" id="headingOne<?=$i?>">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseOne<?=$i?>" aria-expanded="true" aria-controls="collapseOne<?=$i?>">
                            <?=$array[$i][0]?>[<?=$array[$i][1]?>]
                            <?=checkThisFile($custom[$i])?>
                        </button>
                    </h2>
                </div>

                <div id="collapseOne<?=$i?>" class="collapse" aria-labelledby="headingOne<?=$i?>" data-parent="#accordionExample">
                    <div class="card-body">
                        <?debug($itemClass->localServerTXT($array[$i][1]))?>
                    </div>
                </div>
            </div>
            <?}?>
        </div>
    </div>
        <?php
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
}