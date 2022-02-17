!function(a) {
    var b = function() {};
    b.prototype.createLineChart = function(d, c, g, h, e, f) {
        Morris.Line({
            element: d,
            data: c,
            xkey: g,
            ykeys: h,
            labels: e,
            hideHover: "auto",
            gridLineColor: "#eef0f2",
            resize: true,
            lineColors: f
        })
    }
        ,
        b.prototype.createAreaChart = function(d, h, g, c, i, j, e, f) {
            Morris.Area({
                element: d,
                pointSize: 3,
                lineWidth: 2,
                data: c,
                xkey: i,
                ykeys: j,
                labels: e,
                resize: true,
                hideHover: "auto",
                gridLineColor: "#eef0f2",
                lineColors: f
            })
        }
        ,
        b.prototype.createBarChart = function(d, c, g, h, e, f) {
            Morris.Bar({
                element: d,
                data: c,
                xkey: g,
                ykeys: h,
                labels: e,
                gridLineColor: "#eef0f2",
                barSizeRatio: 0.4,
                resize: true,
                hideHover: "auto",
                barColors: f
            })
        }
        ,
        b.prototype.createDonutChart = function(e, d, c) {
            Morris.Donut({
                element: e,
                data: d,
                resize: true,
                colors: c
            })
        }
        ,
        b.prototype.init = function() {
            var e = [{
                y: "2009",
                a: 100,
                b: 90
            }, {
                y: "2010",
                a: 75,
                b: 65
            }, {
                y: "2011",
                a: 50,
                b: 40
            }, {
                y: "2012",
                a: 75,
                b: 65
            }, {
                y: "2013",
                a: 50,
                b: 40
            }, {
                y: "2014",
                a: 75,
                b: 65
            }, {
                y: "2015",
                a: 100,
                b: 90
            }];
            this.createLineChart("morris-line-example", e, "y", ["a", "b"], ["Series A", "Series B"], ["#44a2d2", "#cd85fe"]);
            var c = [{
                y: "2009",
                a: 10,
                b: 20
            }, {
                y: "2010",
                a: 75,
                b: 65
            }, {
                y: "2011",
                a: 50,
                b: 40
            }, {
                y: "2012",
                a: 75,
                b: 65
            }, {
                y: "2013",
                a: 50,
                b: 40
            }, {
                y: "2014",
                a: 75,
                b: 65
            }, {
                y: "2015",
                a: 90,
                b: 60
            }, {
                y: "2016",
                a: 90,
                b: 75
            }];
            this.createAreaChart("morris-area-example", 0, 0, c, "y", ["a", "b"], ["Series A", "Series B"], ["#44a2d2", "#cd85fe"]);
            var d = [{
                y: "2009",
                a: 100,
                b: 90
            }, {
                y: "2010",
                a: 75,
                b: 65
            }, {
                y: "2011",
                a: 50,
                b: 40
            }, {
                y: "2012",
                a: 75,
                b: 65
            }, {
                y: "2013",
                a: 50,
                b: 40
            }, {
                y: "2014",
                a: 75,
                b: 65
            }, {
                y: "2015",
                a: 100,
                b: 90
            }, {
                y: "2016",
                a: 90,
                b: 75
            }];
            this.createBarChart("morris-bar-example", d, "y", ["a", "b"], ["Series A", "Series B"], ["#44a2d2", "#96a3f6"]);
            var f = [{
                label: "Download Sales",
                value: 12
            }, {
                label: "In-Store Sales",
                value: 30
            }, {
                label: "Mail-Order Sales",
                value: 20
            }];
            this.createDonutChart("morris-donut-example", f, ["#cd85fe", "#44a2d2", "#96a3f6"])
        }
        ,
        a.MorrisCharts = new b,
        a.MorrisCharts.Constructor = b
}(window.jQuery),
    function(a) {
        a.MorrisCharts.init()
    }(window.jQuery);
