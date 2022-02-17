<?php
/**
 * 交易市场插件-卖模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>market">交易市场</a></li>
            <li class="breadcrumb-item active" aria-current="page">物品市场</li>
        </ol>
    </nav>
<?php
try {
    $market = new \Plugin\Market\Market();
    $moduleConfig = $market->loadConfig('item');
    $market->MarketMenu();
    if(!$moduleConfig['active']) throw new Exception('未开放物品交易市场！');
    if($moduleConfig['active_login']) if(!isLoggedIn()) throw new Exception('该模块仅限会员可见，请先<a href="'.__BASE_URL__.'login">登陆账号</a>！');

    ?>
    <div class="spinner-border text-warning" id="loading" role="status" style="position: fixed;z-index: 9999;right: 2rem;bottom: 2rem;display:none">
        <span class="sr-only">Loading...</span>
    </div>

    <div class="card mb-3">
        <div class="card-header mb-2">物品市场</div>
        <?if(isLoggedIn()){?>
		<!--
        <div class="col-md-12 mt-2">
            <div class="form-group row justify-content-md-end">
				
				
                <div class="" style="padding-left: 5px;padding-right: 5px;">
                    <div class="dropdown">
                        <a class="btn btn-warning dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                            我的
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="javascript:" onclick="trading(0,'my-trading')">在售物品</a>
                            <a class="dropdown-item" href="javascript:" onclick="trading(0,'my-buy-log')">售出记录</a>
                            <a class="dropdown-item" href="javascript:" onclick="trading(0,'my-sell-log')">购买记录</a>
                        </div>
                    </div>
                </div>
				
                <div class="sell_char" style="padding-left: 5px;padding-right: 5px;">
                    <button class="btn btn-danger" type="button" onclick="trading(0,'sell')">寄售物品</button>
                </div>
			
            </div>
        </div>	-->
        <?}?>
		<div id="searchContainer"></div>

        <table id="trading-item" class="mt-2 table table-bordered table-striped text-center" style="width: 100%;"></table>
        <footer class=" text-right mt-3 mb-3 mr-3">
            默认跟随玩家发布时间从新至旧排序
        </footer>
    </div>

    <div class="position-fixed" style="right: 3rem;bottom: 3rem;z-index: 100;">
        <button  id="cart-btn" class="btn btn-warning" style="font-size: 25px">
            <span id="cart-number" class="badge badge-danger rounded-circle position-absolute" style="right: -20px;top:-10px;display: none;">0</span>
            <i class="fa fa-shopping-cart" data-toggle="tooltip" data-placement="right" title="购物车结账"></i> 结账
        </button>
    </div>

    <div class="modal fade" id="cart" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">购物车</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="">
                    <table  id="cart-item" class="table table-bordered table-striped text-center text-dark table-sm">
                        <thead>
                        <tr>
                            <th hidden>ID</th>
                            <th>物品</th>
                            <th>类型</th>
                            <th>名称</th>
                            <th>价格</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <div id="cart-count"><span>0</span></div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                    <button type="button" class="btn btn-success" onclick="trading(0,'cart-clear')"><i class="fa fa-qrcode" aria-hidden="true"></i> 结算</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('#cart').on('shown.bs.modal', function () {
            altTooltip.init();
        });
      
	  
	
	  
	  
	  $(document).ready(function(){
		 
		  let html = '<div class="modal fade" id="charDlog" tabindex="-1" >' +
		  	'<div class="modal-dialog modal-dialog-centered" style="max-width:650px" >' +
		  	'<div class="modal-content" >' +
		  	'<div class="modal-header">' +
		  	'<h5 class="modal-title">选择角色</h5>' +
		  	'<button type="button" class="close" data-dismiss="modal">' +
		  	'<span aria-hidden="true">&times;</span>' +
		  	'</button>' +
		  	'</div>' +
		  	'<div class="modal-body"><div class="table-responsive  cartTable"> <select id="character" name="character_name" class="form-control"> </select></div></div>' +
		  	'<div class="modal-footer">' +
		  	'<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="saveChar()" class="btn btn-success btn-sm ">确认选择</button>' +
		  	'</div>' +
		  	'</div>' +
		  	'</div>' +
		  	'</div>';
		  		
		  $('body').append(html);
		  getChar();
		  
		  
		  
	  })
	  
	  
	  
	  
    </script>
	<script type="text/javascript">
        var rankingIndex = [1,6];
        var rankingTitle = ['所有职业','所有大区'];
    </script>
    <script type="text/javascript" src="<?=__PATH_PUBLIC_JS__?>trading.js"></script>
    <?php
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}