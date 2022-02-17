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
            <li class="breadcrumb-item active" aria-current="page">物品市场</li>
        </ol>
    </nav>
<?php
try {
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('item');
    $market->MarketMenu();
    if(!$moduleConfig['active']) throw new Exception('未开放物品交易市场！');
    if($moduleConfig['active_login']) if(!isLoggedIn()) throw new Exception('该模块仅限会员可见，请先<a href="'.__BASE_URL__.'login">登陆账号</a>！');
    $equipment = new \Plugin\equipment();
    try {
        #下架
        if (check_value($_REQUEST['request'])) {
            if (!Validator::Number($_REQUEST['request'])) throw new Exception('数据错误，禁止站外提交！');
            $market->setItemSellOff($_SESSION['group'], $_SESSION['username'], $_REQUEST['request'], 0);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    $market->marketSellMenu();

    $list = $market->getMarketItemList();
    #搜索功能
    $server = check_value($_GET['servercode']) ? $_GET['servercode'] : "";
    if(check_value($server)) $list = getArrayKeyForValue($server,$list,'servercode');
    $sPrice = check_value($_GET['price']) ? (($_GET['price'] == 'asc') ? 1 : 0) : "";
    if(check_value($sPrice)) $list = arraySortByKey($list,"price",$sPrice);
    $sType = check_value($_GET['type']) ? $_GET['type'] : "";
    if(check_value($sType)) $list = getArrayKeyForValue($sType,$list,'item_type');
    $sName = check_value($_GET['name']) ? $_GET['name'] : "";
    if(check_value($sName)) $list = getArrayKeyForValue($sName,$list,'item_name');
    #分页需求
    $page = ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) ? ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) : 1;
    $pageLength = 25; //每页显示多少条
    $totalPage = ((count($list)-1) / $pageLength) >= 1 ? ((count($list)-1) / $pageLength) : 1 ;    //总页数 (数据总数/每页显示多少条)
    $list = array_slice($list,($pageLength*($page-1)));
    $pager = new pagination($totalPage,$page,5,__PLUGIN_MARKET_HOME__.'Item?servercode='.$server.'&type='.$sType.'&price='.$sPrice.'&name='.$sName,2);
    ?>
    <div class="card mb-3">
        <div class="card-header">物品市场</div>

        <form action="">
            <div class="mt-3">
                <div class="form-group row justify-content-md-center">
                    <div class="col-sm-3" style="padding-left: 5px;padding-right: 5px;">
                        <select class="form-control" name="servercode" id="servercode">
                            <option value="">所有大区</option>
                            <? foreach (getServerGroupList() as $code=>$item){?>
                                <option value="<?=$code?>" <?=selected($_GET['servercode'],(string)$code)?>><?=$item?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-2" style="padding-left: 5px;padding-right: 5px;">
                        <select class="form-control" name="price" id="price">
                            <option value="">全部价格</option>
                            <option value="desc" <?=selected($_GET['price'],(string)"desc")?>>从高至低</option>
                            <option value="asc" <?=selected($_GET['price'],(string)"asc")?>>从低至高</option>
                        </select>
                    </div>
                    <div class="col-sm-2" style="padding-left: 5px;padding-right: 5px;">
                        <select class="form-control" name="type" id="type">
                            <option value="">全部类型</option>
                            <?foreach ($market->itemType as $key => $name){?>
                                <option value="<?=$key?>" <?=selected($_GET['type'],(string)$key)?>><?=$name?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3" style="padding-left: 5px;padding-right: 5px;">
                        <input type="text" class="form-control" name="name" placeholder="物品名称" />
                    </div>
                    <div class="" style="padding-left: 5px;padding-right: 5px;">
                        <div class="input-group-prepend">
                            <button class="btn btn-success" type="submit">查找</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
            <table id="" class="table text-center table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>NO</th>
                    <th>类型</th>
                    <th>图片</th>
                    <th>物品</th>
                    <th>卖家</th>
                    <th>价格</th>
                    <th>所属大区</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                    <?if(!is_array($list)){?><tr><td colspan="10"><strong>暂无数据</strong></td></tr><?}?>
                    <?$i=($pageLength*($page-1))+1;
                    foreach ($list as $key=>$data){
                        if($key == $pageLength) break;
                        ?>
                        <tr class="data-info" data-info="<?=$data['item_code'];?>">
                            <td><?=$i?></td>
                            <td><?=$market->getItemCategory($data['item_type'])?></td>
                            <td><img src="<?=$equipment->ItemsUrl($data['item_code'])?>" width="35" height="35" alt=""/></td>
                            <td><?=$data['item_name']?></td>
                            <td><?=playerProfile($data['servercode'],$data['name'])?></td>
                            <td><?=$data['price']?><sup><?=getPriceType($data['price_type'])?></sup></td>
                            <td><?=getGroupNameForServerCode($data['servercode'])?></td>
                            <td>
                                <?if(isset($_SESSION['username']) && isset($_SESSION['group']) && $data['username'] == $_SESSION['username'] && $data['servercode'] == getServerCodeForGroupID($_SESSION['group'])){?>
                                    <a href="<?=__BASE_URL__?>Market/Item/<?=$data['ID']?>" class="btn btn-danger col-md-8">下架</a>
                                <?}else{?>
                                    <a href="<?=__BASE_URL__?>Market/ItemBuy/<?=$data['ID']?>" class="btn btn-success col-md-8">购买</a>
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
    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}