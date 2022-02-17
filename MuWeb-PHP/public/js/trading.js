
$.getScript(baseUrl+"public/js/jquery.qrcode.min.js");

$.charSetting={
    "autoWidth": false,
    "bProcessing": true,
    "bStateSave": true,//状态保存
    "ajax":{  
	  url:baseUrl+"api/ext/trading.php",
	  type:"get",
	  dataSrc:function(data){
		   console.log(data);
		   if(data.data){
			   return data.data;
		   }else{
			   return [];
		   }
		  },  
	 
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
        {"title":"类型", "data": "avatar" ,"orderable" : false},
        {"title":"职业", "data": "class" , "sClass" :"hidden"},
        {"title":"角色名", "data": null, "orderable" : false,"render":function (data,type,row) {
                return '<a data-toggle="tooltip" data-placement="right" title="点击查看详情" href="javascript:;" onclick="trading(\'' + row.uid + '\',\'view\')" >'+row.name+'</a>';
            }},
        {"title":"等级", "data": "cLevel" },
        {"title":"大师", "data": "mLevel" },
        {"title":"售价", "data": "price" },
        {"title":"所属大区", "data": "servercode"},
        {"title":"联系QQ", "data": "tencent", "orderable":false ,"render": function (data,type,row) {
			    return '<a href="tencent://message/?uin='+row.tencent+'&Site=&Menu=yes">'+row.tencent+'</a>'
            }   },
        {"title":"查看", "data": null, "orderable" : false, "render": function (data, type, row) {
                return '<a href="javascript:;" onclick="trading(\'' + row.uid + '\',\'view\')" style="font-size: 20px"><i class="fa fa-eye" aria-hidden="true"></i></a>';
            }},
        {"title":"操作","data": null, "orderable" : false, "render": function (data, type, row) {
                if(row.my){
                    return '<button type="button" class="btn btn-warning" onclick="trading(' + row.uid + ',\'trading-offshelf\')"><i class="fa fa-download" aria-hidden="true"></i> 下架</button>';
                }else{
                    const icon = row.pass ? '<i class="fa fa-unlock-alt" aria-hidden="true"></i>' : '<i class="fa fa-qrcode" aria-hidden="true"></i>';
                    const gType = row.pass ? "pass" : "buy";
                    return '<button type="button" class="btn btn-success" onclick="trading(' + row.uid + ',\''+gType+'\')">' + icon + ' 购买</button>';
                }
            }},
    ],
    "dom": "l<'#toolbar'>frtip",
    initComplete: function () {
        $("#toolbar").css("width", "100px").css("display", "inline").css("margin-left", "20px");
        $("#toolbar").append("筛选:（基于现有）");
        var that = this;
        for (var i = 0;i< rankingIndex.length;i++){
            selectApi(that,rankingIndex[i],rankingTitle[i]);
        }
		
    },
    "drawCallback": function( settings ) {
        $("[data-toggle='tooltip']").tooltip();	//tooltip
    }
};


var tradingChar = $('#trading-character').DataTable($.charSetting);
var searchKey="";



