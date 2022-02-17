<?php
/**
 * 玩家个人资料页面
 *    [0]			  	#更新时间
 *    [1]		['Name'],            #名称
 *    [2]		['Class'],           #角色类
 *    [3]		['cLevel'],          #等级
 *    [4]		['mLevel'],          #大师
 *    [5]		['LevelUpPoint'],    #升级点
 *    [6]		['mPoint'],          #大师点
 *    [7]		['Strength'],        #力量
 *    [8]		['Dexterity'],       #敏捷
 *    [9]		['Vitality'],        #体力
 *    [10]		['Energy'],          #智力
 *    [11]		['Leadership'],      #统率
 *    [12]		['Inventory'],       #背包
 *    [13]		['MapNumber'],       #所在地图
 *    [14]		['PkLevel'],         #红名状态
 *    [15]		['CtlCode'],         #角色状态
 *    [16]		['GenFamily'],       #家族类型 0/1/2
 *    [17]		['GenRanking'],      #家族排名
 *    [18]		['GenRanking'],      #家族贡献
 *    [19]		['ConnectStat'],     #在线状态 0/1
 *    [20]		['IP'],              #联机IP
 *    [21]		['ServerName'],      #所在服务器
 *    [22]		['ConnectTM'],       #上线时间
 *    [23]		['DisConnectTM'],    #下线时间
 *    [24]                     #战盟名称
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>rankings/level">英雄排名</a></li>
        <li class="breadcrumb-item active" aria-current="page">玩家简要</li>
    </ol>
</nav>
<?php
try {
    loadModuleConfigs('profiles');
    if(!check_value($_GET['page'])) throw new Exception('您的请求无法完成，请再试一次。');
    if(!mconfig('active')) throw new Exception("该功能暂已关闭，请稍后尝试访问。");
    $weProfiles = new weProfiles();
    if(!Validator::UnsignedNumber($_GET['group']))throw new Exception('出错了，请您重新输入！');
    $weProfiles->setRequest($_GET['group'],$_GET['subpage'],$_GET['name']); # 传昵称
    $cData = $weProfiles->data();
    $onlineStatus = $cData[19] == 1 ? '<img src="'.__PATH_PUBLIC_IMG__.'on.gif" />' : '<img src="'.__PATH_PUBLIC_IMG__.'off.gif" />' ;
    global $custom;
    #特殊定制项目
    $showItems = 'data-info';
//            $rankingConfig = loadConfigurations('rankings');
//            if($rankingConfig['show_level_group'] == $_GET['group']){
//                $cData[3] = '*';
//                $cData[4] = '*';
//				$showItems = '';
//            }
    ?>
    <div class="card mb-3">
        <div class="card-header">[<?=$cData[1]?>] - 玩家简要</div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-4 align-self-center">
                    <div class="text-center mt-2 mb-2">
                        <img src="<?=__PATH_PUBLIC_IMG__?>Top/profile/Avatar/<?=$custom['character_class'][$cData[2]][2]?>" width="150" class=""/>
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless text-center">
                        <tr>
                            <th class="text-right">角色名</th>
                            <th class="text-left"><?=$cData[1]?><?=$onlineStatus?></th>
                        </tr>
                        <tr>
                            <th>角色类型</th>
                            <th><?=getPlayerClassName($cData[2])?></th>
                        </tr>
                        <?if(mconfig('show_level')){?>
                            <tr>
                                <th width="50%">等级</th>
                                <td><?=$cData[3]?></td>
                            </tr>
                            <tr>
                                <th>大师</th>
                                <td><?=$cData[4]?></td>
                            </tr>
                        <?}?>
                        <?if(mconfig('show_total_status')){?>
                            <tr>
                                <th>总属性点</th>
                                <td><?=$cData[5]+$cData[7]+$cData[8]+$cData[9]+$cData[10]+$cData[11]?></td>
                            </tr>
                        <?}?>
                        <?if(mconfig('show_location')){?>
                            <tr>
                                <th>所在地图</th>
                                <td><?=getMapName($cData[13])?></td>
                            </tr>
                        <?}?>
                        <tr>
                            <th>所属大区</th>
                            <td><?=getGroupNameForServerCode($_GET['group'])?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <footer class="blockquote-footer text-right mb-2 mr-3">
                最后更新时间
                <cite title="Source Title">
                    <?=date('Y-m-d h:i A',$cData[0])?>
                </cite>
            </footer>
        </div>
    </div>
    <?if(mconfig('show_guild')){?>
        <?if($cData[24]){?>
            <div class="card mb-3">
                <div class="card-header">战盟信息</div>
                <div class="">
                    <table class="table table-hover text-center">
                        <tr>
                            <th width="50%">所属战盟</th>
                            <td><?=guildProfile($_GET['group'],$cData[25])?></td>
                        </tr>
                        <tr>
                            <th>战盟盟主</th>
                            <td><?=playerProfile($_GET['group'],$cData[26])?></td>
                        </tr>
                        <tr>
                            <th>战盟人数</th>
                            <td><?=$cData[27]?></td>
                        </tr>
                    </table>
                </div>
            </div>
        <?}?>
    <?}?>
    <?if(mconfig('show_gens')){?>
        <div class="card mb-3">
            <div class="card-header">家族信息</div>
            <table class="table table-hover text-center">
                <tr>
                    <th width="50%">家族</th>
                    <td><?=getImgForGensTypeId($cData[17],$cData[18])?></td>
                </tr>
                <tr>
                    <th>称谓</th>
                    <td><?=getGensRank($cData[19])?></td>
                </tr>
            </table>
        </div>
    <?}?>
    <?if(mconfig('show_online')){?>
        <div class="card  mb-3">
            <div class="card-header">在线信息</div>
            <div class="">
                <table class="table table-hover text-center">
                    <tr>
                        <th width="50%">所在线路</th>
                        <td><?=$cData[22]?></td>
                    </tr>
                    <tr>
                        <th>最后上线时间</th>
                        <td><?=$cData[23]?></td>
                    </tr>
                    <tr>
                        <th>最后下线时间</th>
                        <td><?=$cData[24]?></td>
                    </tr>
                </table>
            </div>
        </div>
    <?}?>

    <div class="card mb-3">
        <div class="card-header">背包信息栏</div>
        <div class="card-body">
            <table class="table text-center">
                <tbody>
                <tr>
                    <?
                    if(class_exists('Plugin\equipment')){
                        $itemClass = new \Plugin\equipment();
                        $InventoryData = $itemClass->setEquipmentCode($cData[12]);
                        ?>
                        <td>
                            <div id="InventoryC">
                                <?/*头像*/?>  <?=getPlayerClassAvatar($cData[2], true, true, 'rounded-circle border border-success','110');?>
                                <?/*武器*/?>  <div id="in_weapon"    class="<?=$showItems?>" data-info="<?=$InventoryData[0]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[0]);?>) no-repeat center center;"></div>
                                <?/*盾牌*/?>  <div id="in_shield"    class="<?=$showItems?>" data-info="<?=$InventoryData[1]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[1]);?>) no-repeat center center;"></div>
                                <?/*头盔*/?>  <div id="in_helm"      class="<?=$showItems?>" data-info="<?=$InventoryData[2]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[2]);?>) no-repeat center center;"></div>
                                <?/*铠甲*/?>  <div id="in_armor"     class="<?=$showItems?>" data-info="<?=$InventoryData[3]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[3]);?>) no-repeat center center;"></div>
                                <?/*护腿*/?>  <div id="in_pants"     class="<?=$showItems?>" data-info="<?=$InventoryData[4]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[4]);?>) no-repeat center center;"></div>
                                <?/*护手*/?>  <div id="in_gloves"    class="<?=$showItems?>" data-info="<?=$InventoryData[5]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[5]);?>) no-repeat center center;"></div>
                                <?/*靴子*/?>  <div id="in_boots"     class="<?=$showItems?>" data-info="<?=$InventoryData[6]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[6]);?>) no-repeat center center;"></div>
                                <?/*翅膀*/?>  <div id="in_wings"     class="<?=$showItems?>" data-info="<?=$InventoryData[7]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[7]);?>) no-repeat center center;"></div>
                                <?/*守护*/?>  <div id="in_zoo"       class="<?=$showItems?>" data-info="<?=$InventoryData[8]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[8]);?>) no-repeat center center;"></div>
                                <?/*项链*/?>  <div id="in_pendant"   class="<?=$showItems?>" data-info="<?=$InventoryData[9]?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[9]);?>) no-repeat center center;"></div>
                                <?/*左戒指*/?><div id="in_ring1"     class="<?=$showItems?>" data-info="<?=$InventoryData[10]?>"  style="background: url(<?=$itemClass->ItemsUrl($InventoryData[10]);?>) no-repeat center center;"></div>
                                <?/*右戒指*/?><div id="in_ring2"     class="<?=$showItems?>" data-info="<?=$InventoryData[11]?>"  style="background: url(<?=$itemClass->ItemsUrl($InventoryData[11]);?>) no-repeat center center;"></div>
                                <?/*元素*/?>  <div id="in_pentagram" class="<?=$showItems?>" data-info="<?=$InventoryData[236]?>"  style="background: url(<?=$itemClass->ItemsUrl($InventoryData[236]);?>) no-repeat center center;"></div>
                                <?/*右耳环*/?><div id="in_ear1"      class="<?=$showItems?>" data-info="<?=$InventoryData[237]?>"  style="background: url(<?=$itemClass->ItemsUrl($InventoryData[237]);?>) no-repeat center center;"></div>
                                <?/*左耳环*/?><div id="in_ear2"      class="<?=$showItems?>" data-info="<?=$InventoryData[238]?>"  style="background: url(<?=$itemClass->ItemsUrl($InventoryData[238]);?>) no-repeat center center;"></div>
                            </div>
                        </td>
                    <?}else{?>
                        <center class="text-danger"><?=message('info','未启用装备显示功能')?></center>
                    <?}?>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php
} catch(Exception $e) {
    message('error', $e->getMessage());
}
?>
