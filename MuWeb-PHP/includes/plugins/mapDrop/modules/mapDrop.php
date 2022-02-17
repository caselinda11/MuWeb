<?php
/**
 * [mapDrop]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">地图掉落</li>
        </ol>
    </nav>
    <?php
try {
    $mapDrop = new \Plugin\mapDrop();
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">地图掉落  <span class="text-danger">(*搜索物品请使用Ctrl+S)</span></div>
        <div class="card-body">
            <?
            try {
//                    debug($mapDrop->getMapFileName(1));
            } catch(Exception $ex) {
                message('error', $ex->getMessage());
            }
            ?>
            <form>

                <div class="form-group">
                    <select class="form-control" name="cds" onchange="showHint(this.value)">
                        <option value=" ">选择地图</option>
                        <?php
                        foreach ($mapDrop->loadMapDropList() as $key=>$value){
                            echo '<option value="'.$key.'">'.$value.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div id="txtHint">
                    <center>选择一个地图将会展示该地图的所有物品掉落</center>
                </div>

            </form>
            <div class="modal fade" id="myModal">
                <div class="modal-dialog modal-sm modal-dialog-centered" style="max-width: 30px;">
                    <div class="spinner-border text-dark" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <script>
                function showHint(str)
                {
                    if (str.length===0){
                        document.getElementById("txtHint").innerHTML="";
                        HiddenDiv();
                        return;
                    }
                    if (window.XMLHttpRequest){
                        // IE7+, Firefox, Chrome, Opera, Safari 浏览器执行的代码
                        xmlhttp=new XMLHttpRequest();
                        HiddenDiv();
                    }else{
                        //IE6, IE5 浏览器执行的代码
                        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                        HiddenDiv();
                    }
                    xmlhttp.onreadystatechange=function() {
                        if (xmlhttp.readyState===4 && xmlhttp.status===200) {
                            document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
                            HiddenDiv();
                        }
                    };
                    ShowDiv();
                    xmlhttp.open("GET",baseUrl+"api/ext/droplist.php?map="+str,true);
                    xmlhttp.send();
                }
                //显示加载数据
                function ShowDiv() {
                    $('#myModal').modal({backdrop:'static',keyboard:false});
                }
                //隐藏加载数据
                function HiddenDiv() {
                    $('#myModal').modal('hide');
                }
            </script>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}