$.dataTablesSettings ={
    "autoWidth": false,
    "bProcessing": true,
    "bStateSave": true,//状态保存
	"ajax":{  
	  url:baseUrl+"api/ext/trading.php",
	  type:"get",
	  data:function(d){
				var skill_=$("#skill_").is(':checked')?'1':'0';
				var lucky_=$("#lucky_").is(':checked')?'1':'0';
				var set_=$("#set_").is(':checked')?'1':'0';
				var level_=$("#level_").val();
				if(level_=="0"){
					level_="";
				}
				var itemName_=$("#itemName_").val();
				if(searchKey){
					itemName_=searchKey;
				}
				var ext_=$("#ext_").val();
				if(ext_=="0"){
					ext_="";
				}
				var pos_=$("#pos_").val();//部位
				var servercode_=$("#servercode_").val();//部位
				/*
				var equment0=$("#equment_0").is(':checked')?'4':'0';
				var equment1=$("#equment_1").is(':checked')?'4':'0';
				var equment2=$("#equment_2").is(':checked')?'4':'0';
				var equment3=$("#equment_3").is(':checked')?'5':'0';
				var equment4=$("#equment_4").is(':checked')?'10':'0';
				var equment5=$("#equment_5").is(':checked')?'40':'0';
				var equment6=$("#equment_6").is(':checked')?'10':'0';
				var equment7=$("#equment_7").is(':checked')?'20':'0';
				var equment8=$("#equment_8").is(':checked')?'2':'0';
				var equment9=$("#equment_9").is(':checked')?'7':'0';
				var equment10=$("#equment_10").is(':checked')?'1':'0';
				var equment11=$("#equment_11").is(':checked')?'1':'0';
				*/
				
				var equment0=$("#equment_0").is(':checked')?'1':'0';
				var equment1=$("#equment_1").is(':checked')?'1':'0';
				var equment2=$("#equment_2").is(':checked')?'1':'0';
				var equment3=$("#equment_3").is(':checked')?'1':'0';
				var equment4=$("#equment_4").is(':checked')?'1':'0';
				var equment5=$("#equment_5").is(':checked')?'1':'0';
				var equment6=$("#equment_6").is(':checked')?'1':'0';
				var equment7=$("#equment_7").is(':checked')?'1':'0';
				var equment8=$("#equment_8").is(':checked')?'1':'0';
				var equment9=$("#equment_9").is(':checked')?'1':'0';
				var equment10=$("#equment_10").is(':checked')?'1':'0';
				var equment11=$("#equment_11").is(':checked')?'1':'0';
				
				var socket0=$("#socket_0").is(':checked')?'1':'0';
				var socket1=$("#socket_1").is(':checked')?'1':'0';
				var socket2=$("#socket_2").is(':checked')?'1':'0';
				var socket3=$("#socket_3").is(':checked')?'1':'0';
				var socket4=$("#socket_4").is(':checked')?'1':'0';
				d.skill=skill_;
				d.lucky=lucky_;
				d.set=set_;
				d.level=level_;
				d.itemName=itemName_;
				d.ext=ext_;
				d.pos=pos_;
				d.servercode=servercode_;
				d.equment0=equment0;
				d.equment1=equment1;
				d.equment2=equment2;
				d.equment3=equment3;
				d.equment4=equment4;
				d.equment5=equment5;
				d.equment6=equment6;
				d.equment7=equment7;
				d.equment8=equment8;
				d.equment9=equment9;
				d.equment10=equment10;
				d.equment11=equment11;
				d.socket0=socket0;
				d.socket1=socket1;
				d.socket2=socket2;
				d.socket3=socket3;
				d.socket4=socket4;
				return d;
        },
	  dataSrc:function(data){
		   console.log(data);
		   if(data.data){
			   return data.data;
		   }else{
			   return [];
		   }
		},  
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
        {"title":"No" ,"data":"no"},
		
        {"title":"物品", "data": null, "orderable":false, "render": function (data,type,row) {
                return '<div class="data-info data-select" data-info="'+ row.item_code +'"><img src="'+ row.item_img +'" style="max-width: 35px;max-height: 35px" /></div>';
            }},
        {"title":"物品类型", "data": "item_type"},
        {"title":"物品名称", "data": "html_item_name"},
        {"title":"物品编号", "data": null, "render": function (data,type,row) {
			    return '<div data-toggle="tooltip" data-placement="right" data-html="true" title="<img src=\''+baseUrl+'/public/img/Top/profile/ajax-loader.gif\' alt=\'\'>">'+ data.serial +'</div>'
            }},
        {"title":"卖家", "data": "name"},
        {"title":"售价", "data": "price" },
        {"title":"所属大区", "data": "servercode"},
	    {"title":"联系QQ", "data": "tencent", "orderable":false ,"render": function (data,type,row) {
			    return '<a href="tencent://message/?uin='+row.tencent+'&Site=&Menu=yes">'+row.tencent+'</a>'
            }   },
        {"title":"操作","data": null, "orderable":false, "render": function (data, type, row) {
                if(row.my){
                    return '<button type="button" class="btn btn-warning" onclick="trading(' + row.uid + ',\'trading-offshelf\')"><i class="fa fa-download" aria-hidden="true"></i> 下架</button>';
                }else {
                    const icon = row.pass ? '<i class="fa fa-unlock-alt" aria-hidden="true"></i>' : '<i class="fa fa-qrcode" aria-hidden="true"></i>';
                    const stat = row.pass ? '' : ' <button type="button" class="mr-2 btn btn-outline-warning" onclick="buy_cart(\'' + row.uid + '\',this)"><i class="fa fa-cart-plus" aria-hidden="true"></i> 加入购物车</button>';
                    const gType = row.pass ? "pass" : "buy";
                    return stat + '<button type="button" class="btn btn-success" onclick="trading(\'' + row.uid + '\',\''+gType+'\')">' + icon + ' 购买</button>';
                }
            }},
		
			
			],
    //"dom": "lfrtip<'#toolbar'>",
	 "dom": "tlpr",
	 
    "initComplete": function () {
	
       },
    "drawCallback": function() {
        $("[data-toggle='tooltip']").tooltip();	//tooltip
        $(function() {altTooltip.init();});

    }
}

