<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionVoid extends GenericModel
{
    public function transaction_number(){
        return $this->hasMany('App\TransactionNumber');
    }
    
}
