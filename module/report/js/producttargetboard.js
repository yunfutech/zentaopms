function changeParams (obj) {
    var productID   = $('#conditions').find('#productID').val();
    var line     = $('#conditions').find('#line').val();
    var month = $('#conditions').find('#month').val();
    var director = $('#conditions').find('#director').val();
    if(month.indexOf('-') != -1)
    {
        var montharray = month.split("-");
        var month = '';
        for(i = 0; i < montharray.length; i++) month = month + montharray[i];
    }
    var link = createLink('report', 'producttargetboard', 'productID=' + productID + '&line=' + line+ '&month=' + month + '&director=' + director);
    location.href = link;
}


$(".form-date").datetimepicker(
{
    weekStart: 1,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 3,
    minView: 3,
    forceParse: 0,
    format: "yyyymm"
});