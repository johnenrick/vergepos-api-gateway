<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends GenericModel
{
  protected $validationRuleNotRequired = ['is_predefined'];
  public function systemGenerateValue($data){
    $data['is_predefined'] = 0;
    return $data;
  }
  public function role_access_lists(){
    return $this->hasMany('App\RoleAccessList')->with(['service_action']);
  }
}
