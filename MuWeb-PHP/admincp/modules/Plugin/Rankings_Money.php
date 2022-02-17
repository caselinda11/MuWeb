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
                    <li class="breadcrumb-item active">财富排名</li>
                </ol>
            </div>
            <h4 class="page-title">财富排名</h4>
        </div>
    </div>
</div>
<?php
try {
    function submit()
    {
        $xmlPath = __PATH_PLUGIN_RANKING_MONEY_ROOT__ .'config.xml';

        $xml = simplexml_load_file($xmlPath);

        $xml->active = $_POST['active'];
        $xml->m_active = $_POST['m_active'];
        $xml->jf_active = $_POST['jf_active'];
        $xml->yb_active = $_POST['yb_active'];
        $xml->desc  = $_POST['desc'];
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
    $Money = new \Plugin\Rankings\Money();
    $moduleConfig = $Money->loadConfig();
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
                        <td><strong>金币排名</strong>  <span
                                class="text-muted">显示/隐藏 金币排名选项</td>
                        <td><?=enableDisableCheckboxes('m_active', $moduleConfig['m_active'], '显示', '隐藏'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>积分排名</strong>  <span
                                class="text-muted">显示/隐藏 积分排名选项</td>
                        <td><?=enableDisableCheckboxes('jf_active', $moduleConfig['jf_active'], '显示', '隐藏'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>元宝排名</strong>  <span
                                class="text-muted">显示/隐藏 元宝排名选项</td>
                        <td><?=enableDisableCheckboxes('yb_active', $moduleConfig['yb_active'], '显示', '隐藏'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>默认排名</strong>  <span
                                class="text-muted">选择一个从高至低的排名次序。</td>
                        <td>
                            <select name="desc" class="form-control">
                                <option value="2" <?if($moduleConfig['desc']==2) echo 'selected'?>>金币排名</option>
                                <option value="5" <?if($moduleConfig['desc']==5) echo 'selected'?>>积分排名</option>
                                <option value="6" <?if($moduleConfig['desc']==6) echo 'selected'?>>元宝排名</option>
                            </select>
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



