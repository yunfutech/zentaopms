function changeParams (obj) {
    var orderBy   = $('#orderBy').val();
    var recTotal   = $('#recTotal').val();
    var recPerPage   = $('#recPerPage').val();
    var pageID   = $('#pageID').val();

    var productID   = $('#conditions').find('#productID').val();
    var line     = $('#conditions').find('#line').val();
    var begin = $('#conditions').find('#begin').val();
    if(begin.indexOf('-') != -1)
    {
        var beginarray = begin.split("-");
        var begin = '';
        for(i = 0; i < beginarray.length; i++) begin = begin + beginarray[i];
    }
    var isContract = $('#conditions').find('#isContract').val();
    var completed = $('#conditions').find('#completed').val();
    var link = createLink('report', 'milestoneBoard', 'orderBy=' + orderBy + '&recTotal=' + recTotal + '&recPerPage=' + recPerPage + '&pageID=' + pageID + '&productID=' + productID + '&line=' + line + '&begin=' + begin + '&isContract=' + isContract + '&completed=' + completed);
    location.href = link;
}
