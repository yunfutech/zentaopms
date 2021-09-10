function changeContract(value) {
    console.log(value)
    if (value === '1') {
        console.log($('#pri').disabled)
        $('#pri').attr('disabled', 'disabled')
        $('#pri').val('0')
    } else {
        $('#pri').removeAttr('disabled')
    }
}