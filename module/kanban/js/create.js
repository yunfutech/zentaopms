/**
 * When space type or space value change.
 *
 * @oaram  int    spaceID
 * @param  string type
 * @access public
 * @return void
 */
function changeValue(spaceID, type = '')
{
    if(type == '') type = spaceType;
    location.href = createLink('kanban', 'create', 'spaceID=' + spaceID + '&type=' + type);
}

