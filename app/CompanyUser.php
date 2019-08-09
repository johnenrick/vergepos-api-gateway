<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends GenericModel
{
  public function systemGenerateValue($entry){
    if(!isset($entry['id']) || $entry['id'] == null){
      $entry['status'] = 0;
    }
    return $entry;
  }
  public function company(){
    return $this->belongsTo('App\Company', 'company_id', 'id');
  }
}
