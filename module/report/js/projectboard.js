function changeParams (obj) {
    var begin   = $('#conditions').find('#begin').val();
    var end     = $('#conditions').find('#end').val();
    var projectType = $('#conditions').find('#projectType').val();
    var status = $('#conditions').find('#status').val();
    if(begin.indexOf('-') != -1)
    {
        var beginarray = begin.split("-");
        var begin = '';
        for(i = 0; i < beginarray.length; i++) begin = begin + beginarray[i];
    }
    if(end.indexOf('-') != -1)
    {
        var endarray = end.split("-");
        var end = '';
        for(i = 0 ; i < endarray.length ; i++) end = end + endarray[i];
    }
    var link = createLink('report', 'projectboard', 'begin=' + begin + '&end=' + end + '&projectType=' + projectType + '&status=' + status);
    location.href = link;
}

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
