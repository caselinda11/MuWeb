<?php
/**
 * [溯源系统]模块页面
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
            <li class="breadcrumb-item active" aria-current="page">溯源系统</li>
        </ol>
    </nav>
<?php
try {
    $drop = new \Plugin\drop();
    $tConfig = $drop->loadConfig();
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    ?>
    <div class="card mb-3">
        <div class="card-header">物品溯源</div>
        <div class="card-body">
            <table id="drop-list" class="table table-bordered table-hover table-striped text-center table-sm" style="width:100%"></table>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">
                    本页面定时更新
                </cite>
            </footer>
        </div>
    </div>
    <script type="text/javascript">
        var rankingIndex = [3];
        var rankingTitle = ['所有类型'];
        $(function(){
            $("#drop-list").DataTable({
                "autoWidth": false,
                "bStateSave": true,//状态保存
                "bProcessing": true, //是否显示加载
                // "serverSide": true,//是否从服务器获取数据
                "searching": true,  // 过滤功能
                "ajax": baseUrl+"api/ext/drop.php",
                "lengthMenu": [ [25, 50, 100, -1], [25, 50, 100, "显示所有"] ],
                "pageLength" : 25,
                "oLanguage": {  //对表格国际化
                    "sLengthMenu": "每页显示 _MENU_ 条",
                    "sZeroRecords": "暂时没有可显示的数据",
                    "sInfo": "当前页第 _START_ - _END_ 条　共计 _TOTAL_ 条",
                    "sInfoEmpty": "暂时没有记录",
                    "sInfoFiltered": "(从 _MAX_ 条记录中过滤)",
                    "sSearch": "搜索：",
                    "sProcessing": "正在加载数据...",
                    "oPaginate": {
                        "sFirst": "首页",
                        "sPrevious": "上一页",
                        "sNext": "下一页",
                        "sLast": "尾页"
                    },
                },
                "columns": [
                    {"title":"No", "data": "id"},
                    {"title":"物品", "data": null, "orderable" : false,"render": function (data, type, row) {
                        return '<div><img src="'+row.item_img+'" alt="" style="max-height: 35px;max-width: 35px"/></div>';
                        }
                    },
                    {"title":"物品", "data": "html_item_name", "orderable" : false},
                    {"title":"类型", "data": "type", "orderable" : false},
                    {"title":"编号", "data": "serial", "orderable" : false},
                    {"title":"角色名", "data": "char_name", "orderable" : false},
                    {"title":"获得地图", "data": "map", "orderable" : false},
                    {"title":"掉落坐标", "data": "location", "orderable" : false},
                    {"title":"获得时间", "data": "time"},
                ],
                "dom": "l<'#toolbar'>frtip",
                initComplete: function () {
                    $("#toolbar").css("width", "100px").css("display", "inline").css("margin-left", "20px");
                    $("#toolbar").append("筛选:（基于现有）");
                    for (var i = 0;i< rankingIndex.length;i++){
                        selectApi(this,rankingIndex[i],rankingTitle[i]);
                    }
                },
                "drawCallback": function( settings ) {
                    $("[data-toggle='tooltip']").tooltip();	//tooltip
                }
            });
        });
    </script>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}