var tradingItem = $('#trading-item').DataTable($.dataTablesSettings);

function setServerInfo(data){
	
	  $("#servercode_").empty();
	  for(var i=0;i<data.length;i++){
			   $("#servercode_").append("<option value='"+data[i].SERVER_GROUP+"'>"+data[i].SERVER_NAME+"</option>");
	  }
	
}

function getItemInfo(){
	
	var list=[];
	list[0]="玛雅之石";
	list[1]="祝福宝石";
	list[2]="灵魂宝石";
	list[3]="生命宝石";
	list[4]="创造宝石";
	list[5]="再生原石";
	list[6]="守护宝石";
	list[7]="洛克之羽";
	list[8]="国王卷轴";
	list[9]="神鹰火种";
	list[10]="神鹰之羽";
    list[11]="之翼";
    list[12]="披风";
    list[13]="项链";
    list[14]="指环";

     var h='';
	for(var i=0;i<list.length;i++){
		h=h+'<button type="button" class="mr-2 mt-2 btn btn-warning  btn-sm"  onclick="searchItem({itemName:\''+list[i]+'\'});">'+list[i]+'</button>';
	}
	return h;
	
}



$(document).ready(function(){
	
	 var line1='<button type="button" class="mr-2  mt-2  btn btn-info btn-sm leftbutton" style="width:80px" > 全部 </button>';
		line1=line1+'<label >等级:</label >';
		line1=line1+'<select style="border: 1px solid #aaa;border-radius: 3px;padding: 4px;" class="custom-select-sm" id="level_" >';
		for(var i=0;i<=15;i++){
		line1=line1+'<option value=\''+i+'\'>+'+i+'</option>';
		}
		line1=line1+'</select>';
		line1=line1+'<label >追加</label>';
		line1=line1+'<select style="border: 1px solid #aaa;border-radius: 3px;padding: 4px;" class="custom-select-sm" id="ext_"><option value=0>z0</option><option value=1>z4</option><option value=2>z8</option><option value=3>z12</option><option value=4>z16</option><option value=5>z20</option><option value=6>z24</option><option value=7>z28</option></select>';
		line1=line1+' <div class="form-check form-check-inline">';
		line1=line1+'<label  class="form-check-label ">幸运</label><input type="checkbox" class="form-check-input"  id="lucky_">';
		line1=line1+'<label  class="form-check-label ">技能</label><input type="checkbox" class="form-check-input" id="skill_">';
		line1=line1+'<label  class="form-check-label ">套装</label><input type="checkbox" class="form-check-input" id="set_">';
		line1=line1+'';
		line1=line1+'</div>';
		// 不适用,0:左手,1:右手,2:头盔,3:铠甲,4:护腿,5:护手,6:靴子,7:翅膀,8:帮（宠物),9:项链,10:左戒指,11:右戒指,236:元素卷轴,237:耳环,238:耳环
		line1=line1+'部位选择:<select style="border: 1px solid #aaa;border-radius: 3px;padding: 4px;" class="custom-select-sm" id="pos_" ><option value="">全部</option><option value="0">武器</option><option value="7">头盔</option><option value="8">衣服</option><option value="9">护腿</option><option value="10">护手</option><option value="11">鞋子</option></select>';
		
		line1=line1+'物品名称:<input type="text" id="itemName_"  class="custom-input-sm"  style="width:80px;border: 1px solid #aaa;border-radius: 3px;padding: 4px;"> ';
		line1=line1+'游戏分区<select  class="custom-select-sm" id="servercode_"  style="border: 1px solid #aaa;border-radius: 3px;padding: 4px;"></select> ';
		
	
		
		var line2='';
		var property = new Array();
		property[0]="生命值 +4%"
		property[1]="魔法值 +4%"
		property[2]="伤害减少 +4%"
		property[3]="伤害反射 +5%"
		property[4]="防御成功率 +10%"
		property[5]="金钱增加 +40%"
		for(var i=0;i<property.length;i++){
	      	line2=line2+'<label  class="form-check-label "><input type="checkbox" id="equment_'+i+'"   class="form-check-input">'+ property[i]+'&nbsp;</label >';
		}
	    var my='';
		my=my+'<button type="button" class="btn btn-danger btn-sm"   id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">我的</button>';
		my=my+'<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">';
		my=my+      '<a class="dropdown-item" href="javascript:" onclick="trading(0,\'my-trading\')">在售物品</a>';
		my=my+       '<a class="dropdown-item" href="javascript:" onclick="trading(0,\'my-buy-log\')">售出记录</a>';
		my=my+       '<a class="dropdown-item" href="javascript:" onclick="trading(0,\'my-sell-log\')">购买记录</a>';
		my=my+'</div>';
		my=my+'';
		
		line2='<div class="row" ><div class="col-sm-10"   ><button type="button" class="mr-2  mt-2  btn btn-info btn-sm leftbutton"  style="width:80px" >防具/指环</button><span id="container_2"><label >属性:</label ><div class="form-check form-check-inline">'+line2+'</div></span></div>'+'<div class="col-sm-2" ><button type="button" class="btn btn-warning btn-sm mr-1"  id="btn_search">搜索物品</button>'+my+'</div></div>';
		
		var line3=''
		var in_ = new Array();
		in_[0]='1孔';
		in_[1]='2孔';
		in_[2]='3孔';
		in_[3]='4孔';
		in_[4]='5孔';
		for(var i=0;i<in_.length;i++){
		  	line3=line3+'<label  class="form-check-label  "><input type="checkbox" id="socket_'+i+'" class="form-check-input">'+in_[i]+'&nbsp;&nbsp;</label >';
		}
		line3='<div class="row"><div class="col-sm-10"  ><button type="button" class="mr-2  mt-2  btn btn-info btn-sm leftbutton" style="width:80px" >武器项链</button> <label>镶嵌:</label><div class="form-check form-check-inline">'+line3+'</div></div><div class="col-sm-2" ><button type="button" class="mr-1 btn btn-warning btn-sm"  onclick="clearSearch()"  >清除属性</button><button type="button" class=" btn btn-danger btn-sm"  onclick="trading(0,\'sell\')" >寄售</button></div></div>';
		var line4='';
		$("#searchContainer").html('<div class="container">'+line1+'</div>'+'<div class="container">'+line2+'</div>'+'<div class="container">'+line3+'</div>'+'<div class="container"  id="container4_"><button type="button" class="mr-2  mt-2 btn btn-info btn-sm  leftbutton" style="width:80px"  onclick="searchAll();" >全部道具</button>'+getItemInfo()+'</div>');
		
		
		
		
		
		
		$('#btn_search').click(function () {
	    		searchItem({});
		});
		
		
		$(".leftbutton").click(function(){
			
			
			$(".leftbutton").each(function(){
				if($(this).hasClass("btn-primary")){
					$(this).removeClass("btn-primary");
				}
				$(this).addClass("btn-info");
			});
			//
			if($(this).hasClass("btn-info")){
				$(this).removeClass("btn-info");
			}
			$(this).addClass("btn-primary");
			
			if($(this).text()=="防具/指环"){
				    var line2='';
					var property = new Array();
					property[0]="生命值 +4%"
					property[1]="魔法值 +4%"
					property[2]="伤害减少 +4%"
					property[3]="伤害反射 +5%"
					property[4]="防御成功率 +10%"
					property[5]="金钱增加 +40%"
					
					for(var i=0;i<property.length;i++){
						line2=line2+'<label  class="form-check-label "><input type="checkbox" id="equment_'+i+'"   class="form-check-input">'+ property[i]+'&nbsp;</label >';
					}
					$("#container_2").html('<label >属性:</label ><div class="form-check form-check-inline">'+line2+'</div>');
			}
			
			
			if($(this).text()=="武器项链"){
				    var line2='';
					var property = new Array();
					property[0]="卓越一击 +10%"
					property[1]="攻击力增加 +等级/20%"
					property[2]="攻击力 +2%"
					property[3]="攻击速度 +7"
					property[4]="杀怪回血"
					property[5]="杀怪回蓝"
					for(var i=0;i<property.length;i++){
						j=6+i;
						line2=line2+'<label  class="form-check-label "><input type="checkbox" id="equment_'+j+'"   class="form-check-input">'+ property[i]+'&nbsp;</label >';
					}
					$("#container_2").html('<label >属性:</label ><div class="form-check form-check-inline">'+line2+'</div>');
			}
			
			
			
		});
		
		
		
		
		if( window.location.href.indexOf("market/item")!=-1){
		
			var serverInfo_ =getSession("serverInfo_");
            if(serverInfo_!=null){
				setServerInfo(serverInfo_);
			}
			else{
				 $.ajax({
					url:baseUrl+"api/ext/trading.php",
					type:"POST",
					data:{modules:"serverInfo"},
					dataType:"json",
					success:function (data) {
						if("10000" === data.code){
							setServerInfo(data.data);
							setSession("serverInfo_",data.data);
							
						}else{
							modal_msg(data.msg);
						}
					}
				});
			}
		
		
		}
		
	
	
	
});

