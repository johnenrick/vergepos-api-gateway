<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Store extends GenericModel
{
    public function store_terminals(){
        return $this->hasMany('App\StoreTerminal');
    }
}
