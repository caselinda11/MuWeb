<?php
/**
 * 角色交易插件后台模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group float-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item">
                            <a href="<?=admincp_base()?>">官方主页</a>
                        </li>
                        <li class="breadcrumb-item active">插件系统</li>
                        <li class="breadcrumb-item active">角色交易</li>
                    </ol>
                </div>
                <h4 class="page-title">交易市场</h4>
            </div>
        </div>
    </div>
<?php
function submit_char()
{
    $xmlPath = __PATH_PLUGIN_MARKET_ROOT__ . 'char.xml';
    $xml = simplexml_load_file($xmlPath);

    $xml->active = $_POST['char_active'];
    $xml->active_login = $_POST['char_login'];
    $xml->min_level = $_POST['min_level'];
    $xml->max_level = $_POST['max_level'];
    $xml->price_type = $_POST['char_price_type'];
    $xml->price_rate = $_POST['price_rate'];
    $xml->min_price = $_POST['min_price'];
    $xml->max_price = $_POST['max_price'];
    $xml->frequency = $_POST['frequency'];
    $xml->limit = $_POST['char_limit'];
    $xml->limit_price_type = $_POST['limit_price_type'];
    $save = $xml->asXML($xmlPath);
    if ($save) {
        message('success', '角色交易市场设置修改成功！');
    } else {
        message('error', '角色交易市场设置保存时发生错误！');
    }
}

function submit_item()
{
    $xmlPath = __PATH_PLUGIN_MARKET_ROOT__ . 'item.xml';
    $xml = simplexml_load_file($xmlPath);
    $xml->active = $_POST['item_active'];
    $xml->active_login = $_POST['item_login'];
    $xml->extend_warehouse = $_POST['extend_warehouse'];
    $xml->min_level = $_POST['min_level'];
    $xml->max_level = $_POST['max_level'];
    $xml->price_type = $_POST['item_price_type'];
    $xml->price_rate = $_POST['price_rate'];
    $xml->min_price = $_POST['min_price'];
    $xml->max_price = $_POST['max_price'];
    $xml->exclude = $_POST['exclude'];
    $xml->vip_items = $_POST['vip_items'];
    $xml->jewel = $_POST['jewel'];
    $xml->limit = $_POST['item_limit'];
    $xml->limit_price_type = $_POST['limit_price_type'];
    $save = $xml->asXML($xmlPath);
    if ($save) {
        message('success', '物品交易市场设置修改成功！');
    } else {
        message('error', '物品交易市场设置保存时发生错误！');
    }
}

function submit_flag()
{
    $xmlPath = __PATH_PLUGIN_MARKET_ROOT__ . 'flag.xml';
    $xml = simplexml_load_file($xmlPath);
    $xml->active = $_POST['active'];
    $xml->active_login = $_POST['flag_login'];
    $xml->price_type = $_POST['flag_price_type'];
    $xml->price_rate = $_POST['price_rate'];
    $xml->min_price = $_POST['min_price'];
    $xml->max_price = $_POST['max_price'];
    $xml->limit = $_POST['flag_limit'];
    $xml->limit_price_type = $_POST['limit_price_type'];
    $save = $xml->asXML($xmlPath);
    if ($save) {
        message('success', '旗帜交易市场设置修改成功！');
    } else {
        message('error', '旗帜交易市场设置保存时发生错误！');
    }
}


    $active = [
            0 => '',
            1 => '',
            2 => '',
    ];
    if (check_value($_POST['submit_char'])) {
        submit_char();
        $active[0] = 'active';
    }
    if (check_value($_POST['submit_item'])) {
        submit_item();
        $active[1] = 'active';
    }
    if (check_value($_POST['submit_flag'])) {
        submit_flag();
        $active[2] = 'active';
    }
    $Market = new \Plugin\Market\Market();
    $creditSystem = new CreditSystem();
    #初始状态
    if(array_search('active',$active) == false) $active[0] = 'active';
    ?>
    <div class="card">
        <div class="card-header">市场设置 <strong>日志表:[X_TEAM_MARKET_CHAR] - [X_TEAM_MARKET_ITEM_LOG]</strong></div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=$active[0]?>" id="home-tab" data-toggle="tab" href="#char" role="tab"
                       aria-controls="char" aria-selected="true">角色交易设置</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=$active[1]?>" id="profile-tab" data-toggle="tab" href="#item" role="tab" aria-controls="item"
                       aria-selected="false">物品交易设置</a>
                </li>
            
                <li class="nav-item" role="presentation">
                    <a class="nav-link <?=$active[2]?>" id="profile-tab" data-toggle="tab" href="#flag" role="tab" aria-controls="item"
                       aria-selected="false">旗帜交易设置</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show <?=$active[0]?>" id="char" role="tabpanel" aria-labelledby="home-tab">
                    <? $moduleConfig = $Market->loadConfig('char'); ?>
                    <form action="" method="post">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td width="60%"><strong>模块状态</strong><br><span class="text-muted">禁用/启用 角色交易模块</span></td>
                                <td><?=enableDisableCheckboxes('char_active', $moduleConfig['active'], '启用', '禁用'); ?></td>
                            </tr>
                            <tr>
                                <td width="60%"><strong>登陆可见</strong><br><span class="text-muted">禁用/启用 是否需要登陆才可查看市场</span></td>
                                <td><?=enableDisableCheckboxes('char_login', $moduleConfig['active_login'], '需要', '不需要'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>最小等级</strong><br><span class="text-muted">限制出售的角色最低等级，必须小于最大等级</span></td>
                                <td><input class="input-mini" type="number" name="min_level" value="<?= $moduleConfig['min_level'] ?>"/></td>
                            </tr>
                            <tr>
                                <td><strong>最大等级</strong><br><span class="text-muted">限制出售的角色最高等级，必须大于最小等级。[等级+大师]</span></td>
                                <td><input class="input-mini" type="number" name="max_level" value="<?= $moduleConfig['max_level'] ?>"/></td>
                            </tr>
                            <tr style="background: rgba(255,255,0,0.2)">
                                <td><strong>手续费项</strong><br><span class="text-muted">设置费用是以百分比扣费还是以定额扣费，设定将使用两种的其中一种。</span></td>
                                <td><?=enableDisableCheckboxes('char_price_type', $moduleConfig['price_type'], '百分比', '定额'); ?>
                                </td>
                            </tr>
                            <tr style="background: rgba(255,255,0,0.2)">
                                <td><strong>手续费率</strong><br><span class="text-muted">每笔交易将收取费用，基于上述的百分比或定额收取。</span></td>
                                <td><input class="input-mini" type="number" name="price_rate" value="<?= $moduleConfig['price_rate'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>最低限额</strong><br><span class="text-muted">每笔交易限制发布的最低限额，最低限额必须大于手续费与低于最高限额。不得小于0</span></td>
                                <td><input class="input-mini" type="number" name="min_price" value="<?= $moduleConfig['min_price'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>最高限额</strong><br><span class="text-muted">每笔交易限制发布的最高限额，最高限额必须大于手续费与高于最低限额。不得大于2亿</span></td>
                                <td><input class="input-mini" type="number" name="max_price" value="<?= $moduleConfig['max_price'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>限制次数</strong><br><span class="text-muted">限制每个角色可交易的次数</span></td>
                                <td><input class="input-mini" type="number" name="frequency" value="<?= $moduleConfig['frequency'] ?>"/>次
                                </td>
                            </tr>
                            <tr>
                                <td><strong>限制货币</strong><br><span class="text-muted">使用单种货币还是全部货币作为交易的货币类型</span></td>
                                <td><?=enableDisableCheckboxes('char_limit', $moduleConfig['limit'], '单种', '全部'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>货币类型</strong><br><span class="text-muted">上述选择[单种]请选择货币类型,上述选择[全部]此项无效</span></td>
                                <td>
                                    <?=$creditSystem->buildSelectInput("limit_price_type", $moduleConfig['limit_price_type'], "form-control"); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="text-align:center">
                                        <button type="submit" name="submit_char" value="submit_char" class="btn btn-success col-md-2">保存</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="tab-pane fade show <?=$active[1]?>" id="item" role="tabpanel" aria-labelledby="profile-tab">
                    <? $itemConfig = $Market->loadConfig('item');?>
                    <form action="" method="post">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td width="60%"><strong>模块状态</strong><br><span class="text-muted">禁用或启用 物品交易模块</span></td>
                                <td><?=enableDisableCheckboxes('item_active', $itemConfig['active'], '启用', '禁用'); ?></td>
                            </tr>
                            <tr>
                                <td width="60%"><strong>登陆可见</strong><br><span class="text-muted">禁用/启用 是否需要登陆才可查看市场</span></td>
                                <td><?=enableDisableCheckboxes('item_login', $itemConfig['active_login'], '需要', '不需要'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>扩展仓库</strong><br><span class="text-muted">是否开放扩展仓库</span></td>
                                <td><?=enableDisableCheckboxes('extend_warehouse', $itemConfig['extend_warehouse'], '启用', '禁用'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>会员道具</strong><br><span class="text-muted">是否允许会员道具寄售</span> *(会员道具判断的是物品[识别码]0与F的区别)</td>
                                <td><?=enableDisableCheckboxes('vip_items', $itemConfig['vip_items'], '允许', '禁用'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>最小等级</strong><br><span class="text-muted">限制出售的物品最低等级，必须小于最大等级</span></td>
                                <td><input class="input-mini" type="number" name="min_level" value="<?= $itemConfig['min_level'] ?>"/></td>
                            </tr>
                            <tr>
                                <td><strong>最大等级</strong><br><span
                                        class="text-muted">限制出售的物品最高等级，必须大于最小等级。</span></td>
                                <td><input class="input-mini" type="number" name="max_level" value="<?= $itemConfig['max_level'] ?>"/></td>
                            </tr>
                            <tr style="background: rgba(255,255,0,0.2)">
                                <td><strong>手续费项</strong><br><span class="text-muted">设置费用是以百分比扣费还是以定额扣费，设定将使用两种的其中一种。</span></td>
                                <td><?=enableDisableCheckboxes('item_price_type', $itemConfig['price_type'], '百分比', '定额'); ?>
                                </td>
                            </tr>
                            <tr style="background: rgba(255,255,0,0.2)">
                                <td><strong>手续费率</strong><br><span class="text-muted">每笔交易将收取费用，基于上述的百分比或定额收取。</span></td>
                                <td><input class="input-mini" type="number" name="price_rate" value="<?= $itemConfig['price_rate'] ?>"/></td>
                            </tr>
                            <tr>
                                <td><strong>最低限额</strong><br><span class="text-muted">每笔交易限制发布的最低限额，最低限额必须大于手续费与低于最高限额。不得小于0</span></td>
                                <td><input class="input-mini" type="number" name="min_price" value="<?=$itemConfig['min_price'] ?>"/></td>
                            </tr>
                            <tr>
                                <td><strong>最高限额</strong><br><span class="text-muted">每笔交易限制发布的最高限额，最高限额必须大于手续费与高于最低限额。不得大于2亿</span></td>
                                <td><input class="input-mini" type="number" name="max_price" value="<?= $itemConfig['max_price'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>排除物品</strong><br><span class="text-muted">排除不可交易的物品代码，填写物品总编码，多个物品以(,)逗号分隔。公式：总编码=(大编码*512+小编码)</span>
                                </td>
                                <td><input class="input-mini" type="text" name="exclude" value="<?= $itemConfig['exclude'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>限制货币</strong><br><span class="text-muted">使用单种货币还是全部货币作为交易的货币类型</span></td>
                                <td><?=enableDisableCheckboxes('item_limit', $itemConfig['limit'], '单种', '全部'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>宝石显示</strong><br><span class="text-muted">是否启用物品市场只支持寄售宝石类(玛雅,祝福,灵魂,生命,创造,守护,再生,进化宝石)</span></td>
                                <td><?=enableDisableCheckboxes('jewel', $itemConfig['jewel'], '启用', '禁用'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>货币类型</strong><br><span class="text-muted">上述选择[单种]请选择货币类型,上述选择[全部]此项无效</span></td>
                                <td>
                                    <?=$creditSystem->buildSelectInput("limit_price_type", $itemConfig['limit_price_type'], "form-control"); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="text-align:center">
                                        <button type="submit" name="submit_item" value="submit_item" class="btn btn-success col-md-2">保存</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="tab-pane fade show <?=$active[2]?>" id="flag" role="tabpanel" aria-labelledby="profile-tab">
                    <? $flagConfig = $Market->loadConfig('flag'); ?>
                    <form action="" method="post">
                        <table class="table table-striped table-bordered table-hover">
                            <tbody>
                            <tr>
                                <td width="60%"><strong>模块状态</strong><br><span class="text-muted">禁用或启用 旗帜交易模块</span></td>
                                <td><?=enableDisableCheckboxes('active', $flagConfig['active'], '启用', '禁用'); ?></td>
                            </tr>
                            <tr>
                                <td width="60%"><strong>登陆可见</strong><br><span class="text-muted">需要/不需要 是否需要登陆才可查看市场</span></td>
                                <td><?=enableDisableCheckboxes('flag_login', $itemConfig['active_login'], '需要', '不需要'); ?></td>
                            </tr>
                            <tr style="background: rgba(255,255,0,0.2)">
                                <td><strong>手续费项</strong><br><span class="text-muted">设置费用是以百分比扣费还是以定额扣费，设定将使用两种的其中一种。</span></td>
                                <td><?=enableDisableCheckboxes('flag_price_type', $flagConfig['price_type'], '百分比', '定额'); ?>
                                </td>
                            </tr>
                            <tr style="background: rgba(255,255,0,0.2)">
                                <td><strong>手续费率</strong><br><span class="text-muted">每笔交易将收取费用，基于上述的百分比或定额收取。</span></td>
                                <td><input class="input-mini" type="number" name="price_rate" value="<?= $flagConfig['price_rate'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>最低限额</strong><br><span class="text-muted">每笔交易限制发布的最低限额，最低限额必须大于手续费与低于最高限额。不得小于0</span></td>
                                <td><input class="input-mini" type="number" name="min_price" value="<?= $flagConfig['min_price'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>最高限额</strong><br><span class="text-muted">每笔交易限制发布的最高限额，最高限额必须大于手续费与高于最低限额。不得大于2亿</span></td>
                                <td><input class="input-mini" type="number" name="max_price" value="<?= $flagConfig['max_price'] ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>限制货币</strong><br><span class="text-muted">使用单种货币还是全部货币作为交易的货币类型</span></td>
                                <td><?=enableDisableCheckboxes('flag_limit', $flagConfig['limit'], '单种', '全部'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>货币类型</strong><br><span class="text-muted">上述选择[单种]请选择货币类型,上述选择[全部]此项无效</span></td>
                                <td>
                                    <?=$creditSystem->buildSelectInput("limit_price_type", $flagConfig['limit_price_type'], "form-control"); ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="text-align:center">
                                        <button type="submit" name="submit_flag" value="submit_flag" class="btn btn-success col-md-2">保存</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>

        </div>
    </div>
