function changeWeek (week) {
    var product = $('#product').val()
    if (week !== 0) {
        var link = createLink('report', 'weeklyboard', 'type=thisweek&week=' + week + '&product=' + product);
    } else {
        var link = createLink('report', 'weeklyboard');
    }
    location.href = link;
}

function changeProduct (product) {
    var week = $('#week').val()
    if (product !== 0) {
        var link = createLink('report', 'weeklyboard', 'type=thisweek&week=' + week + '&product=' + product);
    } else {
        var link = createLink('report', 'weeklyboard');
    }
    location.href = link;
}