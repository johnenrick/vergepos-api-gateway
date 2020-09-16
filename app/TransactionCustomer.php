<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionCustomer extends GenericModel
{
    public function customer(){
        return $this->belongsTo('App\Customer');
    }
}
