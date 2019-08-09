<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserAuth extends Authenticatable implements JWTSubject
{
  protected $hidden = array('password');
  protected $table = 'users';
  use Notifiable;

  // Rest omitted for brevity

  /**
   * Get the identifier that will be stored in the subject claim of the JWT.
   *
   * @return mixed
   */
  public function getJWTIdentifier()
  {
      return $this->getKey();
  }

  /**
   * Return a key value array, containing any custom claims to be added to the JWT.
   *
   * @return array
   */
  public function getJWTCustomClaims()
  {
    $companyUser = (new CompanyUser())->select(['company_id'])->where('user_id', $this->id)->orderBy('created_at', 'DESC')->get()->toArray();
    $claims = [
      'company_id' => NULL,
      'roles' => NULL
    ];
    if(count($companyUser)){
      $companyID = $companyUser[0]['company_id'];
      $userRole = array_fill_keys(collect((new UserRole())->select(['role_id'])->where('user_id', $this->id)->where('company_id', $companyID)->get()->toArray())->pluck('role_id')->toArray(), true);
      $claims =[
        'company_id' => $companyID,
        'roles' => $userRole
      ];
    }
    return array(
      'custom' => $claims
    );
    return  [];
  }
}
