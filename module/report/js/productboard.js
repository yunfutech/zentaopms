$(function () {
    var options =
    {
        language: config.clientLang,
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: true,
        showMeridian: 1,
        weeks: true,
        minView: 2,
        format: 'yyyy-mm-dd'
    };
    $('input#begin,input#end').datetimepicker(options);
});


function changeParams(obj) {
    var selectLines = getSelectLines()
    var status = getStatus()
    var begin = getBegin()
    var end = getEnd()
    var link = createLink('report', 'productboard', 'begin=' + begin + '&end=' + end + '&selectLines=' + selectLines + '&status=' + status);
    location.href = link;
}

function getSelectLines() {
    var selectLines = '';
    $('#selectLines input:checkbox').each(function(i)
    {
        if($(this).prop('checked')) selectLines += $(this).val() + ',';
    })
    selectLines = selectLines.substring(0, selectLines.length - 1);
    return selectLines;
}

function getStatus() {
    var status = '';
    $('#status input:radio').each(function(i)
    {
        if($(this).prop('checked')) status = $(this).val();
    })
    return status
}

function getBegin() {
    var begin   = $('#conditions').find('#begin').val();
    if(begin.indexOf('-') != -1)
    {
        var beginarray = begin.split("-");
        var begin = '';
        for(i = 0; i < beginarray.length; i++) begin = begin + beginarray[i];
    }
    return begin;
}

function getEnd() {
    var end     = $('#conditions').find('#end').val();
    if(end.indexOf('-') != -1)
    {
        var endarray = end.split("-");
        var end = '';
        for(i = 0 ; i < endarray.length ; i++) end = end + endarray[i];
    }
    return end;
}

$('#status input:radio').change(function()
{
    changeParams()
})

$('#selectLines input:checkbox').change(function()
{
    changeParams()
})