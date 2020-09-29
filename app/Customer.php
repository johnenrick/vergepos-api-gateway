<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends GenericModel
{
    protected $validationRules = [
        'name' => 'required|max:30|min:3',
        'address' => 'max:100',
    ];
}
