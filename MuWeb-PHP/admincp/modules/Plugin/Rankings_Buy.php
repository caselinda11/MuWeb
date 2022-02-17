<?php
/**
 * 财富排名插件后台模块
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
                    <li class="breadcrumb-item active">充值排名</li>
                </ol>
            </div>
            <h4 class="page-title">充值排名</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_PLUGIN_RANKING_BUY_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->jf_active = $_POST['jf_active'];
        $xml->jf_Field = $_POST['jf_Field'];
        $xml->yb_active = $_POST['yb_active'];
        $xml->yb_Field = $_POST['yb_Field'];
        $xml->desc  = $_POST['desc'];
        $xml->excluded_username  = $_POST['excluded_username'];
        $save = $xml->asXML($xmlPath);
        if ($save) {
            message('success', '配置修改成功！');
        } else {
            message('error', '保存时发生错误！');
        }
    }
    if (check_value($_POST['submit'])) {
        submit();
    }

}catch (Exception $exception){
    message('error',$exception->getMessage());
}
try {
    $Buy = new \Plugin\Rankings\Buy();
    $moduleConfig = $Buy->loadConfig();
    ?>
    <div class="card">
        <div class="card-header">
            财富排名
        </div>
        <div class="card-body">
            <form action="" method="post">
                <table class="table table-striped table-bordered table-hover">
                    <tbody>
                    <tr>
                        <td width="60%"><strong>模块状态</strong>  <span
                                    class="text-muted">启用/禁用 此扩展。</td>
                        <td><?=enableDisableCheckboxes('active', $moduleConfig['active'], '启用', '禁用'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>积分排名</strong>  <span
                                    class="text-muted">显示/隐藏 积分排名选项</td>
                        <td><?=enableDisableCheckboxes('jf_active', $moduleConfig['jf_active'], '显示', '隐藏'); ?></td>
                    </tr>
                    <tr>
                        <th><strong>积分字段</strong><span class="ml-3 text-muted">字段必须在账号表中[MEMB_INFO]</span></th>
                        <td>
                            <input class="form-control" type="text" name="jf_Field" value="<?=$moduleConfig['jf_Field']?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>元宝排名</strong>  <span
                                    class="text-muted">显示/隐藏 元宝排名选项</td>
                        <td><?=enableDisableCheckboxes('yb_active', $moduleConfig['yb_active'], '显示', '隐藏'); ?></td>
                    </tr>
                    <tr>
                        <th><strong>元宝字段</strong><span class="ml-3 text-muted">字段必须在账号表中[MEMB_INFO]</span></th>
                        <td>
                            <input class="form-control" type="text" name="yb_Field" value="<?=$moduleConfig['yb_Field']?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>默认排名</strong>  <span
                                    class="text-muted">选择一个从高至低的排名次序。</td>
                        <td>
                            <select name="desc" class="form-control">
                                <option value="5" <?=selected($moduleConfig['desc'],(string)5)?>>积分优先</option>
                                <option value="6" <?=selected($moduleConfig['desc'],(string)6)?>>元宝优先</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><strong>排除账号</strong><span class="ml-3 text-muted">添加您要排除在排名之外的账号（多条用逗号,分隔）。</span></th>
                        <td>
                            <input class="form-control" type="text" name="excluded_username" value="<?=$moduleConfig['excluded_username']?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div style="text-align:center">
                                <button type="submit" name="submit" value="submit" class="btn btn-success col-md-2">保存
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
} ?>



