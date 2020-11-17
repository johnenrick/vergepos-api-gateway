<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkShift extends GenericModel
{
    public $validationRuleNotRequired = ['end_datetime', 'close_overidden'];
    public function work_shift_cash_readings(){
        return $this->hasMany('App\WorkShiftCashReading');
    }
}
