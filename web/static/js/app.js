/**
 * 菜单折叠
 */
$('#sidebar-menu').metisMenu();
/**
 * 吐司提示
 */
$('[data-toggle="tooltip"]').tooltip();

function alert(msg, type, callbak) {

    var shift;
    var time;
    if(!msg){
        return;
    }

    if(type == 'success'){
        shift = 5;
        time  = 1;
    }else if(type == 'error'){
        shift = 6;
        time  = 2;
    }else{
        shift = 6;
        time  = 2;
    }

    if(callbak == ''){
        callbak = function () {

        };
    }

    time = time * 1000;
    layer.msg(msg, {time:time, shift: shift}, callbak);
}

/**
 * 优化的确认框
 * @param msg 确认框消息
 * @param ok 确认回调函数
 */
function confirm(msg, callback) {
    var d = dialog({
        fixed: true,
        width: '280',
        title: '温馨提示',
        content: msg,
        lock: true,
        opacity: .1,
        okValue: '确定',
        ok: function () {
            if(typeof callback === "function") {
                callback();
                return true;
            }
            return false;
        },
        cancelValue: '取消',
        cancel: function () {
            d.close().remove();
            return false;
        }
    });

    d.showModal();
    return false;

}

/**
 * 重置表单
 */
function resetForm() {
    $(':input','form')
        .not(':button, :submit, :reset, :hidden')
        .val('')
        .removeAttr('checked')
        .removeAttr('selected');
}

$("[type='reset']").click(function () {
    resetForm();
});

/**
 * iframe模态框
 */
(function($){

    $("[data-modal]").on('click',function(event){
        event.stopPropagation(); // 阻止冒泡事件
        event.preventDefault(); // 兼容标准浏览器
        window.event.returnValue = false; // 兼容IE6~8

        var thisObj = $(this);

        var scroll  = thisObj.data('scroll'); // 是否滚动
        var center  = thisObj.data('center'); // 是否居中
        var title   = thisObj.data('title');
        var src     = thisObj.data('src');
        var height  = thisObj.data('height');

        var modal   = thisObj.data('modal');
        var iframe  = $(modal).find('iframe');

        if(!modal || !iframe || !src){
            return false;
        }

        if(scroll){
            $(iframe).attr('scroll', scroll);
        }else{
            $(iframe).attr('scroll', 'auto');
        }

        if(title){

            $(modal).find('.modal-title').text(title);

        }

        if(height){
            $(iframe).css("height", height);
        }else{
            $(iframe).css("height", 300);
        }

        if(!center){
            center = true;
        }

        if(center){
            $(modal).on('show.bs.modal', function () {
                $(this).css('display', 'block');
                var modalHeight = $(window).height() / 2 - $(this).find('.modal-dialog').height() / 2;
                $(this).find('.modal-dialog').css({'margin-top': modalHeight});
            });
        }

        $(iframe).attr('src', src);

        setTimeout(function () {
            $(modal).modal('show');
        }, 500);

        $(document).delegate("button:submit", 'click',function(event){

            event.stopPropagation(); // 阻止冒泡事件
            event.preventDefault(); // 兼容标准浏览器
            window.event.returnValue = false; // 兼容IE6~8

            $(iframe).contents().find("form").find("input:hidden").trigger('click');

        });

    });

})(jQuery);
