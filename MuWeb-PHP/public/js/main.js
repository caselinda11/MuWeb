
$(function() {
	// 启动服务器时间
	serverTime.init("tServerTime", "tLocalTime", "tServerDate", "tLocalDate");
	// 启动罗兰攻城倒计时
	if($('#csCountDown').length) {castleSiege.init();}

	$("[data-toggle='tooltip']").tooltip();	//tooltip
});

var eventsTime = {
	isFirst: false,
	events_time: [],
	loadEventSchedule: function () {
		$.ajax({
			url: baseUrl + "api/events.php",
			success: function (data) {
				if (eventsTime.isFirst === false) {
					$.each(data, function(key, val) {
						eventsTime.eventSchedule('event'+key, val.opentime, val.duration, val.offset, val.timeleft);
						newDiv = $("<li  class='list-group-item list-group-item-action d-flex justify-content-between align-items-center' />");
						newDiv.append($("<div />").text(val.event));
						// newDiv.append($("<sup />").text(val.nextF));
						newDiv.append($("<div />").attr({id: 'event' + key}).text(val.next));
						$("#events").append(newDiv);
					});
					eventsTime.isFirst = true;
				}
			},
			error: function () {
				$("#events").append($('<div class="text-center" />').text('事件文件有错误，请重新配置。'));
			}
		});
	},
	eventSchedule: function(eventId, openTime, duration, offset, timeLeft) {
		var eHours = null;
		var eMinutes = null;
		var eSeconds = null;

		function init() {
			setInterval(function() {
				update();
			}, 1000)
		}

		function update() {
			let eDays;
			if (timeLeft >= 1) {

				var days_module = timeLeft % 86400;
				eDays = (timeLeft - days_module) / 86400;
				var hours_module = days_module % 3600;
				eHours = (days_module - hours_module) / 3600;
				var minutes_module = hours_module % 60;
				eMinutes = (hours_module - minutes_module) / 60;
				eSeconds = minutes_module;

				if (eMinutes < 10) eMinutes = '0' + eMinutes;
				if (eSeconds < 10) eSeconds = '0' + eSeconds;
			} else {
				eDays = '0';
				eHours = '0';
				eMinutes = '00';
				eSeconds = '00';
			}

			if(openTime > 0) {
				if(offset-timeLeft < openTime) {
					$('#'+ eventId).html('<span class="badge badge-success">开放中</span>');
					timeLeft = timeLeft-1;
					return;
				}
			} else {
				if(duration > 0) {
					if(offset-timeLeft < duration) {
						$('#'+ eventId).html('<span class="badge badge-danger">进行中</span>');
						timeLeft = timeLeft-1;
						return;
					}
				}
			}
			if(eHours === '00' && eMinutes === '00') {
				$('#'+ eventId).html(eSeconds + " 秒");
			} else {
				if(eDays > 0) {
					$('#'+ eventId).html(eDays + " 天 " + eHours + " 时 " + eMinutes + " 分 " + eSeconds + " 秒");
				} else {
					$('#'+ eventId).html(eHours + " 时 " + eMinutes + " 分 " + eSeconds + " 秒");
				}
			}

			timeLeft = timeLeft-1;
		}

		init();
	},
};

var castleSiege = {
	csHours: null,
	csMinutes: null,
	csSeconds: null,
	csTimeLeft: null,
	init: function() {
		var a = this;
		$.getJSON(baseUrl + "api/castlesiege.php", function(data) {
			a.csTimeLeft = data.TimeLeft;
			setInterval(function() {
				a.update()
			}, 1000)
		})
	},
	update: function() {
		var b = this;
		if(b.csTimeLeft >= 1) {
			var hours_module = b.csTimeLeft % 3600;
			b.csHours = (b.csTimeLeft-hours_module)/3600;
			var minutes_module = hours_module % 60;
			b.csMinutes = (hours_module-minutes_module)/60;
			b.csSeconds = minutes_module;
		} else {
			b.csHours = 0;
			b.csMinutes = 0;
			b.csSeconds = 0;
		}
		$('#csCountDown').text(b.csHours + "时 " + b.csMinutes + "分 " + b.csSeconds + "秒");

		b.csTimeLeft = b.csTimeLeft-1;
	}
};

