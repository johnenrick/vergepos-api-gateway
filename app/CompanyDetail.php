<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends GenericModel
{
    protected $validationRules = [
        'nature' => 'regex:/^[a-zA-Z0-9\s]+$/',
        'address' => 'regex:/^[a-zA-Z0-9,.!? ]*$/',
        'contact_number' => 'max:30'
    ];
    protected $validationRuleNotRequired = ['contact_number'];
}
