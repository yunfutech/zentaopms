function changeParams () {
    var week = $('#week').val()
    var product = $('#product').val()
    var user = $('#user').val()
    var link = createLink('report', 'weeklyboard', 'week=' + week + '&product=' + product + '&user=' + user);
    location.href = link;
}