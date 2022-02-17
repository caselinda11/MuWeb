<?php
/**
 * 角色排名页面模块
    [0] => 1
    [AccountID] => xwqmwz002
    [Name] => 脆皮
    [cLevel] => 400
    [LevelUpPoint] => 0
    [Class] => 83
    [Strength] => 326
    [Dexterity] => 140
    [Vitality] => 18
    [Energy] => 1977
    [Money] => 197925574
    [MapNumber] => 37
    [PkLevel] => 3
    [PkCount] => 0
    [CtlCode] => 0
    [Leadership] => 0
    [mLevel] => 244
    [ConnectStat] => 0
    [IP] => 1.182.204.5
    [ServerName] => 3倍-1线(普通)
    [ConnectTM] => 2020-09-09 16:57:00
    [DisConnectTM] => 2020-09-10 06:43:00
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.1
 *
 **/

?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item active" aria-current="page">英雄排名</li>
            <li class="breadcrumb-item active" aria-current="page">角色排名</li>
        </ol>
    </nav>

<?php
try {
    loadModuleConfigs('rankings');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    if(!mconfig('enable_level')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    $Rankings = new Rankings();
    #显示排名导航类型
    $Rankings->rankingsMenu();

    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">角色排名</div>
        <div class="mt-2 mb-3">
            <table id="rankings-level" class="table table-bordered table-hover table-striped text-center table-sm" style="width:100%"></table>
            <?if(mconfig('show_date')) {?>
                <footer class=" text-right mt-3 mb-3 mr-3">
                    仅显示排名靠前的前[<strong><?=mconfig('results')?></strong>]条，本页面定时更新，上一次更新时间为
                    [<?=date('Y-m-d H:i',filemtime(__PATH_INCLUDES_CACHE__.'rankings_level.cache'))?>]
                </footer>
            <?}?>
        </div>
    </div>
    <?
    $zIndex = mconfig('show_total_status') ? 9 : 8;
    ?>
    <script type="text/javascript">
        var rankingIndex = [2,<?=$zIndex?>];
        var rankingTitle = ['所有职业','所有大区'];
        //数据表
        $(function(){
                $('#rankings-level').DataTable( {
                    "autoWidth": false,
                    "bProcessing": true,
                    "bStateSave": true,//状态保存
                    "ajax": baseUrl+"api/rankings.php",
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
                        {"title":"类型", "data": "avatar" ,"orderable" : false},
                        {"title":"职业", "data": "class" , "sClass" :"hidden"},
                        {"title":"角色名", "data": "name", "orderable" : false},
                        {"title":"等级", "data": "cLevel" },
                        {"title":"大师", "data": "mLevel" },
                        {"title":"状态", "data": "PkLevel" },
                        <?if(mconfig('show_total_status')){?>{"title":"总点数", "data": "point" },<?}?>
                        {"title":"所在地", "data": "MapNumber", "orderable" : false},
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