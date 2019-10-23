function changeParams (obj) {
    var date = $('#conditions').find('#date').val();
    if (date.indexOf('-') != -1) {
        var beginarray = date.split("-");
        var date = '';
        for (i = 0; i < beginarray.length; i++) {
            date = date + beginarray[i];
        }
    }
    var link = createLink('report', 'projectboard', 'date=' + date);
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
    $('input#date').datetimepicker(options);
});
