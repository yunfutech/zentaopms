/**
 * Add item to create view of programplan.
 *
 * @param  obj    obj
 * @access public
 * @return void
 */
function addItem(obj)
{
    var item = $('#addItem').html().replace(/%i%/g, itemIndex);
    $(obj).closest('tr').after('<tr class="addedItem">' + item  + '</tr>');
    var newItem = $('#names' + itemIndex).closest('tr');
    newItem.find('.form-date').datepicker();
    $("#output" + itemIndex).chosen();
    $("#PM_i__chosen").remove();
    $("#PM" + itemIndex).chosen();
    $("#output_i__chosen").remove();
    itemIndex ++;
}

/**
 * Delete item.
 *
 * @param  obj    obj
 * @access public
 * @return void
 */
function deleteItem(obj)
{
    if($('#planForm .table tbody').children().length < 2) return false;
    $(obj).closest('tr').remove();
}
