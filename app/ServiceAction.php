<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceAction extends GenericModel
{
    protected $fillable = ['description', 'link', 'auth_required'];
    public function service(){
      return $this->belongsTo('App\Service');
    }

}
