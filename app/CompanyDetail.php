<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends GenericModel
{
    protected $validationRuleNotRequired = ['contact_number'];
}
