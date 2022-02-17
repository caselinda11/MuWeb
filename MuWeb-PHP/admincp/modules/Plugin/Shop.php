<?php
/**
 * 在线商城插件后台模块
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
                        <li class="breadcrumb-item active">在线商城</li>
                    </ol>
                </div>
                <h4 class="page-title">在线商城</h4>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="float-right mb-2">
                <a class="btn btn-success btn-lg text-white" data-toggle="modal" data-target="#staticBackdrop">发布商品</a>
                <a class="btn btn-warning btn-lg text-white" href="<?=admincp_base('Plugin/Shop_log'); ?>">购买日志</a>
            </div>
        </div>
    </div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_PLUGIN_SHOP_ROOT__ .'config.xml';
        $xml = simplexml_load_file($xmlPath);
        $xml->active = $_POST['active'];
        $xml->log = $_POST['log'];
        $xml->check_login = $_POST['check_login'];
        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '操作失败，请确保数据的正确性！');
        }
    }
    if (check_value($_POST['submit'])) {
        switch ($_POST['submit']){
            case "submit_add":
                try{
                    if(!check_value($_POST['item_name'])) throw new  Exception("请输入物品名称！");
                    if(!check_value($_POST['item_code'])) throw new  Exception("请输入物品代码！");
                    if(!check_value($_POST['item_content'])) throw new  Exception("请填写物品描述！");
                    if(!check_value($_POST['item_type'])) throw new  Exception("请输入物品类型！");
                    if(!check_value($_POST['item_price'])) throw new  Exception("请输入物品价格！");
                    if(!check_value($_POST['item_count'])) throw new  Exception("请输入物品总数！");
                    if(!$_POST['price_type']) throw new  Exception("请选择价格类型！");
                    if(!Validator::Items($_POST['item_code'])) throw new  Exception("请正确输入物品代码！");
                    $data = [
                        'item_name'       => $_POST['item_name'],
                        'item_type'       => $_POST['item_type'],
                        'class_type'      => $_POST['class_type'],
                        'item_count'     => $_POST['item_count'],
                        'item_price'      => $_POST['item_price'],
                        'price_type'      => $_POST['price_type'],
                        'status'          => 1,
                    ];

                    $INSERT = Connection::Database("Web")->query("INSERT INTO [X_TEAM_SHOP] ([item_name],[item_code],[item_type],[class_type],[item_count],[item_content],[item_price],[price_type],[status]) VALUES (:item_name, '".$_POST['item_code']."', :item_type, :class_type, :item_count, '".$_POST['item_content']."', :item_price, :price_type, :status)",$data);
                    if(!$INSERT) throw new  Exception("操作失败，请确保数据的正确性！");
                    message('success', '商品发布成功！');
                }catch (Exception $exception){
                    message('error',$exception->getMessage());
                }
                break;
            case "submit_edit":
                try{
                    if(!check_value($_POST['id'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    if(!check_value($_POST['item_name'])) throw new Exception("请输入物品名称！");
                    if(!check_value($_POST['item_code'])) throw new Exception("请输入物品代码！");
                    if(!Validator::Items($_POST['item_code'])) throw new Exception("请正确输入物品代码！");
                    if(!check_value($_POST['item_content'])) throw new  Exception("请填写物品描述！");
                    if(!check_value($_POST['item_count'])) throw new  Exception("请填写物品总数！");
                    if(!check_value($_POST['item_type'])) throw new  Exception("请输入物品类型！");
                    if(!check_value($_POST['item_price'])) throw new  Exception("请输入物品价格！");
                    $data = [
                        'item_name'       => $_POST['item_name'],
                        'item_type'       => $_POST['item_type'],
                        'class_type'      => $_POST['class_type'],
                        'item_count'     => $_POST['item_count'],
                        'item_price'      => $_POST['item_price'],
                        'price_type'      => $_POST['price_type'],
                        'status'          => $_POST['status'],
                        'id'              => $_POST['id'],
                    ];
                    $update = Connection::Database("Web")->query("UPDATE [X_TEAM_SHOP] SET [item_name] = :item_name,[item_code] = '".$_POST['item_code']."',[item_type] = :item_type,[class_type] = :class_type,[item_count] = :item_count,[item_content] = '".$_POST['item_content']."',[item_price] = :item_price,[price_type] = :price_type,[status] = :status WHERE [id] = :id",$data);
                    if(!$update) throw new  Exception("操作失败，请确保数据的正确性！");
                    message('success', '商品编辑成功！');
                }catch (Exception $exception){
                    message('error',$exception->getMessage());
                }
                break;
            case "submit_delete":
                try{
                    if(!check_value($_POST['id'])) throw new  Exception("操作失败，请确保数据的正确性！");
                    $delete = Connection::Database("Web")->query("DELETE FROM [X_TEAM_SHOP] WHERE [id] = ?",[$_POST['id']]);
                    if(!$delete) throw new  Exception("操作失败，请确保数据的正确性！");
                    message('success', '商品删除成功！');

                }catch (Exception $exception){
                    message('error',$exception->getMessage());
                }
                break;
            case "submit_save":
                submit();
                break;
            default:
                message('error', '操作失败，请确保数据的正确性！');
                break;
        }
    }
    $shop = new \Plugin\Shop();
    $creditSystem = new CreditSystem();
    $moduleConfig = $shop->loadConfig();
    $credit = new CreditSystem();
    ?>
    <div class="card">
        <div class="card-header">
            商城设置
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td width="60%"><strong>模块状态</strong><span class="ml-2 text-muted">禁用/启用 在线商城模块</span></td>
                        <td><?=enableDisableCheckboxes('active', $moduleConfig['active'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td width="60%"><strong>生成日志</strong><span class="ml-2 text-muted">禁用/启用 当购买商品时是否生成日志。</span><span class="text-danger">如果有限制商品总数,必须开启日志功能</span></td>
                        <td><?=enableDisableCheckboxes('log', $moduleConfig['log'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td width="60%"><strong>登陆显示</strong><span class="ml-2 text-muted">禁用/启用 启用是否需要登陆网站才显示商城模块，禁用则不需要。</span></td>
                        <td><?=enableDisableCheckboxes('check_login', $moduleConfig['check_login'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="text-align:center">
                                <button type="submit" name="submit" value="submit_save" class="btn btn-success col-md-2">保存</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">商品列表</div>
        <div class="">
            <?
            $itemData = $shop->getShopDataList();
            if (!is_array($itemData)) message('warning',"暂无商品信息!");
            ?>
            <?if(is_array($itemData)){?>
            <table class="table table-bordered text-center">
                <thead>
                <th>商品名称</th>
                <th>商品代码</th>
                <th>商品类型</th>
                <th>适用角色</th>
                <th>商品总数</th>
                <th>商品说明</th>
                <th>商品价格</th>
                <th>价格类型</th>
                <th>状态</th>
                <th>操作</th>
                </thead>
                <tbody>
                    <? foreach ($itemData as $data){?>
                    <form class="form-horizontal" action="" method="post">
                        <tr>
                            <td>
                                <input type="hidden" name="id" id="id" class="form-control" value="<?=$data['id']?>" />
                                <input type="text" name="item_name" id="item_name" class="form-control" value="<?=$data['item_name']?>" />
                            </td>
                            <td><input type="text" name="item_code" id="item_code" class="form-control" value="<?=$data['item_code']?>" /></td>
                            <td>
                                <select name="item_type" id="item_type" class="form-control">
                                    <?foreach ($shop->shopType() as $key=>$item){?>
                                        <option value="<?=$key?>" <?=selected((int)$data['item_type'],(string)$key)?>><?=$item?></option>
                                    <?}?>
                                </select>
                            </td>
                            <td>
                                <select name="class_type" id="class_type" class="form-control">
                                    <?foreach ($shop->classType() as $key=>$item){?>
                                        <option value="<?=$key?>" <?=selected($data['class_type'],(string)$key)?>><?=$item?></option>
                                    <?}?>
                                </select>
                            </td>
                            <td width="8%">
                                <input type="number" name="item_count" id="item_count" class="form-control" value="<?=$data['item_count']?>" />
                            </td>
                            <td>
                                <textarea rows="1" name="item_content" id="item_content" class="form-control">
                                    <?=$data['item_content']?>
                                </textarea>
                            </td>
                            <td width="8%">
                                <input type="number" name="item_price" id="item_price" class="form-control" value="<?=$data['item_price']?>" />
                            </td>
                            <td>
                                <?=$creditSystem->buildSelectInput("price_type", $data['price_type'], "form-control"); ?>
                            </td>
                            <td>
                                <select name="status" class="form-control">
                                    <option value="0"  <?=selected($data['status'],0)?>>禁用</option>
                                    <option value="1"  <?=selected($data['status'],1)?>>启用</option>
                                </select>
                            </td>
                            <td>
                                <button type="submit" name="submit" value="submit_edit" class="btn btn-primary">编辑</button>
                                <button type="submit" name="submit" value="submit_delete" class="btn btn-danger">删除</button>
                            </td>
                        </tr>
                    </form>
                    <?}?>
                    <?}?>
                </tbody>
            </table>
        </div>
    </div>
<!--    添加模态窗口-->
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">发布新商品</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
        <form action="" method="post">
            <div class="modal-body">
                <div class="form-group">
                    <label for="item_name" class="col-form-label">商品名称</label>
                    <input type="text" name="item_name" id="item_name" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="item_code" class="col-form-label">商品代码(*多个物品请用(,)逗号间隔)</label>
                    <input type="text" name="item_code" id="item_code" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="item_type" class="col-form-label">商品类型</label>
                    <select name="item_type" id="item_type" class="form-control">
                        <?foreach ($shop->shopType() as $key=>$item){?>
                            <option value="<?=$key?>" ><?=$item?></option>
                        <?}?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="class_type" class="col-form-label">适用角色</label>
                    <select name="class_type" id="class_type" class="form-control">
                        <?foreach ($shop->classType() as $key=>$item){?>
                            <option value="<?=$key?>"><?=$item?></option>
                        <?}?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="item_price" class="col-form-label">商品价格</label>
                    <input type="number" name="item_price" id="item_price" class="form-control"/>
                </div>
                <div class="form-group">
                    <label for="item_name" class="col-form-label">价格类型</label>
                    <?=$creditSystem->buildSelectInput("price_type", 1, "form-control"); ?>
                </div>
                <div class="form-group">
                    <label for="item_count" class="col-form-label">商品总数<span class="text-muted"> *商品限制发售多少件,0为不限制.</span></label>
                    <input type="number" name="item_count" id="item_count" class="form-control" value="0"/>
                </div>
                <div class="form-group">
                    <label for="item_content" class="col-form-label">商品说明<span class="text-muted"> *(可以写入html代码,例如&#60;br&#62;是换行符)</span></label>
                    <textarea class="form-control" name="item_content" id="item_content"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                <button type="submit" class="btn btn-success" name="submit" value="submit_add">发布商品</button>
            </div>
        </form>
    </div>
    </div>
    </div>

    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
