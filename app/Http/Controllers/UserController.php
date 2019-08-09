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
              'company' => []
            ]
          ],
          'user_roles' => ['is_child' => true],
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
      // $this->retrieveCustomQueryModel = function($queryModel){
      //
      //   if(config('payload.company_id') && config('payload.company_id') > 1){
      //     $queryModel = $queryModel->leftJoin('company_users', 'company_users.user_id', '=', 'users.id');
      //     return $queryModel->where('company_users.company_id', config('payload.company_id'));
      //   }else if(config('payload.company_id')){
      //     return $queryModel->where('users.id', config('payload.company_id'));
      //   }else{
      //     return $queryModel;
      //   }
      // }
    }

    public function create(Request $request){
      // printR($request->all());

      $entry = $request->all();
      // $entry['email'] = rand().'@yahoo.com';
      $validation = new Core\GenericFormValidation($this->tableStructure, 'create');
      if(!config('payload.company_id')){
        $validation->additionalRule = ['company_code' => 'required|exists:companies,code'];
        $entry['user_role'] = 101;
      }else{
        $validation->additionalRule = ['company_user.company_id' => 'required|exists:companies,id'];
        if(config('payload.roles.1') == null){
          // $validation->additionalRule['user_role'] = ['required'];
          // $validation->additionalRule['user_role.id'] = 'required|gte:100';
          $companyUser = isset($entry['company_user']) ? $entry['company_user'] : ['company_id' => config('payload.company_id')];
          $entry['company_user'] = $companyUser;
        }
      }
    // printR($entry);

      if($validation->isValid($entry)){
        $companyUser = isset($entry['company_user']) ? $entry['company_user'] : [];
        unset($entry['company_user']);
        $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
        $userResult = $genericCreate->create($entry);
        if($userResult['id']){ // create company user
          $this->model = new App\CompanyUser();
          $this->tableStructure = [];
          $this->initGenericController();
          $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
          $companyID = null;

          $status = 0;
          if(config('payload.roles.1')){ // super admin
            $companyID = $companyUser['company_id'];
            $status = $entry['status'];
          }else if(config('payload.roles.100')){ //company admin
            $companyID = config('payload.company_id');
            $status = $entry['status'];
          }else{
            $company = (new App\Company())->where('code', $entry['company_code'])->get()->first()->toArray();

            $companyID = $company['id'];
            $this->model->useSessionCompanyID = false;
          }
          $this->responseGenerator->addDebug('company', 'got here');
          $companUserEntry = [
            'company_id' => $companyID,
            'user_id' => $userResult['id'],
            'status' => $status // not verified
          ];
          $companyUserResult = $genericCreate->create($companUserEntry);
          if($companyUserResult['id']){
            $this->model = new App\UserRole();
            if($status === 0){ // registration
              $this->model->useSessionCompanyID = false;
            }
            $this->tableStructure = [];
            $this->initGenericController();
            $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
            $roleID =  (config('payload.roles') == null || (!config('payload.roles.1') && !config('payload.roles.100'))) ? 101 : $userRole['id'];
            $this->responseGenerator->addDebug('roleID', $roleID);
            $this->responseGenerator->addDebug('payload.roles.1', config('payload.roles.1'));
            $this->responseGenerator->addDebug('payload.roles.100', config('payload.roles.100'));
            $userRoleEntry = [
              "user_id" => $userResult['id'],
              "role_id" => $roleID,
              "company_id" => $companyID
            ];
            $userRoleResult = $genericCreate->create($userRoleEntry);
            if($roleID * 1 == 100){
              $userRoleEntry = [
                "user_id" => $userResult['id'],
                "role_id" => 101,
                "company_id" => $companyID
              ];
              $this->model = new App\UserRole();
              $this->tableStructure = [];
              $this->initGenericController();
              $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
              $genericCreate->create($userRoleEntry);
            }
            if($userRoleResult['id']){
              $this->responseGenerator->setSuccess([
                'id' => $userResult['id'],
                // 'company_user_id' => $companyUserResult['id'],
                'user_role_id' => $userRoleResult['id']
              ]);
            }
          }else{
            $this->responseGenerator->addDebug('failed to create $companyUserResult', $companyRoleEntry);
          }
        }else{
          $this->responseGenerator->setSuccess([
            'id' => $userResult['id']
          ]);
        }
      }else{
        $this->responseGenerator->setFail([
          "code" => 1,
          "message" => $validation->validationErrors
        ]);
      }
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
