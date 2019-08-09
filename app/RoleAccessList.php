<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleAccessList extends GenericModel
{
  public function service_action(){
    return $this->belongsTo('App\ServiceAction', 'service_action_registry_id', 'id');
  }
}
