// 选择类型
function selectType(object) {
    var thisObj = $(object);
    var type = ["array", "object"];
    var btn  = thisObj.closest('tr').find('.addFieldBtn');
    if($.inArray(thisObj.val(), type) < 0){
        btn.attr("disabled", true);
        // alert('隐藏增加按钮');
    }else{
        btn.attr("disabled", false);
        // alert('显示增加按钮');
    }
}

// 新增字段
function addField(object, type) {

    var thisObj = $(object);
    var tableObj = thisObj.closest('.row').find('.table');
    var TrObj = thisObj.closest('tr');
    var level = parseInt(TrObj.find('input.js_level').val());

    if(type == 'header'){
        var cloneObj = $('.clone-table .js_headerClone').clone(true);
    }else if(type == 'request'){
        var cloneObj = $('.clone-table .js_requestClone').clone(true);
    }else if(type == 'response'){
        var cloneObj = $('.clone-table .js_responseClone').clone(true);

    }

    if(level >= 0){
        var pl = (level+1) * 10 + 12;
    }else{
        var pl = 12;
    }

    if(TrObj.length > 0){
        cloneObj.find("input.js_level").val(level + 1).data('level', level + 1);
        TrObj.after(cloneObj).next('tr').find('input.js_name').css('padding-left', pl + 'px').focus();
    }else{

        cloneObj.find("input.js_level").val(0);
        cloneObj.appendTo(tableObj).find('input:eq(0)').focus();
    }

}

// 删除字段
function deleteField(btn) {
    $(btn).closest('tr').remove();
}

function replaceAll(originalStr,oldStr,newStr){
    var regExp = new RegExp(oldStr,"gm");
    return originalStr.replace(regExp,newStr)
}

// 根据表格获取json字符串
function getTableJson(tableId) {
    var json = "[";
    var i = 0;
    var j = 0;
    $('#' + tableId).find('tbody').find('tr').each(function() {
        i = i + 1;
        j = 0;
        if (i != 1)
            json += ","
        json += "{";
        $(this).find('td').find('input').each(function(i, val) {
            j = j + 1;
            if (j != 1)
                json += ",";
            json += "\"" + val.name + "\":\"" + replaceAll(val.value,'"','\\"') + "\""
        });
        $(this).find('td').find('select').each(function(i, val) {
            j = j + 1;
            if (j != 1)
                json += ",";
            json += "\"" + val.name + "\":\"" + replaceAll(val.value,'"','\\"') + "\""
        });
        json += "}"
    });
    json += "]";
    return json;
}