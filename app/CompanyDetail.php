<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends GenericModel
{
    protected $validationRules = [
        'nature' => 'regex:/^[a-zA-Z0-9\s]+$/',
        'address' => 'regex:/^[a-zA-Z0-9,.!? ]*$/',
    ];
    protected $validationRuleNotRequired = ['contact_number'];
}
