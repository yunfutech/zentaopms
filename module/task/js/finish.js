$(function()
{
    setTotalConsumed();

    $('#currentConsumed').keyup(setTotalConsumed);

    $('#submit').click(function()
    {
        if(task.consumed != 0 && $('#currentConsumed').val() == 0 && $('#currentConsumed').val() != '') return confirm(consumedEmpty);
    })
})

function setTotalConsumed()
{
    var currentConsumed = $('#currentConsumed').val();
    if(!parseFloat(currentConsumed)) currentConsumed = 0;
    var totalConsumed = parseFloat(currentConsumed) + parseFloat(consumed);
    totalConsumed = Math.round(totalConsumed * 1000) / 1000;
    $('#totalConsumed').html(totalConsumed);
    $('#consumed').val(totalConsumed);
}
