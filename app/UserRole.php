<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRole extends GenericModel
{
    public function role(){
      return $this->belongsTo('App\Role');
    }
}
