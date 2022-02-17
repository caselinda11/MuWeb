<?php
/**
 * 交易市场
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>Market/Item">物品市场</a></li>
            <li class="breadcrumb-item active" aria-current="page">购买物品</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('char');
    $market->MarketMenu();
    $market->marketSellMenu();
    if(!$moduleConfig['active']) throw new Exception('暂未开放角色交易市场！');
    $id = $_REQUEST['request'];
    if(!check_value($id)) throw new Exception("请求无效请重新选择角色!");
    if(!Validator::Number($id)) throw new Exception("请求无效请重新选择角色!");
    $data = $market->_getCharInfoForMarketName($id,true);
    if(!is_array($data)) throw new Exception("您所选择的角色已经下架请重新选择！");
    $equipment = new \Plugin\equipment();

    try{
        if(check_value($_POST['submit'])){
            if ($data['servercode'] != getServerCodeForGroupID($_SESSION['group'])) throw new Exception("此角色与您的大区不符，请选择其他角色！");
            $market->setBuyCharacter($_SESSION['group'],$_SESSION['username'],$_POST['id'],true);
        }

    }catch (Exception $exception){
        $_POST = [];
        message('error', $exception->getMessage());
    }
    ?>
    <p class="alert alert-info">说明：购买就是购买一个角色包含背包的所有物品，请确保有足够位置储存新的角色。</p>
    <div class="card mb-3">
        <div class="card-header">详细信息</div>
        <div class="bg-light">
            <div class="row no-gutters">
                <div class="col-md-12">
                    <table class="table text-center">
                        <tr><th>背包信息</th></tr>
                        <tr>
                            <?$inventoryData = $market->_getInventoryData($data['Inventory']);?>
                            <td>
                                <?foreach ($inventoryData as $iData){?>
                                    <img class="data-info border border-dark" data-info="<?=$iData[1];?>"  src="<?=$iData['0']?>"  alt="" height="30"/>
                                <?}?>
                            </td>
                        </tr>
                    </table>
                    <form class="form-horizontal" action="" method="post">
                        <div class="form-group row justify-content-md-center">
                            <input type="hidden" name="name" value="<?=$data['servercode']?>" />
                            <input type="hidden" name="id" value="<?=$data['ID']?>" />
                            <input type="hidden" name="key" value="<?=Token::generateToken('market_character')?>"/>
                            <button type="submit" name="submit" value="submit" class="col-sm-offset-4 btn btn-success col-sm-4">
                                确认购买
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}