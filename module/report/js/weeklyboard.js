function changeParams () {
    var week = $('#conditions').find('#week').val();
    if (parseInt(week) !== 0) {
        week = parseInt(week) + 1
        var link = createLink('report', 'weeklyboard', 'week=' + week);
    } else {
        var link = createLink('report', 'weeklyboard');
    }
    location.href = link;
}