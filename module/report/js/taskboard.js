function changeParams (obj) {
    var date = $('#conditions').find('#date').val();
    var dept = $('#conditions').find('#dept').val();
    console.log(date)
    if (date.indexOf('-') != -1) {
        var beginarray = date.split("-");
        var date = '';
        for (i = 0; i < beginarray.length; i++) date = date + beginarray[i];
    }


    var link = createLink('report', 'taskboard', 'date=' + date + '&dept=' + dept);
    location.href = link;
}
/**
 * Convert a date string to date object in js.
 *
 * @param  string $date
 * @access public
 * @return date
 */

$(function () {
    var options =
    {
        language: config.clientLang,
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,
        minView: 2,
        format: 'yyyy-mm-dd',
        startDate: new Date()
    };
    $('input#date').fixedDate().datetimepicker(options);
});
