<?php
/**
 * 获取云插件
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
                    <li class="breadcrumb-item active">购买插件</li>
                </ol>
            </div>
            <h4 class="page-title">插件市场</h4>
        </div>
    </div>
</div>

<?php
try{
    try{
        /**
         * 检查版本
         * @return bool|void
         */
        function getPluginList() {

            $url = 'http://192.168.0.231:81/Plugin.php';
            $opt_data = 'key=BY2&version=2.0.0';
            // 创建一个新cURL资源
            $curl  = curl_init();//初始化

            // 设置URL和相应的选项
            curl_setopt($curl,CURLOPT_URL,$url);  //设置url
            curl_setopt($curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
            curl_setopt($curl,CURLOPT_HEADER,0);  //设置头信息
            curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
            curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
            curl_setopt($curl,CURLOPT_POSTFIELDS,$opt_data);  //设置post的数据

            // 抓取URL并把它传递给浏览器
            $result = curl_exec($curl);
            if($result === false){
                echo curl_errno($curl);
                exit();
            }

            // 关闭cURL资源，并且释放系统资源
            curl_close($curl);
            $resultArray = json_decode($result, true);
            return $resultArray;
        }

        message('info','下列插件为作者自由开发提供，根据自己需求选择性购买使用，并不是必要的。');
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    $list = @getPluginList();
    if (!($list)) throw new Exception('暂时无法获取插件列表信息!');
?>
<div class="card">
    <div class="card-header">云插件列表</div>
    <div class="card-body">
        <table class="table table-bordered text-center table-striped table-hover">
            <thead>
                <th>插件名</th>
                <th style="width: 50%">插件描述</th>
                <th>版本支持</th>
                <th>作者</th>
                <th>售价</th>
    <!--            <th>操作</th>-->
            </thead>
            <tbody>
            <?if($list){?>
                <?foreach ($list as $id=>$item){?>
                    <tr>
                        <td><?=$item[0]?></td>
                        <td><?=$item[1]?></td>
                        <td><?=$item[2]?></td>
                        <td><?=$item[3]?></td>
                        <td>￥<b><?=$item[4]?></b>.<sup>00</sup></td>
<!--                        <td><a class="btn btn-success" href="index.php?module=--><?//=$_REQUEST['module']?><!--&buy=--><?//=$id?><!--">购买插件</a></td>-->
                    </tr>
                <?}?>
            <?}?>
            </tbody>
        </table>
    </div>
    <footer class="blockquote-footer text-right mt-3 mb-3 mr-3 font-14">
        此页面为收费功能,可联系作者购买
    </footer>
</div>
<?php
}catch (Exception $exception){
    message('error',$exception->getMessage());
}
