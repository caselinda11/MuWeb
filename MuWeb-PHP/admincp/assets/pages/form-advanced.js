!function(a) {
    var b = function() {};
    b.prototype.init = function() {
        a(".title_color").asColorPicker();
        a(".type_color").asColorPicker();
    },
        a.AdvancedForm = new b,
        a.AdvancedForm.Constructor = b
}(window.jQuery),
    function(a) {
        a.AdvancedForm.init()
    }(window.jQuery);