var serverTime = {
	weekDays: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
	monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
	serverDate: null,
	localDate: null,
	dateOffset: null,
	nowDate: null,
	eleServer: null,
	eleLocal: null,
	eleServerDate: null,
	eleLocalDate: null,
	init: function(e, c, s, l) {
		var f = this;
		f.eleServer = e;
		f.eleLocal = c;
		f.eleServerDate = s;
		f.eleLocalDate = l;
		$.getJSON(baseUrl + "api/servertime.php", function(a) {
			f.serverDate = new Date(a.ServerTime);
			f.localDate = new Date();
			f.dateOffset = f.serverDate - f.localDate;
			$('#'+ f.eleServer).html(f.dateTimeFormat(f.serverDate));
			$('#'+ f.eleLocal).html(f.dateTimeFormat(f.localDate));
			$('#'+ f.eleServerDate).html(f.dateFormat(f.serverDate));
			$('#'+ f.eleLocalDate).html(f.dateFormat(f.localDate));

			setInterval(function() {
				f.update()
			}, 1000)
		})
	},
	update: function() {
		var b = this;
		b.nowDate = new Date();
		$('#'+ b.eleLocal).html(b.dateTimeFormat(b.nowDate));
		b.nowDate.setTime(b.nowDate.getTime() + b.dateOffset);
		$('#'+ b.eleServer).html(b.dateTimeFormat(b.nowDate));
	},
	dateTimeFormat: function(e) {
		var c = this;
		var f = [];
		f.push(c.digit(e.getHours()));
		f.push(":");
		f.push(c.digit(e.getMinutes()));
		return f.join("")
	},
	dateFormat: function(e) {
		var c = this;
		var f = [];
		f.push(c.weekDays[e.getDay()]);
		f.push(" ");
		f.push(c.monthNames[e.getMonth()]);
		f.push(" ");
		f.push(e.getDate());
		return f.join("")
	},
	digit: function(b) {
		b = String(b);
		b = b.length === 1 ? "0" + b : b;
		return b
	}
};

function testAlipay(){
	
	$.ajax({
	    // 获取id，challenge，success（是否启用failback）
	    url: baseUrl +"api/test.php", // 加随机数防止缓存
	    type: "get",
	    dataType: "text",
	    success: function (data) {
	        console.log(data);
	    }
	});
	
}

function testDrop(){
	
	$.ajax({
	    // 获取id，challenge，success（是否启用failback）
	    url: baseUrl +"api/ext/drop.php", // 加随机数防止缓存
	    type: "get",
	    dataType: "json",
	    success: function (data) {
	        console.log(data);
	    }
	});
	
}





function checkUsername(){
	var servercode = $("#serverGroup").val();
	var username = $("#userName").val();
	if(!servercode || !username) return commonUtil.message('账号不能为空，请先输入账号。','danger','body');
	if (!(/^[A-Za-z0-9_]{4,10}$/.test(username))) return commonUtil.message('账号由[4-10]位字母数字或下划线组成。','danger','body');
	$.ajax({
		url:baseUrl+"api/ext/register.php",
		type:"POST",
		data:{servercode:servercode,username:username,models:'check'},
		dataType:"json",
		success:function (data) {
			if(data.code && "10000" == data.code){
				$("body").append(data.data);
			}else{
				modal_msg(data);
			}
		}
	});
}
// 提示
function modal_msg(msg) {

	if($("#operation").length>0)
	{
		$('#operation').modal('hide');
	}

	let html = '<div class="modal fade" id="message" tabindex="-1">' +
		'<div class="modal-dialog modal-dialog-centered">' +
		'<div class="modal-content">' +
		'<div class="modal-header">' +
		'<h5 class="modal-title">提示</h5>' +
		'<button type="button" class="close" data-dismiss="modal">' +
		'<span aria-hidden="true">&times;</span>' +
		'</button>' +
		'</div>' +
		'<div class="modal-body">' + msg + '</div>' +
		'<div class="modal-footer">' +
		'<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>';
	$('body').append(html);
	$('.modal').modal('hide');
	$('#message').modal('show');
	$('#message').on('hidden.bs.modal', function () {
		$('#message').remove();
	})
}

// 提示并且跳转地址
function modal_url(url,msg) {

	if($("#operation").length>0)
	{
		$('#operation').modal('hide');
	}

	let html = '<div class="modal fade" id="message" tabindex="-1">' +
		'<div class="modal-dialog modal-dialog-centered">' +
		'<div class="modal-content">' +
		'<div class="modal-header">' +
		'<h5 class="modal-title">提示</h5>' +
		'<button type="button" class="close" data-dismiss="modal">' +
		'<span aria-hidden="true">&times;</span>' +
		'</button>' +
		'</div>' +
		'<div class="modal-body">' + msg + '</div>' +
		'<div class="modal-footer">' +
		'<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>';
	$('body').append(html);
	$('.modal').modal('hide');
	$('#message').modal('show');
	$('#message').on('hidden.bs.modal', function () {
		$('#message').remove();
		window.location = url;
	})
}

