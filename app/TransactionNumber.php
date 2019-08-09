<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionNumber extends GenericModel
{
  public function systemGenerateValue($data){
    $data['user_id'] = config('payload.id');
    return $data;
  }
  public function transaction(){
    return $this->hasOne('App\Transaction');
  }
}
