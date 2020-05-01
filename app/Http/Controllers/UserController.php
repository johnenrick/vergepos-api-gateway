<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App;
class UserController extends GenericController
{
    function __construct(){
      $this->model = new App\User();
      $this->tableStructure = [
        'columns' => [
        ],
        'foreign_tables' => [
          'user_basic_information' => ['is_child' => true, 'validation_required' => true],
          'company_user' => [
            'is_child' => true,
            'validation_required' => false,
            'foreign_tables' => [
              'company' => [
                'is_child' => false,
                'validation_required' => false,
                'foreign_tables' => [
                  'company_detail' => []
                ]
              ]
            ]
          ],
          'user_roles' => [
            'is_child' => true,
            'foreign_tables' => [
              'role' => []
            ]
          ],
          // 'user_bio' => ['validation_required' => false],
          // 'user_addresses' => [],
          // 'user_educational_backgrounds' => [],
          // 'user_organizations' => [],
          // 'user_awards' => [],
          // 'user_professional_activities' => [],
          // 'user_social_media_links' => [],
          // 'user_contact_number' => [],
          // 'user_profile_picture' => [],
          // 'user_followers' => [],
          // 'user_contacts' => []
        ]
      ];
      $this->initGenericController();
      $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
        $leftJoinedTable[] = 'company_users';
        $queryModel = $queryModel->join('company_users', 'company_users.user_id', '=', 'users.id');
        $queryModel = $queryModel->where('company_id', $this->userSession('company_id'));
        return $queryModel;
      };
    }
    public function hasInvalidUserRoles($userRoles){
      for($x = 0; $x < count($userRoles); $x++){
        if($userRoles[$x]['role_id'] * 1 < 100 && !$this->userSession('roles.1')){
          return true;
        }
        if($userRoles[$x]['role_id'] * 1 >= 100 && !$this->userSession('roles.100')){
          return true;
        }
      }
      // return $userRoles
    }
    public function create(Request $request){
      $requestData = $request->all();
      $requestData['company_user'] = [
        "company_id" => $this->userSession('company_id')
      ];
      if(isset($requestData['user_roles']) && $this->hasInvalidUserRoles($requestData['user_roles'])){
        $this->responseGenerator->setFail([
          "code" => 2,
          "message" => "You dont have the previlege to set the user roles"
        ]);
        return $this->responseGenerator->generate();
      }
      $resultObject = [
        "success" => false,
        "fail" => false
      ];
      $validation = new Core\GenericFormValidation($this->tableStructure, 'create');
      if($validation->isValid($requestData)){
          $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
          $resultObject['success'] = $genericCreate->create($requestData);
      }else{
        $resultObject['fail'] = [
          "code" => 1,
          "message" => $validation->validationErrors
        ];
      }
      $this->responseGenerator->setSuccess($resultObject['success']);
      $this->responseGenerator->setFail($resultObject['fail']);
      return $this->responseGenerator->generate();
    }


    public function changePassword(Request $request){
      if(!auth()->user()){
        $this->responseGenerator->setFail(["code" => 2, "message" => "Not Logged In"]);
        return $this->responseGenerator->generate();
      }
      $requestArray = $request->all();
      $validationRules = $this->model->getValidationRule();
      $validator = Validator::make($requestArray, [
        "current_password" => "required|".$validationRules['password'],
        "new_password" => "required|".$validationRules['password']
      ]);
      if($validator->fails()){
        $validator->errors()->toArray();
        $this->responseGenerator->setFail([
          "code" => 1,
          "message" => $validator->errors()->toArray()
        ]);
        return $this->responseGenerator->generate();
      }
      $user = auth()->user()->toArray();
      if(Auth::validate(["email" => $user['email'], "password" => $requestArray["current_password"]])){
        $result = $this->model->updateEntry($user['id'], ["password" => $requestArray["new_password"] ]);
        $this->responseGenerator->setSuccess($result);
      }else{
        $this->responseGenerator->setFail([
          "code" => 10,
          "message" => 'Current Password Incorrect'
        ]);
      }
      return $this->responseGenerator->generate();
    }
    public function requestChangePassword(Request $request){
      $requestArray = $request->all();
      $validator = Validator::make($requestArray, [
        "email" => "required|email|exists:users,email"
      ]);
      if($validator->fails()){
        $validator->errors()->toArray();
        $this->responseGenerator->setFail([
          "code" => 1,
          "message" => $validator->errors()->toArray()
        ]);
      }else{
        $code = substr(base_convert(time(), 10, 24), 0 , 7);
        $this->model = new App\ChangePasswordRequest();
        $this->model->email = $requestArray['email'];
        $this->model->confirmation_code = $code;
        if($this->model->save()){
          $this->responseGenerator->setSuccess([
            "confirmation_code" => $code
          ]);
        }else{
          $this->responseGenerator->setFail([
            "code" => 2,
            "message" => 'System Error. Failed to make the request'
          ]);
        }
      }
      return $this->responseGenerator->generate();
    }
    
    public function confirmChangePassword(Request $request){
      $requestArray = $request->all();
      $validator = Validator::make($requestArray, [
        "email" => "required|email|exists:users,email",
        "confirmation_code" => "required|exists:change_password_requests,confirmation_code",
        "new_password" => "required|min:6",
      ]);
      if($validator->fails()){
        $validator->errors()->toArray();
        $this->responseGenerator->setFail([
          "code" => 1,
          "message" => $validator->errors()->toArray()
        ]);
      }else{
        $changePasswordRequestModel = new App\ChangePasswordRequest();
        $changePasswordRequestResult = ($changePasswordRequestModel->where('confirmation_code', $requestArray['confirmation_code'])->where('email', $requestArray['email'])->get())->toArray();
        if(count($changePasswordRequestResult) === 0){
          $this->responseGenerator->setFail([
            "code" => 2,
            "message" => 'Cannot find any request that matches the given Confirmation Code and Email'
          ]);
        }else{
          $requestLife =  time() - strtotime($changePasswordRequestResult[0]['created_at']); // in seconds
          if($requestLife >= 36000){ // if life is more than 10 hours old
            $this->responseGenerator->setFail([
              "code" => 3,
              "message" => 'Request has already expired. Try requesting again and confirm it as soon as you receive the email'
            ]);
          }else if($changePasswordRequestResult[0]['status'] * 1 === 2){ // invalidated
            $this->responseGenerator->setFail([
              "code" => 4,
              "message" => 'Request has been invalidated'
            ]);
          }else if($changePasswordRequestResult[0]['status'] * 1 === 1){ // request already used and cannot be reused
            $this->responseGenerator->setFail([
              "code" => 5,
              "message" => 'You have already changed your password using the confirmation code. Try logging in your account or make another password change request'
            ]);
          }else{ // ok
            $userResult = ((new App\User())->where('email', $changePasswordRequestResult[0]['email'])->get())->toArray();
            if(count($userResult) !== 1){
              $this->responseGenerator->setFail([
                "code" => 6,
                "message" => 'System error! Please contact us immediately'
              ]);
            }else{
              $userUpdateResult = (new App\User())->updateEntry($userResult[0]['id'], [
                "password" => $requestArray['new_password']
              ]);
              if($userUpdateResult){
                if((new App\ChangePasswordRequest())->updateEntry($changePasswordRequestResult[0]["id"], ["status" => 1])){
                  $this->responseGenerator->setSuccess(true);
                }
              }
            }
          }
        }
      }
      // check code if used
      // update request db
      return $this->responseGenerator->generate();
    }
}
