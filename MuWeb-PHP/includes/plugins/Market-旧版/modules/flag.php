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
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>Market">交易市场</a></li>
        <li class="breadcrumb-item active" aria-current="page">旗帜市场</li>
    </ol>
</nav>
<?php
try {
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('flag');
    $market->MarketMenu();
    $market->marketSellMenu();
    if(!$moduleConfig['active']) throw new Exception('暂未开放旗帜交易市场！');
    if($moduleConfig['active_login']) if(!isLoggedIn()) throw new Exception('该模块仅限会员可见，请先<a href="'.__BASE_URL__.'login">登陆账号</a>！');
    $equipment = new \Plugin\equipment();
    try {
        #下架
        if(check_value($_REQUEST['request'])){
            if (!Validator::Number($_REQUEST['request'])) throw new Exception('数据错误，禁止站外提交！');
            $market->setItemSellOff($_SESSION['group'], $_SESSION['username'], $_REQUEST['request'],1);
        }
    } catch(Exception $ex) {
        message('error', $ex->getMessage());
    }
    $list = $market->getMarketItemList(1);
    #分页需求
    $page = ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) ? ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) : 1;
    $pageLength = 25; //每页显示多少条
    $totalPage = ((count($list)-1) / $pageLength) >= 1 ? ((count($list)-1) / $pageLength) : 1 ;    //总页数 (数据总数/每页显示多少条)
    $list = array_slice($list,($pageLength*($page-1)));
    $pager = new pagination($totalPage,$page,5,__PLUGIN_MARKET_HOME__.'Item?servercode='.$server.'&type='.$sType.'&price='.$sPrice.'&name='.$sName,2);
    ?>
    <div class="card mb-3">
        <div class="mt-3 mb-3">
        <table id="ranking" class="table text-center table-striped table-hover">
                <thead>
                <tr>
                    <th>类型</th>
                    <th>物品</th>
                    <th>卖家</th>
                    <th>价格</th>
                    <th hidden>价格类型</th>
                    <th>所属大区</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?if(empty($list)){?><tr><td colspan="10"><strong>暂无数据</strong></td></tr><?}?>
                <?$i=($pageLength*($page-1))+1;
                    foreach ($list as $key=>$data){
                        if($key == $pageLength) break;
                    ?>
                    <tr class="data-info" data-info="<?=$data['item_code'];?>">
                        <td><?=$market->getItemCategory($data['item_type'])?></td>
                        <td><img src="<?=$equipment->ItemsUrl($data['item_code'])?>"  alt="<?=$data['item_code']?>" width="35"/></td>
                        <td><?=playerProfile($data['servercode'],$data['name'])?></td>
                        <td><?=$data['price']?><sup><?=$market->getPriceType($data['price_type'])?></sup></td>
                        <td hidden><?=$market->getPriceType($data['price_type'])?></td>
                        <td><?=getGroupNameForServerCode($data['servercode'])?></td>
                        <td>
                            <?if($data['username'] == $_SESSION['username']){?>
                                <a href="<?=__BASE_URL__?>Market/flag/<?=$data['ID']?>" class="btn btn-danger">下架</a>
                            <?}else{?>
                                <a href="<?=__BASE_URL__?>Market/ItemBuy/<?=$data['ID']?>" class="btn btn-success">购买</a>
                            <?}?>
                        </td>
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
