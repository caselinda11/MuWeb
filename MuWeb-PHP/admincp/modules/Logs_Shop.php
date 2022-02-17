<?php
/**
 * 文件说明
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
                        <li class="breadcrumb-item active">消费记录</li>
                        <li class="breadcrumb-item active">购买日志</li>
                    </ol>
                </div>
                <h4 class="page-title">购买日志</h4>
            </div>
        </div>
    </div>
<?php
try {
    if(strtolower(config('server_files')) != 'igcn') throw new Exception("该记录不适用于您现在的数据库!");

    global $serverGrouping;
    foreach ($serverGrouping AS $key=>$item) {
        $database = Connection::Database('MuOnline', $key);
        $ShopYbLog[] = $database->query_fetch("SELECT TOP 50 * FROM T_InGameShop_Log where CoinType = 1 ORDER BY BuyDate DESC");
        $ShopJfLog[] = $database->query_fetch("SELECT TOP 50 * FROM T_InGameShop_Log where CoinType = 0 ORDER BY BuyDate DESC");
    }

?>
    <div class="card">
        <div class="card-body">
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" href="#yb" data-toggle="tab" aria-expanded="true">元宝消费</a></li>
        <li class="nav-item">
            <a class="nav-link" href="#jf" data-toggle="tab" aria-expanded="false">积分消费</a>
        </li>
    </ul>
            <div class="tab-content">
            <div class="tab-pane fade active show" id="yb">
<?php
    if (empty($ShopYbLog)) throw new Exception("数据库中没有购买交易!");
	echo '<table  id="datatable" class="table table-bordered table-condensed table-hover text-center">';
	echo '<thead>';
		echo '<tr>';
			echo '<th class="text-center">账号</th>';
			echo '<th class="text-center">ID1</th>';
			echo '<th class="text-center">ID2</th>';
			echo '<th class="text-center">ID3</th>';
			echo '<th class="text-center">价格</th>';
			echo '<th class="text-center">类型</th>';
            echo '<th class="text-center">日期</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	foreach($ShopYbLog as $aData) {
	    foreach ($aData as $data) {
            echo '<tr>';
            echo '<td>' . $data['AccountID'] . '</td>';
            echo '<td>' . $data['ID1'] . '</td>';
            echo '<td>' . $data['ID2'] . '</td>';
            echo '<td>' . $data['ID3'] . '</td>';
            echo '<td>' . $data['Price'] . '</td>';
            echo '<td>' . ($data['CoinType'] == 0 ? '积分' : '元宝') . '</td>';
            echo '<td>' . $data['BuyDate'] . '</td>';
            echo '</tr>';
        }
	    }
	echo '
	</tbody>
	</table>';
	?>
            </div>
            <div class="tab-pane fade" id="jf">
<?php
                if (empty($ShopJfLog)) throw new Exception("数据库中没有购买交易!");
                echo '<table id="datatable1" class="table table-bordered table-condensed table-hover text-center">';
                echo '<thead>';
                echo '<tr>';
                echo '<th class="text-center">账号</th>';
                echo '<th class="text-center">ID1</th>';
                echo '<th class="text-center">ID2</th>';
                echo '<th class="text-center">ID3</th>';
                echo '<th class="text-center">价格</th>';
                echo '<th class="text-center">类型</th>';
                echo '<th class="text-center">日期</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach($ShopJfLog as $aData) {
                    foreach ($aData as $data) {
                        echo '<tr>';
                        echo '<td>' . $data['AccountID'] . '</td>';
                        echo '<td>' . $data['ID1'] . '</td>';
                        echo '<td>' . $data['ID2'] . '</td>';
                        echo '<td>' . $data['ID3'] . '</td>';
                        echo '<td>' . $data['Price'] . '</td>';
                        echo '<td>' . ($data['CoinType'] == 0 ? '积分' : '元宝') . '</td>';
                        echo '<td>' . $data['BuyDate'] . '</td>';
                        echo '</tr>';
                    }
                }
                echo '
	</tbody>
	</table>';
                ?>
            </div>
            </div>
	</div>
</div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}
?>
<script>
    $(document).ready(function(){
            $('#datatable1').DataTable({
                "oLanguage": {  //对表格国际化
                    "sLengthMenu": "每页显示 _MENU_ 条",
                    "sZeroRecords": "没有找到符合条件的数据",
                    "sInfo": "当前页第 _START_ - _END_ 条　共计 _TOTAL_ 条",
                    "sInfoEmpty": "木有记录",
                    "sInfoFiltered": "(从 _MAX_ 条记录中过滤)",
                    "sSearch": "搜索：",
                    "oPaginate": {
                        "sFirst": "首页",
                        "sPrevious": "前一页",
                        "sNext": "后一页",
                        "sLast": "尾页"
                    }
                }
            })
        }
    );
</script>
