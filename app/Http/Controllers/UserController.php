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
}
