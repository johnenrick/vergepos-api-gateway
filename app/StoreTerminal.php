<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StoreTerminal extends GenericModel
{
    public function store(){
        return $this->belongsTo('App\Store');
    }
}
