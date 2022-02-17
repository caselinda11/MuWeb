<?
//展示页 可二次开发  buy/message 传入 ordid code Area

if(!$_GET['ordid'])exit('数据错误');
if($_GET['code']=='1000'){
    alert('donation','充值成功，点击确定返回充值界面。');
}
else exit( '数据错误！');