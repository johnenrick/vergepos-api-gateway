<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends GenericModel
{
  public $validationRules = [
    'description' => 'required|max:50',
    // 'barcode' => 'unique:products,barcode,except,id',
    'cost' => 'required|numeric',
    'price' => 'numeric',
  ];
  public $validationRuleNotRequired = ['is_available', 'short_description', 'barcode'];
  public function systemGenerateValue($entry){
    if(!isset($entry['id'])){
      !isset($entry['short_description']) ? $entry['short_description'] = substr($entry['description'], 0, 20) : null;
      !isset($entry['is_available']) ? $entry['is_available'] = 1 : null;
    }
    return $entry;
  }
  public function category(){
    return $this->belongsTo('App\Category');
  }
}