// 操作提示
function modal_tip() {
	let html = '<div class="modal fade" id="operation" tabindex="-1">' +
		'<div class="modal-dialog modal-dialog-centered">' +
		'<div class="modal-content">' +
		'<div class="modal-header">' +
		'<h5 class="modal-title">提示</h5>' +
		'<button type="button" class="close" data-dismiss="modal">' +
		'<span aria-hidden="true">&times;</span>' +
		'</button>' +
		'</div>' +
		'<div class="modal-body">是否要继续操作？</div>' +
		'<div class="modal-footer">' +
		'<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="parent.window.submit()">确定</button>' +
		'<button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>';
	$('body').append(html);
	$('.modal').modal('hide');
	$('#operation').modal('show');
	$('#operation').on('hidden.bs.modal', function () {
		$('#operation').remove();
	})
}

// 操作提示2,附带参数
function modal_tip2(id) {
	let html = '<div class="modal fade" id="operation" tabindex="-1">' +
		'<div class="modal-dialog modal-dialog-centered">' +
		'<div class="modal-content">' +
		'<div class="modal-header">' +
		'<h5 class="modal-title">提示</h5>' +
		'<button type="button" class="close" data-dismiss="modal">' +
		'<span aria-hidden="true">&times;</span>' +
		'</button>' +
		'</div>' +
		'<div class="modal-body">是否要继续操作？</div>' +
		'<div class="modal-footer">' +
		'<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="parent.window.submit('+id+')">确定</button>' +
		'<button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>';
	$('body').append(html);
	$('.modal').modal('hide');
	$('#operation').modal('show');
	$('#operation').on('hidden.bs.modal', function () {
		$('#operation').remove();
	})
}

function selectApi(that,index, title) {
	that.api().columns(index).every( function () {
		var column = this;
		var select = $("<select style='border: 1px solid #aaa;border-radius: 3px;padding: 4px;' class='ml-2 mr-2'><option value=''>"+title+"</option></select>")
			.appendTo( $("#toolbar") )
			.on( 'change', function () {
				var val = $.fn.dataTable.util.escapeRegex(
					$(this).val()
				);
				column
					.search( val ? '^'+val+'$' : '', true, false )
					.draw();
			} );

		column.data().unique().sort().each( function ( d, j ) {
			select.append( '<option value="'+d+'">'+d+'</option>' )
		} );
	} );
}

//传入对象
function setSession(key,data){
	
	var d={};
	d.data=data;
	d.time=Date.now();
	sessionStorage.setItem(key,JSON.stringify(d));
	
}
//返回对象
function getSession(key){
	var s=sessionStorage.getItem(key);
	if(s){
	 d=JSON.parse(s);
     if((Date.now()-d.time)<=7200000)  	{
		 return d.data;
	 }  
	}
	return null;
}



var commonUtil = {
	/**
	 * 弹出消息框
	 * @param msg 消息内容
	 * @param type 消息框类型（参考bootstrap的alert）
	 * @param to 消息框类型（参考bootstrap的alert）
	 *
	 */
	message: function(msg, type, to) {
		if(typeof(to) =="undefined") { // 未传入type则默认为success类型的消息框
			to = ".modal";
		}
		var icon = '';
		switch (type) {
			case "success":
				icon = '<i class="fa fa-check-circle" aria-hidden="true"></i>  ';
				break;
			case "danger":
				icon = '<i class="fa fa-times-circle" aria-hidden="true"></i> ';
				break;
			case "warning":
				icon = '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>  ';
				break;
			default:
				icon = '<i class="fa fa-question-circle" aria-hidden="true"></i>  ';
				break;
		}
		// 创建bootstrap的alert元素
		var divHeader = $("<div ></div>").addClass('d-flex').addClass('justify-content-center').addClass('align-items-center');
		var divElement = $("<div ></div>").addClass('alert').addClass('alert-'+type).addClass('alert-dismissible').addClass('fade').addClass('show');
		divElement.css({ // 消息框的定位样式
			"position": "fixed",
			"top": "100px",
			"z-index": "999",
		});
		divElement.html(icon + msg); // 设置消息框的内容
		// 消息框添加可以关闭按钮
		var closeBtn = $('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
		$(divElement).append(closeBtn);
		$(divHeader).append(divElement);
		// 消息框放入到页面中
		$(to).append(divHeader);

		// 短暂延时后上浮消失
		setTimeout(function() {$('.alert').alert('close')}, 1300);
	}
};









