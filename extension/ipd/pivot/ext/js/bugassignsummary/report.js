function changeParams(obj)
{
    var dept  = $('#conditions').find('#dept').val();
    var begin = $('#conditions').find('#begin').val();
    var end   = $('#conditions').find('#end').val();
    if(begin.indexOf('-') != -1)
    {
        var beginarray = begin.split("-");
        var begin = '';
        for(i=0 ; i < beginarray.length ; i++)
        {
            begin = begin + beginarray[i];
        }
    }
    if(end.indexOf('-') != -1)
    {
        var endarray = end.split("-");
        var end = '';
        for(i=0 ; i < endarray.length ; i++)
        {
            end = end + endarray[i];
        }
    }

    var params = window.btoa('dept=' + dept + '&begin=' + begin + '&end=' + end);
    var link = createLink('pivot', 'preview', 'dimension=' + dimension + '&group=' + groupID + '&module=pivot&method=bugassignsummary&params=' + params);
    location.href = link;
}