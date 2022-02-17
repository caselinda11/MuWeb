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
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>Market/Character">角色市场</a></li>
            <li class="breadcrumb-item active" aria-current="page">购买角色</li>
        </ol>
    </nav>
<?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('char');
    if(!$moduleConfig['active']) throw new Exception('未开放角色交易市场！');
    $id = $_REQUEST['request'];
    if(!check_value($id)) throw new Exception("请求无效请重新选择角色!");
    if(!Validator::Number($id)) throw new Exception("请求无效请重新选择角色!");
    $data = $market->_getCharInfoForMarketName($id);
    if(!is_array($data)) throw new Exception("您所选择的角色已经下架请重新选择！");
    $equipment = new \Plugin\equipment();

    try{
        if(check_value($_POST['submit'])){
            if ($data['servercode'] != getServerCodeForGroupID($_SESSION['group'])) throw new Exception("此角色与您的大区不符，请选择其他角色！");
            $market->setBuyCharacter($_SESSION['group'],$_SESSION['username'],$_POST['id']);
        }

    }catch (Exception $exception){
        $_POST = [];
        message('error', $exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header"><?=$data['Name']?> 详细信息</div>
        <div class="bg-light">
                <div class="row no-gutters">
                    <div class="col-md-5 mb-md-0 p-md-4">
                        <?=getPlayerClassAvatar($data['Class'], true, true, 'rounded w-100','100');?>
                    </div>
                    <div class="col-md-7 position-static p-4 pl-md-0">
                        <table class="table">
                            <tr>
                                <th>角色<sup>职业</sup></th>
                                <td><?=$data['Name']?><sup><?global $custom?><?=$custom['character_class'][$data['Class']][0]?></sup></td>
                            </tr>
                            <tr>
                                <th>等级<sup>大师</sup></th>
                                <td><?=$data['cLevel']?><sup><?=$data['mLevel']?></sup></td>
                            </tr>
                            <tr>
                                <th>所属家族</th>
                                <td><?=getImgForGensTypeId($data['GenFamily'],$data['GenLevel'])?></td>
                            </tr>
                            <tr>
                                <th>所属大区</th>
                                <td><?=getGroupNameForServerCode($data['servercode'])?></td>
                            </tr>
                            <tr>
                                <th>剩余Mu币</th>
                                <td><?=number_format($data['Money'])?></td>
                            </tr>
                            <tr>
                                <th>交易次数</th>
                                <td>该角色第<kbd><?=$market->checkSellChar($data['Name'])?></kbd>次交易</td>
                            </tr>
                            <tr>
                                <th>角色售价</th>
                                <td><kbd><?=number_format($data['price'])?></kbd><sup><?=getPriceType($data['price_type'])?></sup></td>
                            </tr>
                        </table>
                    </div>
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