//清空表单
function clearSearch(){
	    	$("#level_ option:first").prop("selected", 'selected'); 
			$("#ext_ option:first").prop("selected", 'selected'); 
			$("#pos_ option:first").prop("selected", 'selected');
			$("#servercode_ option:first").prop("selected", 'selected');
			
			$("#skill_").attr('checked',false);
			$("#lucky_").attr('checked',false);
			$("#set_").attr('checked',false);
			$("#itemName_").val("");
			$("#equment_0").attr('checked',false);
			$("#equment_1").attr('checked',false);
			$("#equment_2").attr('checked',false);
			$("#equment_3").attr('checked',false);
			$("#equment_4").attr('checked',false);
			$("#equment_5").attr('checked',false);
			
			$("#socket_0").attr('checked',false);
			$("#socket_1").attr('checked',false);
			$("#socket_2").attr('checked',false);
			$("#socket_3").attr('checked',false);
			$("#socket_4").attr('checked',false);
			searchKey="";
}


function searchItem(params){
	if(params.itemName){
		searchKey=params.itemName;
	}else{
		searchKey="";
		
	}
	tradingItem.ajax.reload();
		
}

function searchAll(){
	clearSearch();
	tradingItem.ajax.reload();
		
}


function trading(id,modules){
    var pass = $('#password').val();
    var cartArray = [];
    if("cart-clear" === modules){
        var leng = $("#cart-item tbody tr").length;
        for(var i=0; i<=leng; i++)
        {
            var numberStr = $("#cart-item tbody tr").eq(i).find("td:first").html();
            cartArray.push(numberStr);
        }
        if($("#cart-item tbody").text().length == 0) return commonUtil.message('购物车空无法进行结算','danger');
    }
    $.ajax({
        url:baseUrl+"api/ext/trading.php",
        beforeSend:function(){$('#loading').show()},
        complete:function(){$('#loading').hide()},
        type:"POST",
        data:{id:id,modules:modules,password:pass,cart:cartArray},
        dataType:"json",
        success:function (data) {
            if("10000" === data.code){
                $('body').append(data.data);
            }
			else if("20008"=== data.code){//支付状态
			    sessionStorage.setItem("dir", window.location.href);
			    $(document.body).append(data.data);
            }
			else{
                modal_msg(data.msg);
            }
        }
    });
}

