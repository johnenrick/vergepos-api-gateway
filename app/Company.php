<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends GenericModel
{
  protected $validationRule = [
    'name' => 'required|email|unique:companies,name,except,id|min:4',
    'parent_company_id' => 'exists:company,id'
  ];
  protected $validationRuleNotRequired = ['parent_company_id'];
  public function company_detail(){
    return $this->hasOne('App\CompanyDetail');
  }
  public function stores(){
    return $this->hasMany('App\Store');
  }
}
