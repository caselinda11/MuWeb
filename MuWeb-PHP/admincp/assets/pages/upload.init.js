
/*
Template Name: Zoter - Bootstrap 4 Admin Dashboard
 Author: Mannatthemes
 Website: www.mannatthemes.com
 File: Upload init js
 */


$(document).ready(function(){
    // Basic
    $('.dropify').dropify({
        messages: {
            default: '将文件拖放到此处或单击',
            replace: '拖放文件或单击以替换',
            remove:  '移除文件',
            error:   '抱歉，文件太大'
        },
        error: {
            fileSize: "文件(最大:{{ value }})太大。",
            minWidth: "图片宽度太小。(最小 {{ value }}}px)",
            maxWidth: "图片宽度太大。(最大 {{ value }}}px) ",
            minHeight: "图片高度太小。(最小 {{ value }}}px) ",
            maxHeight: "图片高度太大。(最大 {{ value }}px) ",
            imageFormat: "图片格式不允许 (仅 {{ value }})。",
            fileExtension: "不允许使用该文件 (仅 {{ value }})。"
        }
    });

    // Used events
    var drEvent = $('#input-file-events').dropify();

    drEvent.on('dropify.beforeClear', function(event, element){
        return confirm("你真的要删除吗 \"" + element.file.name + "\" ?");
    });

    drEvent.on('dropify.afterClear', function(event, element){
        alert('档案已删除');
    });

    drEvent.on('dropify.errors', function(event, element){
        console.log('有错误！');
    });

    var drDestroy = $('#input-file-to-destroy').dropify();
    drDestroy = drDestroy.data('dropify');
    $('#toggleDropify').on('click', function(e){
        e.preventDefault();
        if (drDestroy.isDropified()) {
            drDestroy.destroy();
        } else {
            drDestroy.init();
        }
    })
});