function market_sell(modules) {
    var typeName = "角色";
    if(modules === "sell-item"){
        typeName = "物品";
    }
    var item = $('#item').val();
    var char = $('#char_name').val();
    var pass = $('#password').val();
    var price = $('#price').val();
    var tencent = $('#tencent').val();
    if (char == null) return commonUtil.message('暂无'+typeName+'可用于寄售!','danger');
    if (!(/^[A-Za-z0-9]{7,8}$/.test(char))) return commonUtil.message(typeName+'识别错误，请联系在线客服。','danger');
    if (pass && !(/^[0-9]{4,8}$/.test(pass))) return commonUtil.message('交易密码必须是[4~8]位数字!','danger');
    if (price == null) return commonUtil.message('请输入售出价格!','danger');
    if (price < 1 || price > 5000) return commonUtil.message('出售价格仅支持[1~5000]元!','danger');
    if (!(/^[0-9]{1,4}$/.test(price))) return commonUtil.message('出售价格仅支持[1~5000]元!','danger');
    if (tencent == null) return commonUtil.message('请输入联系QQ!','danger');
    if (!(/^[0-9]{5,13}$/.test(tencent))) return commonUtil.message('联系QQ必须是[5~13]位数字号码!','danger');
	
	
	
	
    $.ajax({
        url:baseUrl+"api/ext/trading.php",
        beforeSend:function(){$('#btn-sell').attr('onclick','javascript:void();');$('#loading').show()},
        complete:function(){$('#loading').hide()},
        type:"POST",
        data:{char:char,pass:pass,price:price,tencent:tencent,modules:"selling",item:item},
        dataType:"json",
        success:function (data) {
            if("10000" === data.code){
				console.log(tradingItem.ajax);
			    tradingChar.ajax.reload();
                tradingItem.ajax.reload();
                $('body').append(data.data);
            }
			
			else{
				modal_msg(data.msg);
            }
        }
    });
	
	
	
	
	
}

