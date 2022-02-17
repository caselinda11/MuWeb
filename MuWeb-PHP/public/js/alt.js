
	altTooltip = {
		tip : null,
	};
	altTooltip.init = function () {
		if(!tipContainerID){ var tipContainerID = "qTip";}
		var tipContainer = document.getElementById(tipContainerID);
		if(!tipContainer) {
			tipContainer = document.createElementNS ? document.createElementNS("http://www.w3.org/1999/xhtml", "div") : $.create("div");
			tipContainer.setAttribute("id", tipContainerID);
			$("body").append(tipContainer);
		}

		this.tip = $("#qTip");
		this.tip.css('display','none');
		this.tip.css('position','absolute');
		this.tip.css('z-index','9999');

		var sTitle;
		var flag=false;
		$('.data-info').each(function(){//鼠标移入的DOM 的class名字
			$(this).on('mousemove',function(evt){
				evt.stopPropagation();
				if (altTooltip.tip){
					var x=0, y=0;
					if (document.all) {//IE
						x = (document.documentElement && document.documentElement.scrollLeft) ? document.documentElement.scrollLeft : document.body.scrollLeft;
						y = (document.documentElement && document.documentElement.scrollTop) ? document.documentElement.scrollTop : document.body.scrollTop;
						x += window.event.clientX;
						y += window.event.clientY;
					} else {//Good Browsers
						x = evt.pageX;
						y = evt.pageY;
					}
					altTooltip.tip.css('left', (x + 10) + "px");//显示信息的框离鼠标的X方向距离
					altTooltip.tip.css('top',  (y + 10) + "px");//显示信息的框离鼠标的Y方向距离
				}
			});
			$(this).on('mouseover',function(e){
				flag=true;
				var _this=$(this);
				sTitle = $(this).attr("data-info");
				if($(this).attr("data-info2")){
					if (!altTooltip.tip) return;
					altTooltip.tip.html($(this).attr("data-info2"));
					altTooltip.tip.css('display','block');
				}else{
					if(sTitle){
						$.ajax({
							url:baseUrl + 'api/items.php',
							beforeSend:function(){
								if (!altTooltip.tip) return;
								altTooltip.tip.html('<img src="'+baseUrl+'/public/img/Top/profile/ajax-loader.gif" style="z-index: 9999" alt="loading"/>');
								altTooltip.tip.css('display','block');
								},
							type:"POST",
							data:{item:sTitle},
							dataType:"html",
							success:function (res) {
								
								console.log(res);
								
								if(flag && res){
									_this.attr('data-info2',res);
									if (!altTooltip.tip) return;
									altTooltip.tip.html(res);
									altTooltip.tip.css('display','block');
								}
							}
						});
					}

				}
			});
			$(this).on('mouseout',function(){
				flag=false;
				if (!altTooltip.tip) return;
				altTooltip.tip.html("");
				altTooltip.tip.css('display','none');
			})
		})
	};

	$(function() {
		altTooltip.init();
	});

