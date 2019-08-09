<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends GenericModel
{
  public $validationRuleNotRequired = ['company_id'];
  public function systemGenerateValue($entry){
    $entry['company_id'] = config('payload.company_id');
    return $entry;
  }
}
