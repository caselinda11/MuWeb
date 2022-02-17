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
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>Market/Item">物品市场</a></li>
        <li class="breadcrumb-item active" aria-current="page">购买物品</li>
    </ol>
</nav>
<?php
try{
    if(!isLoggedIn()) redirect(1,'login');
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('item');
    if(!$moduleConfig['active']) throw new Exception('未开放角色交易市场！');
    $id = $_REQUEST['request'];
    if(!check_value($id)) throw new Exception("请求无效请重新选择物品!");
    if(!Validator::Number($id)) throw new Exception("请求无效请重新选择物品!");
    $data = $market->getItemInfoForId($id);
    if(!is_array($data)) throw new Exception("您所选择的物品已经下架请重新选择!");
    $equipment = new \Plugin\equipment();
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色用于接收物品！');
    try{
        if(check_value($_POST['submit'])){
            if (getGroupIDForServerCode($data['servercode']) != $_SESSION['group']) throw new Exception("该物品与您的大区不符，请选择其他物品！");
            $market->setBuyItem($_SESSION['group'],$_SESSION['username'],$_POST['id']);
        }
    }catch (Exception $exception){
        message('error', $exception->getMessage());
    }
?>
    <div class="card">
        <div class="card-header">购买物品</div>
        <div class="card-body">
            <div class="mb-3">
                <form class="form-horizontal" action="" method="post">
                <table class="table table-striped text-center">
                    <tbody>
                    <tr class="data-info" data-info="<?=$data['item_code'];?>">
                        <td rowspan="20" width="40%">
                            <div>
                                <img src="<?=$equipment->ItemsUrl($data['item_code'])?>" alt="<?=$data['item_code']?>" width="35" height="35"/>
                            </div>
                            <div>鼠标放过来可以查看属性</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="table table-striped text-center">
                                <tbody>
                                <tr><th colspan="2">购买须知</th></tr>
                                <tr><td colspan="2">该物品为玩家自由发布的物品道具。</td></tr>
                                <tr><td colspan="2">选择一名角色作为接受物品方，物品会发送至角色储物柜。</td></tr>
                                <tr><td colspan="2">请您在购买前考虑清楚是否需要该物品。</td></tr>
                                <tr><td colspan="2">请谨慎操作，一旦购买将无法进行退货。</td></tr>
                                <tr>
                                    <th>物品类型</th>
                                    <td>
                                        <?=$market->getItemCategory($data['item_type'])?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>物品卖家</th>
                                    <td>
                                        <?=$data['name']?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>物品价格</th>
                                    <td>
                                        <?=$data['price']?><sup><?=getPriceType($data['price_type'])?></sup>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><span class="text-danger">*选择一名角色用于接收该物品</span></td></tr>
                                <tr>
                                    <td><strong>接收角色</strong></td>
                                    <td>
                                        <select id="character" name="character_name" class="form-control">
                                            <?foreach($AccountCharacters as $code=>$thisCharacter) {?>
                                                <?$characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                                                if($characterData['CtlCode']) continue;?>
                                                <option value="<?=$thisCharacter?>"><?=$thisCharacter?></option>
                                            <?}?>
                                        </select>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="id" value="<?=$data['ID']?>" />
                    <input type="hidden" name="key" value="<?=Token::generateToken('market_item')?>"/>
                    <button type="submit" name="submit" value="submit" class="col-sm-offset-4 btn btn-success col-sm-4">
                        确认购买
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}