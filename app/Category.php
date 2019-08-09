<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends GenericModel
{
    public $validationRuleNotRequired = ['category_id'];
    public function systemGenerateValue($entry){
      if(!isset($entry['id']) && !isset($entry['category_id'])){
        $entry['category_id'] = 0;
      }
      $entry['company_id'] = config('payload.company_id');
      return $entry;
    }
    public function category(){
      return $this->belongsTo('App\Category');
    }
    public function categories(){
      return $this->hasMany('App\Category');
    }
    public function products(){
      return $this->hasMany('App\Product');
    }
}
