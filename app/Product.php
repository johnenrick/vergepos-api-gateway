<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends GenericModel
{
  public $validationRules = [
    'description' => 'required|max:50'
  ];
  public $validationRuleNotRequired = ['is_available', 'short_description'];
  public function systemGenerateValue($entry){
    if(!isset($entry['id'])){

      !isset($entry['short_description']) ? $entry['short_description'] = substr($entry['description'], 20) : null;
      !isset($entry['is_available']) ? $entry['is_available'] = 1 : null;
    }
    return $entry;
  }
  public function category(){
    return $this->belongsTo('App\Category');
  }
}
