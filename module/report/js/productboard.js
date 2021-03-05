$('#selectLines input:checkbox').change(function()
{
    var selectLines = '';
    $('#selectLines input:checkbox').each(function(i)
    {
        if($(this).prop('checked')) selectLines += $(this).val() + ',';
    })
    selectLines = selectLines.substring(0, selectLines.length - 1);
    var status = '';
    $('#status input:radio').each(function(i)
    {
        if($(this).prop('checked')) status = $(this).val();
    })
    location.href = createLink('report', 'productboard', 'selectLines=' + selectLines + '&status=' + status);
})

$('#status input:radio').change(function()
{
    var selectLines = '';
    $('#selectLines input:checkbox').each(function(i)
    {
        if($(this).prop('checked')) selectLines += $(this).val() + ',';
    })
    selectLines = selectLines.substring(0, selectLines.length - 1);
    var status = '';
    $('#status input:radio').each(function(i)
    {
        if($(this).prop('checked')) {
            status = $(this).val();
        }
    })
    location.href = createLink('report', 'productboard', 'selectLines=' + selectLines + '&status=' + status);
})
