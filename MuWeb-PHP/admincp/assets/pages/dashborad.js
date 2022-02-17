!function(a){
    var b=function(){};
    b.prototype.createStackedChart=function(d,c,g,h,e,f){
        Morris.Bar({
            element:d,data:c,xkey:g,ykeys:h,preUnits:"",barSizeRatio:0.4,stacked:true,labels:e,hideHover:"auto",resize:true,gridLineColor:"#eeeeee",barColors:f}
        )}
        ,b.prototype.createAreaChart=function(d,h,g,c,i,j,e,f){
        Morris.Area({
            element:d,pointSize:3,lineWidth:2,data:c,xkey:i,ykeys:j,labels:e,resize:true,hideHover:"auto",gridLineColor:"#29b348",lineColors:f,fillOpacity:0.1,xLabelMargin:10,yLabelMargin:10,grid:false,axes:false,pointSize:0}
        )}
        ,b.prototype.init=function(){
        a("#world-map-markers").vectorMap({
            map:"world_mill_en",scaleColors:["#3263aa","#3263aa"],normalizeFunction:"polynomial",hoverOpacity:0.7,hoverColor:false,regionStyle:{
                initial:{
                    fill:"#b2c2da"}
            }
            ,markerStyle:{
                initial:{
                    r:9,fill:"#3263aa","fill-opacity":0.9,stroke:"#fff","stroke-width":5,"stroke-opacity":0.4}
                ,hover:{
                    stroke:"#fff","fill-opacity":1,"stroke-width":1.5}
            }
            ,backgroundColor:"transparent",markers:[{
                latLng:[7.11,171.06],name:"马绍尔群岛"}
                ,{
                    latLng:[17.3,-62.73],name:"Saint Kitts and Nevis"}
                ,{
                    latLng:[3.2,73.22],name:"Maldives"}
                ,{
                    latLng:[35.88,14.5],name:"Malta"}
                ,{
                    latLng:[12.05,-61.75],name:"Grenada"}
                ,{
                    latLng:[13.16,-61.23],name:"Saint Vincent and the Grenadines"}
                ,{
                    latLng:[13.16,-59.55],name:"Barbados"}
                ,{
                    latLng:[-4.61,55.45],name:"Seychelles"}
                ,{
                    latLng:[14.01,-60.98],name:"Saint Lucia"}
                ,{
                    latLng:[1.3,103.8],name:"Singapore"}
                ,{
                    latLng:[15.3,-61.38],name:"Dominica"}
                ,{
                    latLng:[26.02,50.55],name:"Bahrain"}
            ]}
        );window.addEventListener("load",function(){
            vanillaCalendar.init({
                disablePastDays:true}
            )}
        );if(typeof Skycons!=="undefined"){
            var f=new Skycons({
                    color:"#f1ac57"}
                ,{
                    resizeClear:true}
            ),g=["clear-day","clear-night","partly-cloudy-day","partly-cloudy-night","cloudy","rain","sleet","snow","wind","fog"],e;for(e=g.length;e--;){
                f.set(g[e],g[e])}
            f.play()}
        Morris.Donut({
            element:"donut-example",data:[{
                label:"Tablets",value:50}
                ,{
                    label:"Iphones",value:114}
                ,{
                    label:"Laptops",value:230}
            ],resize:true,colors:["#e3eaef","#44a2d2","#b2c2da"],labelColor:"#888",backgroundColor:"transparent",fillOpacity:0.1,}
        );
        const d = [
            {y: "1", a: 45, b: 180}
            , {y: "2", a: 75, b: 65}
            , {y: "3", a: 100, b: 90}
            , {y: "4", a: 75, b: 65}
            , {y: "5", a: 100, b: 90}
            , {y: "6", a: 75, b: 65}
            , {y: "7", a: 50, b: 40}
            , {y: "8", a: 75, b: 65}
            , {y: "9", a: 50, b: 40}
            , {y: "10", a: 75, b: 65}
            , {y: "11", a: 100, b: 90}
            , {y: "12", a: 80, b: 65}
            , {y: "13", a: 45, b: 180}
            , {y: "14", a: 75, b: 65}
            , {y: "15", a: 100, b: 90}
            , {y: "16", a: 75, b: 65}
            , {y: "17", a: 100, b: 90}
            , {y: "18", a: 75, b: 65}
            , {y: "19", a: 50, b: 40}
            , {y: "20", a: 75, b: 65}
            , {y: "21", a: 50, b: 40}
            , {y: "22", a: 75, b: 65}
            , {y: "23", a: 100, b: 90}
            , {y: "24", a: 80, b: 65}
            , {y: "25", a: 50, b: 40}
            , {y: "26", a: 75, b: 65}
            , {y: "27", a: 50, b: 40}
            , {y: "28", a: 75, b: 65}
            , {y: "29", a: 100, b: 90}
            , {y: "30", a: 80, b: 65}
        ];this.createStackedChart("morris-bar-stacked",d,"y",["a","b"],["注册总数","访问总数"],["#44a2d2","#e6edf3"]);var c=[{
            y:"2011",a:10,b:15}
            ,{y:"2012",a:30,b:35}
            ,{y:"2013",a:10,b:25}
            ,{y:"2014",a:55,b:45}
            ,{y:"2015",a:30,b:20}
            ,{y:"2016",a:40,b:35}
            ,{y:"2017",a:10,b:25}
            ,{y:"2018",a:25,b:30}
        ];this.createAreaChart("morris-area-chart",0,0,c,"y",["a"],["注册总数"],["#44a2d2 "])}
        ,a.Dashboard=new b,a.Dashboard.Constructor=b}
(window.jQuery),function(a){
    a.Dashboard.init()}
(window.jQuery);