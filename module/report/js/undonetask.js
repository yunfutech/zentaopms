function changeParams (obj) {
    var dept = $('#conditions').find('#dept').val();
    var link = createLink('report', 'undonetask', 'dept=' + dept);
    location.href = link;
}

