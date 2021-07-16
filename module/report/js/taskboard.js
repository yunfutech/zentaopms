function changeParams (obj) {
    var date = $('#conditions').find('#date').val();
    var dept = $('#conditions').find('#dept').val();
    var product = $('#conditions').find('#product').val();
    var director = $('#conditions').find('#director').val();
    if (date.indexOf('-') != -1) {
        var beginarray = date.split("-");
        var date = '';
        for (i = 0; i < beginarray.length; i++) {
            date = date + beginarray[i];
        }
    }
    var link = createLink('report', 'taskboard', 'date=' + date + '&dept=' + dept) + '&director=' + director + '&product=' + product;
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
        forceParse: true,
        showMeridian: 1,
        minView: 2,
        format: 'yyyy-mm-dd'
    };
    $('input#date').datetimepicker(options);
});
