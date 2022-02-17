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
            <li class="breadcrumb-item active" aria-current="page">物品检测</li>
        </ol>
    </nav>
<?php
if(class_exists('Plugin\equipment')) {
    echo '<div class="card mb-3">';
    try {
        $itemClass = new \Plugin\equipment();
        $itemClass->parse_item_txt();
        if(is_array($itemClass->info)){
            echo '<table class="table table-bordered text-center" style="width:100%">';
                echo '<thead>';
                    echo '<tr>';
                        echo '<th>总编号</th>';
                        echo '<th>编号(大-小)</th>';
                        echo '<th>物品名称</th>';
                        echo '<th>Slot</th>';
                        echo '<th>Option</th>';
                        echo '<th>宽度</th>';
                        echo '<th>高度</th>';
                    echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach($itemClass->info as $key=>$data){
                    foreach($data as $tData){
                        echo '<tr>';
                        echo '<td>'.($key * 512 + $tData['Index']).'</td>';
                        echo '<td>'.$key.'-'.$tData['Index'].'</td>';
                        echo '<td>'.$tData['Name'].'</td>';
                        echo '<td>'.$tData['Slot'].'</td>';
                        echo '<td>'.$tData['Option'].'</td>';
                        echo '<td>'.$tData['Width'].'</td>';
                        echo '<td>'.$tData['Height'].'</td>';
                        echo '</tr>';
                    }
                }
                echo '</tbody>';
            echo '</table>';
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    echo '</div>';
}