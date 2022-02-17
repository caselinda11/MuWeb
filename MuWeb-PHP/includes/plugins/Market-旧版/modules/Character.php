<?php
/**
 * 交易市场插件-卖模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
    ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>Market">交易市场</a></li>
            <li class="breadcrumb-item active" aria-current="page">角色市场</li>
        </ol>
    </nav>
    <?php
try {
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('char');
    $market->MarketMenu();
    $market->marketSellMenu();
    if(!$moduleConfig['active']) throw new Exception('暂未开放角色交易市场！');
    if($moduleConfig['active_login']) if(!isLoggedIn()) throw new Exception('该模块仅限会员可见，请先<a href="'.__BASE_URL__.'login">登陆账号</a>！');
    try {
        if(check_value($_REQUEST['request'])){
            if (!Validator::Number($_REQUEST['request'])) throw new Exception('数据错误，禁止站外提交！');
            $market->setCharSellOff($_SESSION['group'], $_SESSION['username'],$_REQUEST['request']);
        }
    } catch(Exception $ex) {
        message('error', $ex->getMessage());
    }
    $list = $market->getMarketCharList();
    #分页需求
    $page = ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) ? ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) : 1;
    $pageLength = 25; //每页显示多少条
    $totalPage = ((count($list)-1) / $pageLength) >= 1 ? ((count($list)-1) / $pageLength) : 1 ;    //总页数 (数据总数/每页显示多少条)
    $list = array_slice($list,($pageLength*($page-1)));
    $pager = new pagination($totalPage,$page,5,__PLUGIN_MARKET_HOME__.'Character?servercode='.$server.'&class='.$sClass.'&price='.$sPrice.'&char_name='.$char_name,2);

    ?>
    <div class="card mb-3">
    <div class="mt-3 mb-3">
    <table id="item" class=" table table-striped table-bordered text-center">

        <thead>
        <tr>
            <th>单号</th>
            <th>角色</th>
            <th>价格</th>
            <th>背包</th>
        </tr>
        </thead>
        <tbody>
        <?if(empty($list)){?><tr><td colspan="10"><strong>暂无数据</strong></td></tr><?}?>
        <?
        $Character = new Character();
        $i = ($pageLength*($page-1))+1;
        foreach ($list as $key=>$sellData){
            if($key == $pageLength) break;
            $group = getGroupIDForServerCode($sellData['servercode']);
            $data = $Character->getCharacterDataForCharacterName($group,$sellData['name']);
            if (!is_array($data)) continue;
            if(!$data['CtlCode']) continue;
        ?>
            <tr>
                <td><?=$sellData['ID']?></td>
                <td>
                    <div><?=getPlayerClassAvatar($data['Class'], true, true, 'align-self-center rounded','80');?></div>
                    <div><strong><?=playerProfile($sellData['servercode'],$data['Name'])?></strong>[<?=getPlayerClassName($data['Class'])?>]</div>
                    <div>等级<sup class="text-danger">大师</sup>: <strong><?=$data['cLevel']?><sup class="text-danger"><?=$data['mLevel']?></sup></strong></div>
                    <div>大区:[<?=getGroupNameForGroupID($group)?>]</div>
                </td>
                <td>
                    <div>角色状态:[<strong><?=getPkLevel($data['PkLevel'])?></strong>]</div>
                    <div>持有金币:[<strong><?=number_format($data['Money'])?></strong>]金</div>
                    <div>售价:<span class="text-danger"><b><?=$sellData['price']?><?=getPriceType($sellData['price_type'])?></b></span></div>
                    <div>
                        <?if($data['AccountID'] == $_SESSION['username']){?>
                            <a href="<?=__BASE_URL__?>Market/Character/<?=$sellData['ID']?>" class="col-md-5 btn btn-primary">下架</a>
                        <?}else{?>
                            <a href="<?=__BASE_URL__?>Market/CharacterBuy/<?=$sellData['ID']?>" class="col-md-5 btn btn-success">购买</a>
                        <?}?>
                    </div>
                </td>
                <?
                    if(class_exists('Plugin\equipment')){
                    $itemClass = new \Plugin\equipment();
                    $InventoryData = $itemClass->setEquipmentCode($data['Inventory']);
                    ?>
                    <td>
                        <div id="InventoryM">
                            <?/*武器*/?>  <div id="in_weapon"    class="data-info" data-info="<?=$InventoryData[0];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[0]);?>) no-repeat center center;"></div>
                            <?/*盾牌*/?>  <div id="in_shield"    class="data-info" data-info="<?=$InventoryData[1];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[1]);?>) no-repeat center center;"></div>
                            <?/*头盔*/?>  <div id="in_helm"      class="data-info" data-info="<?=$InventoryData[2];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[2]);?>) no-repeat center center;"></div>
                            <?/*铠甲*/?>  <div id="in_armor"     class="data-info" data-info="<?=$InventoryData[3];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[3]);?>) no-repeat center center;"></div>
                            <?/*护腿*/?>  <div id="in_pants"     class="data-info" data-info="<?=$InventoryData[4];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[4]);?>) no-repeat center center;"></div>
                            <?/*护手*/?>  <div id="in_gloves"    class="data-info" data-info="<?=$InventoryData[5];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[5]);?>) no-repeat center center;"></div>
                            <?/*靴子*/?>  <div id="in_boots"     class="data-info" data-info="<?=$InventoryData[6];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[6]);?>) no-repeat center center;"></div>
                            <?/*翅膀*/?>  <div id="in_wings"     class="data-info" data-info="<?=$InventoryData[7];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[7]);?>) no-repeat center center;background-size: cover;"></div>
                            <?/*守护*/?>  <div id="in_zoo"       class="data-info" data-info="<?=$InventoryData[8];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[8]);?>) no-repeat center center;"></div>
                            <?/*项链*/?>  <div id="in_pendant"   class="data-info" data-info="<?=$InventoryData[9];?>"   style="background: url(<?=$itemClass->ItemsUrl($InventoryData[9]);?>) no-repeat center center;"></div>
                            <?/*左戒指*/?><div id="in_ring1"     class="data-info" data-info="<?=$InventoryData[10];?>"  style="background: url(<?=$itemClass->ItemsUrl($InventoryData[10]);?>) no-repeat center center;"></div>
                            <?/*右戒指*/?><div id="in_ring2"     class="data-info" data-info="<?=$InventoryData[11];?>"  style="background: url(<?=$itemClass->ItemsUrl($InventoryData[11]);?>) no-repeat center center;"></div>
                        </div>
                    </td>
                <?}else{?>
                    <td><center class="text-danger"><?=message('info',"无法查看装备")?></center></td>
                <?}?>
            </tr>
        <?$i++;}?>
        </tbody>
    </table>
        <div class="mt-3 mb-2">
            <?=$pager->showpager()?>
        </div>
    </div>
    </div>
    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}