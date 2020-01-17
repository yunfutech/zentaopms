<?php
if($this->loadModel('user')->isLogon() or ($this->app->company->guest and $this->app->user->account == 'guest'))
{
    if($module == 'report' and $method == 'annualdata') return true;
    if(!isset($this->app->user->rights['rights']['report']['annualdata'])) $this->app->user->rights['rights']['report']['annualdata'] = true;
}
