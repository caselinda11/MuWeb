<?php
/**
 * 等级排名页面模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item active" aria-current="page">英雄排名</li>
            <li class="breadcrumb-item active" aria-current="page">战盟排名</li>
        </ol>
    </nav>
<?php
try {
    loadModuleConfigs('rankings');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    if(!mconfig('enable_guilds')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');

    $Rankings = new Rankings();
    #显示排名导航类型
    $Rankings->rankingsMenu();

    ?>

    <div class="card mt-3 mb-3">
        <div class="card-header">战盟排名</div>
        <div class="rankings mt-3 mb-3">
            <table id="rankings-guild" class="table table-hover table-bordered text-center table-striped table-sm"></table>
            <?if(mconfig('show_date')) {?>
            <footer class="text-right mt-3 mb-3 mr-3">
                仅显示排名靠前的前[<strong><?=mconfig('results')?></strong>]条，本页面定时更新，上一次更新时间为
                [<?=date('Y-m-d H:i',filemtime(__PATH_INCLUDES_CACHE__.'rankings_guilds.cache'))?>]
            </footer>
            <?}?>
        </div>
    </div>
    <script type="text/javascript">
        var rankingIndex = [6];
        var rankingTitle = ['所有大区'];
        //数据表
        $(document).ready(function(){
                $('#rankings-guild').DataTable( {
                    "autoWidth": false,
                    "bProcessing": true,
                    "bStateSave": true,//状态保存
                    "ajax":{ // 获取数据
                        "url":baseUrl+"api/rankings.php",
                        "dataType":"json" //返回来的数据形式
                    },
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
                        {"title":"No", "data": "no" },
                        {"title":"战盟图徽", "data": "logo" ,"orderable" : false},
                        {"title":"战盟", "data": 'gName', "orderable" : false},
                        {"title":"战盟盟主", "data": "name", "orderable" : false},
                        {"title":"评分", "data": "score" },
                        {"title":"战盟人数", "data": "number" },
                        {"title":"所属大区", "data": "servercode"},
                    ],
                    "dom": "l<'#toolbar'>frtip",
                    initComplete: function () {
                        $("#toolbar").css("width", "100px").css("display", "inline").css("margin-left", "20px");
                        $("#toolbar").append("筛选:（基于下列现有数据）");
                        var that = this;
                        for (var i = 0;i< rankingIndex.length;i++){
                            selectApi(that,rankingIndex[i],rankingTitle[i]);
                        }
                    },
                    "drawCallback": function( settings ) {
                        $("[data-toggle='tooltip']").tooltip();	//tooltip
                    }
                } );
            }
        );
    </script>
    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}