<?php
$properties = $this->loadModel('api')->getLicenses();
$html       = '';
if(isset($properties['expireDate']) and $properties['expireDate'] != 'All Life')
{
    $expireDays    = helper::diffDate($properties['expireDate'], date('Y-m-d'));
    $expireWarning = $lang->user->expireWarning;
    if(strpos($this->config->version, 'biz') !== false) $expireWarning = $lang->user->expireBizWaring;
    if(strpos($this->config->version, 'max') !== false) $expireWarning = $lang->user->expireMaxWaring;
    if($expireDays <= 30 and $expireDays > 0) $html = sprintf($expireWarning, $expireDays);
    if($expireDays == 0) $html = $lang->user->expiryReminderToday;
}
?>
<script>
$(function()
{
    $('#poweredby').append(<?php echo json_encode($html);?>);
})
</script>
