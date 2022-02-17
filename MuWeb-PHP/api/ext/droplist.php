<?php
define('access', 'api');

include('../../includes/Init.php');
// if(substr($_SERVER["HTTP_REFERER"],7,strlen($_SERVER['SERVER_NAME'])) != $_SERVER['SERVER_NAME']) exit(404);
$drop = new \Plugin\mapDrop();

$id=$_GET["map"];   //申明GET名

$mapData =  $drop->getMapData($id);

echo '<table id="search" class="table rankings-table table-hover text-center">';
echo '<thead>';
echo '<tr>';
echo '<th style="font-weight:bold;text-align:center" >物品</th>';
echo '<th style="font-weight:bold;text-align:center">等级</th>';
echo '<th style="font-weight:bold;text-align:center">技能</th>';
echo '<th style="font-weight:bold;text-align:center">幸运</th>';
echo '<th style="font-weight:bold;text-align:center">追加</th>';
echo '<th style="font-weight:bold;text-align:center">卓越</th>';
echo '<th style="font-weight:bold;text-align:center">元素</th>';
echo '<th style="font-weight:bold;text-align:center">镶嵌</th>';
echo '<th style="font-weight:bold;text-align:center">怪物</th>';
echo '</tr>';
echo '</thead>';
echo '<tdoby>';
foreach ($mapData as $key=>$item){
    $item = $item['@attributes'];
    $itemName = $item['Name'];
    if(class_exists('Plugin\equipment')) {
        $itemClass = new \Plugin\equipment();
        try {
            $itemName = $itemClass->getItem($item['Cat'], $item['Index']);
        } catch (Exception $e) {
            $itemName = $item['Name'];
        }
    }
    echo '<tr>';
        echo '<td>'.$itemName['Name'].'</td>';
        echo '<td>'.$item['MinLevel'].'~'.$item['MaxLevel'].'</td>';
        echo '<td>'.$drop->setItemSkill($item['Skill']).'</td>';
        echo '<td>'.$drop->setItemLucky($item['Luck']).'</td>';
        echo '<td>'.$drop->setItemOption($item['Option']).'</td>';
        echo '<td>'.$drop->setItemExc($item['Exc']).'</td>';
        echo '<td>'.$drop->setItemElement($item['Element']).'</td>';
        echo '<td>'.$drop->setItemSocketCount($item['SocketCount']).'</td>';
        echo '<td>'.$drop->getMonsterName($item['MonsterID']).'</td>';
    echo '</tr>';
}
echo '</tdoby>';
echo '</table>';
?>