function getResult(id,cartArray= []){
	
	/*
    var tradeOutTime = 122;
    function reqs() {
        tradeOutTime = tradeOutTime - 2;
        $.ajax({
            type: 'POST',
            url: baseUrl+"api/ext/result.php",
            dataType: 'json',
            data:{id:id,cart:cartArray},
            success: function(data) {
                if("10000" == data.code){
                    tradingChar.ajax.reload();
                    tradingItem.ajax.reload();
                    $("#cart-item tbody").empty();
                    $("#cart #cart-count").empty();
                    $("#cart #cart-count").append("<div><span>0</span></div>");
                    modal_msg(data.data);
                    clearInterval(Interval);
                }else{
                    $('body').append(data.msg);
                    clearInterval(Interval);
                }
            }
        });
    }

    reqs();
    var Interval = setInterval(function() {
        $("#trading-buy").length > 0 &&　tradeOutTime > 0 && reqs();
        if (0 >= tradeOutTime){
            $(".qrcode-time-content").empty();
            $(".qrcode-time-content").html("<span class='text-danger'>该二维码已过有效期请重新打开</span>");
            clearInterval(Interval);
        }
    }, 2000);
	*/

}


function getQrCode(id,cartArray= [],buy_char) {
    $.ajax({
        type: 'POST',
        url: baseUrl+'api/ext/trading.php',
        dataType: 'json',
        data:{id:id,modules:"qrcocde",cart:cartArray,buy_char:buy_char},
        success: function(data) {
            if('10000' === data.code){
                $('#qr-code-content').empty();
                $('#qr-code-content').append(data.data);
            }else{
                modal_msg(data.msg);
            }
        }
    });
}

var linkdata="";


function getQrCode(id,cartArray= []) {
	
	$.ajax({
        type: 'POST',
        url: baseUrl+'api/ext/trading.php',
        dataType: 'json',
        data:{id:id,modules:"qrcocde",cart:cartArray},
        success: function(data) {
            
			if('10000' === data.code){
			    linkdata=data.data;
				if( window.location.href.indexOf("market/item")!=-1){
					$('#qr-code-content').empty();
					$('#charDlog').modal('show');
					$('#trading-buy').modal('hide');
				}else{
					 $('#qr-code-content').empty();
					 $('#qr-code-content').append(data.data);
					
				}
			
		  
            }else{
                modal_msg(data.msg);
            }
        }
    });
	
	
	
}

