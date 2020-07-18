<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends GenericModel
{
  protected $validationRules = [
    'name' => 'required|min:4|unique:companies,name,except,id',
    'code' => 'min:2|unique:companies,name,except,id|max:20',
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
