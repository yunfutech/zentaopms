/**
 * Ajax get chart data.
 *
 * @access public
 * @return bool
 */
function ajaxGetChart(check = true, chart = DataStorage.chart, echart = window.echart)
{
    var chartParams = JSON.parse(JSON.stringify(chart));
    if(typeof DataStorage != 'undefined') chartParams.fieldSettings = JSON.parse(JSON.stringify(DataStorage.fieldSettings));
    if(typeof DataStorage != 'undefined') chartParams.langs         = JSON.parse(JSON.stringify(DataStorage.langs));

    /* Redraw echart. */
    /* 拿数据并重绘图表。*/
    $.post(createLink('chart', 'ajaxGetChart'), chartParams, function(resp)
    {
        var data = JSON.parse(resp);
        if(echart)
        {
            echart.clear();
            echart.setOption(data, true);
            $('.btn-export').removeClass('hidden');
        }
    });
}

/**
 * Init picker.
 *
 * @access public
 * @return void
 */
function initPicker($row, pickerName = 'picker-select')
{
    $row.find('.' + pickerName).picker({'maxDropHeight': pickerHeight});
    $row.find("." + pickerName).each(function(index)
    {
       if($(this).hasClass('required')) $(this).siblings("div .picker").addClass('required');
    });
}

/**
 * Init datepicker.
 *
 * @param  object   $obj
 * @param  function callback
 * @access public
 * @return void
 */
function initDatepicker($obj, callback)
{
    $obj.find('.form-date').datepicker();
    $obj.find('.form-datetime').datetimepicker();

    if(typeof callback == 'function') callback($obj);
}