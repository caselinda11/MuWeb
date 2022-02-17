!function(a) {
    var b = function() {
        this.$body = a("body"),
            this.$wrapper = a("#wrapper"),
            this.$btnFullScreen = a("#btn-fullscreen"),
            this.$leftMenuButton = a(".button-menu-mobile"),
            this.$menuItem = a(".has_sub > a")
    };
    b.prototype.initNicescroll = function() {
        a(".niceScrollleft").niceScroll({
            height: "auto",
            position: "right",
            scrollspeed: 40,
            cursorcolor: "#ddd",
            cursorwidth: "8px",
        })
    }
        ,
        b.prototype.initLeftMenuCollapse = function() {
            var c = this;
            this.$leftMenuButton.on("click", function(d) {
                d.preventDefault();
                c.$body.toggleClass("fixed-left-void");
                c.$wrapper.toggleClass("enlarged")
            })
        }
        ,
        b.prototype.initComponents = function() {
            a('[data-toggle="tooltip"]').tooltip();
            a('[data-toggle="popover"]').popover()
        }
        ,
        b.prototype.initFullScreen = function() {
            var c = this;
            c.$btnFullScreen.on("click", function(d) {
                d.preventDefault();
                if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement) {
                    if (document.documentElement.requestFullscreen) {
                        document.documentElement.requestFullscreen()
                    } else {
                        if (document.documentElement.mozRequestFullScreen) {
                            document.documentElement.mozRequestFullScreen()
                        } else {
                            if (document.documentElement.webkitRequestFullscreen) {
                                document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT)
                            }
                        }
                    }
                } else {
                    if (document.cancelFullScreen) {
                        document.cancelFullScreen()
                    } else {
                        if (document.mozCancelFullScreen) {
                            document.mozCancelFullScreen()
                        } else {
                            if (document.webkitCancelFullScreen) {
                                document.webkitCancelFullScreen()
                            }
                        }
                    }
                }
            })
        }
        ,
        b.prototype.initMenu = function() {
            var c = this;
            c.$menuItem.on("click", function() {
                var f = a(this).parent();
                var g = f.find("> ul");
                if (!c.$body.hasClass("sidebar-collapsed")) {
                    if (g.is(":visible")) {
                        g.slideUp(300, function() {
                            f.removeClass("nav-active");
                            a(".body-content").css({
                                height: ""
                            });
                            d()
                        })
                    } else {
                        e();
                        f.addClass("nav-active");
                        g.slideDown(300, function() {
                            d()
                        })
                    }
                }
                return false
            });
            function e() {
                a(".has_sub").each(function() {
                    var f = a(this);
                    if (f.hasClass("nav-active")) {
                        f.find("> ul").slideUp(300, function() {
                            f.removeClass("nav-active")
                        })
                    }
                })
            }
            function d() {
                var f = a(document).height();
                if (f > a(".body-content").height()) {
                    a(".body-content").height(f)
                }
            }
        }
        ,
        b.prototype.activateMenuItem = function() {
            a("#sidebar-menu a").each(function() {
                if (this.href == window.location.href) {
                    a(this).addClass("active");
                    a(this).parent().addClass("active");
                    a(this).parent().parent().prev().addClass("active");
                    a(this).parent().parent().parent().addClass("active");
                    a(this).parent().parent().prev().click()
                }
            })
        }
        ,
        b.prototype.Preloader = function() {
            a(window).load(function() {
                a("#status").fadeOut();
                a("#preloader").delay(350).fadeOut("slow");
                a("body").delay(350).css({
                    overflow: "visible"
                })
            })
        }
        ,
        b.prototype.init = function() {
            this.initNicescroll();
            this.initLeftMenuCollapse();
            this.initComponents();
            this.initFullScreen();
            this.initMenu();
            this.activateMenuItem();
            this.Preloader()
        }
        ,
        a.MainApp = new b,
        a.MainApp.Constructor = b
}(window.jQuery),
    function(a) {
        a.MainApp.init()
    }(window.jQuery);

