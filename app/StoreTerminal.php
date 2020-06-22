<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreTerminal extends GenericModel
{
    protected $validationRuleNotRequired = ['serial_number', 'accreditation_number', 'machine_identification_number', 'permit_number'];
    public function store(){
        return $this->belongsTo('App\Store');
    }
}
