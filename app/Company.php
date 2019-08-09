<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends GenericModel
{
  protected $validationRule = [
    'name' => 'required|email|unique:users,email,except,id',
    'parent_company_id' => 'exists:company,id'
  ];
  protected $validationRuleNotRequired = ['parent_company_id'];
}
