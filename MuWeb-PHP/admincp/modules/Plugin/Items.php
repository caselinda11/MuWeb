<?php
/**
 * [透视物品]插件后台模块
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
                    <li class="breadcrumb-item active">透视物品</li>
                </ol>
            </div>
            <h4 class="page-title">透视物品</h4>
        </div>
    </div>
</div>
<?php
try {
    $equipment = new \Plugin\equipment();
    function submit()
    {
        $xmlPath = __PATH_PLUGIN_ITEM_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active_class = $_POST['active_class'];
        $xml->active_exc = $_POST['active_exc'];
        $xml->active_set = $_POST['active_set'];
        $xml->active_soc = $_POST['active_soc'];
        $xml->active_sel = $_POST['active_sel'];
        $xml->active_too = $_POST['active_too'];
        $xml->too_name = $_POST['too_name'];

        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '保存时发生错误！');
        }
    }

    $list = $equipment->localTooList();
    if (check_value($_POST['submit'])) {

        switch ($_POST['submit']){
            case "submit_add":
                try{
                    if(!check_value($_POST['order'])) throw new Exception('请填写所有表单字段。');
                    if(!check_value($_POST['item_type'])) throw new Exception("物品总编号是必填项。");
                    if(!check_value($_POST['item_name'])) throw new Exception("物品名称是必填项。");
                    if(!check_value($_POST['item_use'])) throw new Exception("功能编号是必填项。");
                    if(!check_value($_POST['item_exc'])) throw new Exception("物品卓越值是必选项。");
                    if(!check_value($_POST['item_set'])) throw new Exception("物品套装值是必选项。");
                    if(!check_value($_POST['item_lev'])) throw new Exception("物品等级值是必选项。");
                    if(!check_value($_POST['item_rate'])) throw new Exception("物品概率/百分比是必填项。");
                    if(!check_value($_POST['color'])) throw new Exception("请正确选择字体颜色。");
                    $data = [
                        'item_type' =>  (int)$_POST['item_type'],
                        'item_name'  =>  $_POST['item_name'],
                        'item_use'  =>  (int)$_POST['item_use'],
                        'item_exc'  =>  (int)$_POST['item_exc'],
                        'item_set'  =>  (int)$_POST['item_set'],
                        'item_lev'  =>  (int)$_POST['item_lev'],
                        'item_rate' =>  (int)$_POST['item_rate'],
                        'color'     =>  $_POST['color'],
                        "order"     =>  (int)$_POST['order'],
                    ];
                    $list[] = $data;

                    # 按顺序排序
                    # http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
                    usort($list, function($a, $b) {
                        return $a['order'] - $b['order'];
                    });
                    # 转码
                    $Json = json_encode($list, JSON_PRETTY_PRINT);
                    # 保存
                    $cfgFile = fopen(__PATH_PLUGIN_ITEM_ROOT__.'tooList.json', 'w');
                    if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题');
                    fwrite($cfgFile, $Json);
                    fclose($cfgFile);

                    message('success', '特殊属性配置添加成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_edit":
                try{
                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    if(!check_value($_POST['item_type'])) throw new Exception("物品总编号是必填项。");
                    if(!check_value($_POST['item_name'])) throw new Exception("物品名称是必填项。");
                    if(!check_value($_POST['item_use'])) throw new Exception("功能编号是必填项。");
                    if(!check_value($_POST['item_exc'])) throw new Exception("物品卓越值是必选项。");
                    if(!check_value($_POST['item_set'])) throw new Exception("物品套装值是必选项。");
                    if(!check_value($_POST['item_lev'])) throw new Exception("物品等级值是必选项。");
                    if(!check_value($_POST['item_rate'])) throw new Exception("物品概率/百分比是必填项。");
                    if(!check_value($_POST['color'])) throw new Exception("请正确选择字体颜色。");
                    $elementId = $_POST['ID'];
                    $edit = [
                        'item_type' =>  (int)$_POST['item_type'],
                        'item_name' =>  $_POST['item_name'],
                        'item_use'  =>  (int)$_POST['item_use'],
                        'item_exc'  =>  (int)$_POST['item_exc'],
                        'item_set'  =>  (int)$_POST['item_set'],
                        'item_lev'  =>  (int)$_POST['item_lev'],
                        'item_rate' =>  (int)$_POST['item_rate'],
                        'color'     =>  $_POST['color'],
                        "order"     =>  (int)$_POST['order'],
                    ];
                    $list[$elementId] = $edit;

                    # 按顺序排序
                    # http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
                    usort($list, function($a, $b) {
                        return $a['order'] - $b['order'];
                    });
                    # 转码
                    $Json = json_encode($list, JSON_PRETTY_PRINT);
                    # 保存
                    $cfgFile = fopen(__PATH_PLUGIN_ITEM_ROOT__.'tooList.json', 'w');
                    if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题');
                    fwrite($cfgFile, $Json);
                    fclose($cfgFile);

                    message('success', '特殊属性配置编辑成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_delete":
                try{
                    if(!check_value($_POST['ID'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    if(!array_key_exists($_POST['ID'], $list)) throw new Exception('ID无效，禁止非法操作！');
                    unset($list[$_POST['ID']]);
                    $delConfig = array_values($list);
                    # 转码
                    $Json = json_encode($delConfig, JSON_PRETTY_PRINT);
                    # 保存更改
                    $cfgFile = fopen(__PATH_PLUGIN_ITEM_ROOT__.'tooList.json', 'w+');
                    if(!$cfgFile) throw new Exception('打开用户面板文件时出现问题.');
                    fwrite($cfgFile, $Json);
                    fclose($cfgFile);
                    message('success', '特殊属性配置删除成功！');
                } catch(Exception $ex) {
                    message('error', $ex->getMessage());
                }
                break;
            case "submit_save":
                submit();
                break;
            default:
                message('error', '禁止非法提交,请确保数据的正确性!');
                break;
        }

    }

}catch (Exception $exception){
    message('error',$exception->getMessage());
}
try {

    $moduleConfig = $equipment->loadConfig();
    $list = $equipment->localTooList();
    $tooNameFile = $equipment->getXml(__PATH_ITEMS_FILE__.'TooName.xml');
    ?>
    <div class="card">
        <div class="card-header">
            物品显示
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td width="60%"><strong>职业要求</strong>  <span class="text-muted">启用/禁用 此属性显示。</td>
                        <td><?=enableDisableCheckboxes('active_class', $moduleConfig['active_class'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>卓越属性</strong>  <span class="text-muted">启用/禁用 此属性显示。</td>
                        <td><?=enableDisableCheckboxes('active_exc', $moduleConfig['active_exc'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>套装属性</strong>  <span class="text-muted">启用/禁用 此属性显示。</td>
                        <td><?=enableDisableCheckboxes('active_set', $moduleConfig['active_set'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>镶嵌属性</strong>  <span class="text-muted">启用/禁用 此属性显示。</td>
                        <td><?=enableDisableCheckboxes('active_soc', $moduleConfig['active_soc'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>识别码</strong>  <span class="text-muted">启用/禁用 此属性显示。</td>
                        <td><?=enableDisableCheckboxes('active_sel', $moduleConfig['active_sel'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>特殊属性</strong>  <span class="text-muted">启用/禁用 此属性显示（在下列窗口中配置特殊属性）。</td>
                        <td><?=enableDisableCheckboxes('active_too', $moduleConfig['active_too'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>特殊属性名称</strong>  <span class="text-muted">开启上述才可显示，用于描述开头的名称，设0不显示。</td>
                        <td><input type="text" class="form-control" value="<?=$moduleConfig['too_name']?>" name="too_name"/></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="text-align:center">
                                <button type="submit" name="submit" value="submit_save" class="btn btn-success col-md-2">保存
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">特殊属性物品列表 <a href="" data-toggle="modal" data-target="#myModal" class="text-danger"><i class="dripicons-question"></i>功能说明</a></div>
        <div class="">
            <table class="table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th style="width: 8%;">优先级</th>
                    <th style="width: 10%;">总编号</th>
                    <th>描述(注释)</th>
                    <th style="width: 15%;">功能</th>
                    <th style="width: 10%;">卓越</th>
                    <th style="width: 7%;">套装</th>
                    <th style="width: 7%;">等级</th>
                    <th style="width: 8%;">概率/百分比</th>
                    <th style="width: 10%;">字体颜色</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?if(!empty($list)){?>
                    <?foreach ($list as $id => $data){
                        ?>
                        <form action="" method="post">
                            <tr>
                                <td><input type="hidden" class="form-control" name="ID" value="<?=$id?>"/>
                                    <input type="number" class="form-control" name="order" value="<?=$data['order']?>"/>
                                </td>
                                <td><input type="number" class="form-control" name="item_type" value="<?=$data['item_type']?>"/></td>
                                <td><input type="text" class="form-control" name="item_name" value="<?=$data['item_name']?>"/></td>
                                <td>
                                    <select name="item_use" class="form-control">
                                        <?foreach ($tooNameFile['Too'] as $item){?>
                                            <option value="<?=$item['@attributes']['Index']?>" <?=selected($item['@attributes']['Index'],(string)$data['item_use'])?>><?=$item['@attributes']['Name']?></option>
                                        <?}?>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control" name="item_exc" value="<?=$data['item_exc']?>" /></td>
                                <td><input type="text" class="form-control" name="item_set" value="<?=$data['item_set']?>" /></td>
                                <td><input type="text" class="form-control" name="item_lev" value="<?=$data['item_lev']?>" /></td>
                                <td><input type="text" class="form-control" name="item_rate"  value="<?=$data['item_rate']?>"/></td>
                                <td>
                                    <select name="color" class="form-control">
                                        <option class="bg-primary" value="text-primary" <?=selected($data['color'],(string)'text-primary')?>>天蓝色</option>
                                        <option class="bg-secondary" value="text-secondary" <?=selected($data['color'],(string)'text-secondary')?>>灰色</option>
                                        <option class="bg-success" value="text-success" <?=selected($data['color'],(string)'text-success')?>>绿色</option>
                                        <option class="bg-danger" value="text-danger" <?=selected($data['color'],(string)'text-danger')?>>红色</option>
                                        <option class="bg-warning" value="text-warning" <?=selected($data['color'],(string)'text-warning')?>>黄色</option>
                                        <option class="bg-info" value="text-info" <?=selected($data['color'],(string)'text-info')?>>蓝色</option>
                                        <option class="bg-dark" value="text-dark" <?=selected($data['color'],(string)'text-dark')?>>黑色</option>
                                        <option class="bg-white" value="text-white" <?=selected($data['color'],(string)'text-white')?>>白色</option>
                                        <option class="bg-muted" value="text-muted" <?=selected($data['color'],(string)'text-muted')?>>深灰色</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="btn-group col-md-12">
                                        <button type="submit" name="submit" value="submit_edit" class="btn btn-primary">编辑</button>
                                        <button type="submit" name="submit" value="submit_delete" class="btn btn-danger">删除</button>
                                    </div>
                                </td>
                            </tr>
                        </form>
                    <?}?>
                <?}else{?>
                    <tr><th  colspan="10" class="text-danger">暂无特殊属性信息</th></tr>
                <?}?>
                <tr><th colspan="10"><strong>添加新的特殊属性信息</strong></th></tr>
                <form action="" method="post">
                    <tr>
                        <td><input type="number" class="form-control" name="order" value="1"/></td>
                        <td><input type="number" class="form-control" name="item_type" /></td>
                        <td><input type="text" class="form-control" name="item_name" /></td>
                        <td>
                            <select name="item_use" class="form-control">
                                <?foreach ($tooNameFile['Too'] as $item){?>
                                <option value="<?=$item['@attributes']['Index']?>"><?=$item['@attributes']['Name']?></option>
                                <?}?>
                            </select>
                        </td>
                        <td><input type="text" class="form-control" name="item_exc" value="0"/></td>
                        <td><input type="text" class="form-control" name="item_set" value="0" /></td>
                        <td><input type="text" class="form-control" name="item_lev" value="0" /></td>
                        <td><input type="text" class="form-control" name="item_rate" /></td>
                        <td>
                            <select name="color" class="form-control">
                                <option class="bg-primary" value="text-primary">天蓝色</option>
                                <option class="bg-secondary" value="text-secondary">灰色</option>
                                <option class="bg-success" value="text-success">绿色</option>
                                <option class="bg-danger" value="text-danger" selected>红色</option>
                                <option class="bg-warning" value="text-warning">黄色</option>
                                <option class="bg-info" value="text-info">蓝色</option>
                                <option class="bg-dark" value="text-dark">黑色</option>
                                <option class="bg-white" value="text-white">白色</option>
                                <option class="bg-muted" value="text-muted">深灰色</option>
                            </select>
                        </td>
                        <td><button type="submit" name="submit" value="submit_add" class="btn btn-success col-md-12">添加</button></td>
                    </tr>
                </form>
                </tbody>
            </table>
        </div>
    </div>
    <!--modal-content -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">功能说明</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h6 class="mt-0">此功能是显示在物品上的特殊属性，配置文件：<span class="text-danger">[includes/plugins/items/Xml/TooName.xml]</span>。</h6>
                    <p>
                        <strong>优先级</strong>：用于整理前后顺序。<br>
                        <strong>总编号</strong>：= 大编号*512+编号。<br>
                        <strong>描述(注释)</strong>：仅做为此页面注释作用<br>
                        <strong>功能</strong>：可在[Xml/TooName.xml]中更改属性名称<br>
                        <strong>卓越</strong>：[0]为不限制,填[1]为所有卓越都显示,[2~6]规定属性多少以上显示。<br>
                        <strong>套装</strong>：[0]为不限制,填[1]为所有套装都显示,[5,9]为老套装,[6,10]为新套装。<br>
                        <strong>等级</strong>：[0]为不限制,[1~15]绝对等级显示。[101~115](大于显示,例如填21,那么大于1级的就会生效,依次类推)<br>
                        <strong>概率/百分比</strong>：显示在装备上的概率数值。<br>
                    </p>
                    <p>请按照上述说明进行添加，如果是整套也是一件一件的加。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal-content -->
    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
} ?>



