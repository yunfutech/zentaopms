$(function()
{
    $('#projectTableList').on('sort.sortable', function(e, data)
    {
        var list = '';
        for(i = 0; i < data.list.length; i++) list += $(data.list[i].item).attr('data-id') + ',';
        $.post(createLink('project', 'updateOrder'), {'projects' : list, 'orderBy' : orderBy});
    });
});

function byProduct(productID, projectID, status)
{
    var projectType = $("input[name='projectType']").val()
    location.href = createLink('project', 'all', "status=" + status + "&project=" + projectID + "&orderBy=" + orderBy + '&productID=' + productID + '&projectType=' + projectType);
}

function goProjectType(value, status, projectID) {
    var productID = 0
    location.href = createLink('project', 'all', "status=" + status + "&project=" + 0 + "&orderBy=order_desc&productID=" + productID + '&projectType=' + value);
}