<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends GenericModel
{
    protected $hidden = array('password');
    // protected $fillable = ['user_id', 'first_name', 'middle_name', 'last_name', 'mobile_number', 'gender', 'birthdate', 'occupation'];
    protected $validationRules = [
      'email' => 'required|email|unique:users,email,except,id',
      'password' => 'required|min:4',
      'pin' => 'required|min:4|max:4'
    ];
    protected $defaultValue = [
      'middle_name' => ''
    ];
    protected $validationRuleNotRequired = ['username', 'middle_name', 'status', 'user_type_id'];
    public function systemGenerateValue($data){
      (isset($data['email'])) ? $data['username'] = $data['email'] : null;
      (isset($data['password'])) ? $data['password'] = Hash::make($data['password']) : null;
      if((!isset($data['id']) || $data['id'] == 0) && !isset($data['status'])){ // if create
        $data['status'] = 1;
      }
      return $data;
    }
    // public function company_users()
    // {
    //     return $this->hasMany('App\CompanyUser');
    // }
    public function company_user()
    {
        return $this->hasOne('App\CompanyUser');
    }
    public function user_roles()
    {
        return $this->hasMany('App\UserRole');
    }
    public function user_basic_information()
    {
        return $this->hasOne('App\UserBasicInformation');
    }
    public function user_bio()
    {
        return $this->hasOne('App\UserBio');
    }
    public function user_addresses()
    {
        return $this->hasMany('App\UserAddress')->with(['region']);
    }
    public function user_educational_backgrounds()
    {
        return $this->hasMany('App\UserEducationalBackground');
    }
    public function user_organizations()
    {
        return $this->hasMany('App\UserOrganization');
    }
    public function user_awards()
    {
        return $this->hasMany('App\UserAward');
    }
    public function user_professional_activities()
    {
        return $this->hasMany('App\UserProfessionalActivity');
    }
    public function user_social_media_links()
    {
        return $this->hasMany('App\UserSocialMediaLink');
    }
    public function user_contact_number()
    {
        return $this->hasOne('App\UserContactNumber');
    }
    public function user_profile_picture()
    {
        return $this->hasOne('App\UserProfilePicture');
    }
    public function user_followers()
    {
        return $this->hasMany('App\UserFollower');
    }
    public function user_contacts()
    {
        return $this->hasMany('App\UserContact', 'contact_user_id');
    }
}
