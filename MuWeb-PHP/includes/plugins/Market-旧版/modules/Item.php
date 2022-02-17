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
    if(!isLoggedIn()) redirect(1,'login');
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('item');
    $market->MarketMenu();
    $market->marketSellMenu();
    if(!$moduleConfig['active']) throw new Exception('暂未开放物品交易市场！');
    if($moduleConfig['active_login']) if(!isLoggedIn()) throw new Exception('该模块仅限会员可见，请先<a href="'.__BASE_URL__.'login">登陆账号</a>！');

    try {
        if(check_value($_REQUEST['request'])){
            if (!Validator::Number($_REQUEST['request'])) throw new Exception('数据错误，禁止站外提交！');
            $market->setCharSellOff($_SESSION['group'], $_SESSION['username'],$_REQUEST['request'],true);
        }
    } catch(Exception $ex) {
        message('error', $ex->getMessage());
    }
    $list = $market->getMarketCharList(true);
    #分页需求
    $page = ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) ? ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) : 1;
    $pageLength = 25; //每页显示多少条
    $totalPage = ((count($list)-1) / $pageLength) >= 1 ? ((count($list)-1) / $pageLength) : 1 ;    //总页数 (数据总数/每页显示多少条)
    $list = array_slice($list,($pageLength*($page-1)));
    $pager = new pagination($totalPage,$page,5,__PLUGIN_MARKET_HOME__.'Item?servercode='.$server.'&type='.$sType.'&price='.$sPrice.'&name='.$sName,2);

    ?>
    <div class="card mb-3">
        <div class="mt-3 mb-3">
            <table id="item" class=" table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th>订单号</th>
                    <th>物品列表</th>
                    <th>价格</th>
                </tr>
                </thead>
                <tbody>
                <?if(empty($list)){?><tr><td colspan="10"><strong>暂无数据</strong></td></tr><?}?>
                <?if(is_array($list)){?>
                    <?
                    $Character = new Character();
                    $i=($pageLength*($page-1))+1;
                    foreach ($list as $key=>$sellData){
                        if($key == $pageLength) break;
                        $group = getGroupIDForServerCode($sellData['servercode']);
                        $data = $Character->getCharacterDataForCharacterName($group,$sellData['name']);
                        if (!is_array($data)) continue;
                        if(!$data['CtlCode']) continue;
                        ?>
                        <tr>
                            <td>
                                订单号:<?=$sellData['ID']?>
                                <div>大区:[<?=getGroupNameForGroupID($group)?>]</div>
                            </td>
                            <?$inventoryData = $market->_getInventoryData($data['Inventory']);?>
                            <td width="50%">
                                <?foreach ($inventoryData as $iData){?>
                                    <img class="data-info border border-dark" data-info="<?=$iData[1];?>"  src="<?=$iData['0']?>"  alt="" height="30"/>
                                <?}?>
                            </td>
                            <td>

                                <div>售价:<span class="text-danger"><b><?=$sellData['price']?><?=getPriceType($sellData['price_type'])?></b></span></div>
                                <div>
                                    <?if($data['AccountID'] == $_SESSION['username']){?>
                                        <a href="<?=__BASE_URL__?>Market/Item/<?=$sellData['ID']?>" class="col-md-6 btn btn-primary">下架</a>
                                    <?}else{?>
                                        <a href="<?=__BASE_URL__?>Market/ItemBuy/<?=$sellData['ID']?>" class="col-md-6 btn btn-success">购买</a>
                                    <?}?>
                                </div>
                            </td>
                        </tr>
                        <?$i++;}?>
                <?}?>
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