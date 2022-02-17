var qTipX = 10; //显示信息的框离鼠标的X方向距离
var qTipY = 10; //显示信息的框离鼠标的Y方向距离

var qTipTag = "data-info";   //鼠标移入的DOM 的class名字
var url=baseUrl + 'api/items.php'    // 请求地址
var img='<img src="'+baseUrl +'/public/img/Top/ajax-loader.gif" />'    //请求之前的loading 图片

tooltip = {
  name : "qTip",
  offsetX : qTipX,
  offsetY : qTipY,
  tip : null,
}
tooltip.init = function () {
	
	var tipNameSpaceURI = "http://www.w3.org/1999/xhtml";
	if(!tipContainerID){ var tipContainerID = "qTip";}
	var tipContainer = document.getElementById(tipContainerID);
	if(!tipContainer) {
	  tipContainer = document.createElementNS ? document.createElementNS(tipNameSpaceURI, "div") : document.createElement("div");
		tipContainer.setAttribute("id", tipContainerID);
	  document.getElementsByTagName("body").item(0).appendChild(tipContainer);
	}

	if (!document.getElementById) return;
	this.tip = document.getElementById (this.name);
	this.tip.style.display="none";
	this.tip.style.position="absolute";
	var a, sTitle;
	var anchors = document.getElementsByClassName(qTipTag);

	var $this=this
	var flag=false

	$('.'+qTipTag).each(function(){
		$(this).on('mousemove',function(e){
			 e.stopPropagation()
			if (tooltip.tip){
				tooltip.move (e)
			}
		})
		$(this).on('mouseover',function(e){
			flag=true
			
			
			var _this=$(this)
			sTitle = $(this).attr("data-info");
			if($(this).attr("data-info2")){
				tooltip.show($(this).attr("data-info2"))
			}else{
				if(sTitle){
					tooltip.post(url,{item_no:sTitle},function(res){
					if(flag){
//						console.log(res)
						if(res){
							var html=res     
						_this.attr('data-info2',html)
						tooltip.show(html)
						}else{
							tooltip.show('')
						}
						
					}		
				})
				}
				
			}
		})
		$(this).on('mouseout',function(){
			flag=false
			tooltip.hide()
		})
		
	})
}
tooltip.move = function (evt) {
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
	tooltip.tip.style.left = (x + tooltip.offsetX) + "px";
	tooltip.tip.style.top = (y + tooltip.offsetY) + "px";
}
tooltip.show = function (text) {
	if (!tooltip.tip) return;
	tooltip.tip.innerHTML = text;
	tooltip.tip.style.display = "block";
	
}
tooltip.hide = function () {
	if (!tooltip.tip) return;
	tooltip.tip.innerHTML = "";
	tooltip.tip.style.display = "none";   
}
tooltip.post=function(url,data,fn){
		tooltip.show(img)
		
		$.post(url,data,function(res){
//			console.log(res)
		 if(typeof(fn) == 'function'){
					fn(res)
				}
		},'html')
}





window.onload = function () {
	tooltip.init();
}