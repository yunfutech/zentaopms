function changeMonth(value) {
    date = new Date(Date.parse(value))
    month = date.getMonth()
    year = date.getFullYear()
    if (month === 0) {
        year = year - 1
        month = 12
    }
    if (month.toString().length == 1) {
        month = '0' + month
    }
    lastMonth = year + '-' + month

    var oldTargets   = $('#oldTargets').val();
    oldTargets = JSON.parse(oldTargets)
    if (oldTargets.hasOwnProperty(lastMonth)) {
        $('#lastTarget').val(oldTargets[lastMonth].performance)
        $('#lastTarget').attr('readonly', true);
    } else {
        $('#lastTarget').removeAttr('readonly')
        $('#lastTarget').val(0)
    }
}
