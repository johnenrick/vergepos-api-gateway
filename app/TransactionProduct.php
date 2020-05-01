<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends GenericModel
{
    //
    public function product(){
        return $this->belongsTo('App\Product');
      }
}