//获得角色列表
function getChar(){
	$.ajax({
					url:baseUrl+"api/ext/trading.php",
					type:"POST",
					data:{modules:"getChar"},
					dataType:"json",
					success:function (data) {
						if("10000" === data.code){
							     
								data=data.data;
								$("#character").empty();
								for(var i=0;i<data.length;i++){
								   $("#character").append("<option value='"+data[i].value+"'>"+data[i].value+"</option>");
								}
							
							
						}else{
							modal_msg(data.msg);
						}
					}
		});
	
	
}

//保存角色
function saveChar(){
	
	if(!$("#character").val()){
		modal_msg("角色不能为空");
	}
	$.ajax({
					url:baseUrl+"api/ext/trading.php",
					type:"POST",
					data:{modules:"saveChar",character_name:$("#character").val()},
					dataType:"json",
					success:function (data) {
						console.log(data.data);
						if("10000" === data.code){
							$('#charDlog').modal('hide');
							if(linkdata){
							 $('body').append(linkdata);
							}
						}else{
							modal_msg("请选择角色");
						}
					}
				});
}


function buy_cart(id,btn) {
    if($("#cart-item tbody").text().length > 0){
        var len = $("#cart-item tbody tr").length;
        for(var i=0; i<=len; i++)
        {
            var numberStr = $("#cart-item tbody tr").eq(i).find("td:first").html();
            if(id === numberStr) return commonUtil.message('购物车已有该物品','danger','body');
        }
    }
    var tds = $(btn).parent().siblings();//获取当前元素的父节点的全部兄弟节点，就是当前这行的所有td
    var itemImg = $(tds).eq(1).html();//获取商品类型的td的文本值
    var itemType = $(tds).eq(2).text();//获取商品类型的td的文本值
    var itemName = $(tds).eq(3).html();//获取商品名称的td的文本值
    var itemPrice = $(tds).eq(6).text();//获取商品价格的td的文本值
    var itemPriceCount = parseInt($("#cart #cart-count span").text()) + parseInt(itemPrice.substring(1,itemPrice.length-3));
    var cart = $("#cart-number");
    cart.show();
    const cartNumber = parseInt(cart.text()) + 1;
    if(cartNumber > 10) return commonUtil.message('购物车每次最多可购买10件道具。','danger','body');
    cart.empty();
    cart.append(cartNumber);
    commonUtil.message('添加购物车成功!','success','body');
    //拼接到购物车
    var html = '<tr><td id="cart-id" hidden>'+id+'</td><td>'+itemImg+'</td><td>'+itemType+'</td><td>'+itemName+'</td><<td>'+itemPrice+'</td><td><button type="button" class="btn btn-danger" onclick="deleteShopping(this)">删除</button></td></tr>';
    $("#cart-item tbody").append(html);
    $("#cart #cart-count").empty();
    $("#cart #cart-count").append("<div>总计: <span>"+itemPriceCount+"</span>.00元("+cartNumber+"件道具)</div>");
}

function deleteShopping(btn){//给上一步你拼接的删除按钮上绑定一个这样的方法
    var tds = $(btn).parent().siblings();
    var itemPrice = $(tds).eq(4).text();//获取商品价格的td的文本值
    var itemPriceCount = parseInt($("#cart #cart-count span").text()) - parseInt(itemPrice.substring(1,itemPrice.length-3));
    var cart = $("#cart-number");
    var number = cart.text();
    var cartNumber = parseInt(number) - 1;
    if(0 === cartNumber) cart.hide();
    $("#cart #cart-count").empty();
    cart.empty();
    cart.append(cartNumber);
    $(btn).parent().parent().remove();
    $("#cart #cart-count").append("<div>总计: <span>"+itemPriceCount+"</span>.00元("+cartNumber+"件道具)</div>");
}

    $(function() {
        $("#cart-btn").click(function(){
            if($("#cart-item tbody").text().length == 0)return commonUtil.message('购物车目前没有物品','danger','body');
            $("#cart").modal('show');
        });
    });
