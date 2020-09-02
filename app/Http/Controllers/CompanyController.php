<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class CompanyController extends GenericController
{
  function __construct(){
    $this->model = new App\Company();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'company_detail' => [],
        'company_users' => [],
        'stores' => [
          'foreign_tables' => [
            'store_terminals' => [
            ]
          ]
        ]
      ]
    ];
    $this->initGenericController();
  }
  function create(Request $request){
    $entry = $request->all();
    $resultObject = [
      "success" => false,
      "fail" => false
    ];
    $validation = new Core\GenericFormValidation($this->tableStructure, 'create');
    $validation->additionalRule = [
      'user.email' => 'required|email|unique:users,email',
      'user.password' => 'required|min:6',
      'user.pin' => 'required|size:4',
      'user.user_basic_information.first_name' => 'required|min:1|max:30|regex:/^[a-zA-Z0-9\s]+$/',
      'user.user_basic_information.last_name' => 'required|min:1|max:30|regex:/^[a-zA-Z0-9\s]+$/',
      'user.user_basic_information.middle_name' => 'max:30|regex:/^[a-zA-Z0-9\s]+$/',
    ];
    $this->responseGenerator->addDebug('entry', $entry);
    if($validation->isValid($entry)){
        $userEntry = $entry['user'];
        unset($entry['user']);
        $this->model->useSessionCompanyID = false;
        $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
        $resultObject['success'] = $genericCreate->create($entry);
        if($resultObject['success']){
          $companyId = $resultObject['success']['id'];
          $userModel = new App\User();
          $userGenericCreate = new Core\GenericCreate((new Core\TableStructure([
            'columns' => [
            ],
            'foreign_tables' => [
              'user_basic_information' => []
            ]
          ], $userModel))->getStructure(), $userModel);
          $userResult = $userGenericCreate->create($userEntry);
          if($userResult){
            $resultObject['success']['user'] = $userResult;
            $this->addCompanyUser($companyId, $userResult['id']);
            $this->addUserDefaultRole($companyId, $userResult['id']);
          }
          $resultObject['success']['stores'] = $this->addDefaultStore($companyId, $entry['name'], $entry['company_detail']['address']);
        }
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
  public function update(Request $request){
    $resultObject = [
      "success" => false,
      "fail" => false
    ];
    $entry = $request->all();
    // echo $this->userSession('company_id') . "---" . $entry['id']; 
    if($this->userSession('company_id') != $entry['id']){
      $resultObject['fail'] = [
        "code" => 401,
        "message" => "Do not have permission"
      ];
      $this->responseGenerator->setFail($resultObject['fail']);
      return $this->responseGenerator->generate();
    }

    // $this->tableStructure = (new Core\TableStructure($this->tableStructure, $this->model))->getStructure();
    $validation = new Core\GenericFormValidation($this->tableStructure, 'update');
    $this->responseGenerator->addDebug('validatio', $validation);
    if($validation->isValid($entry)){
      $genericUpdate = new Core\GenericUpdate($this->tableStructure, $this->model);
      $resultObject['success'] = $genericUpdate->update($entry);
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
  private function addDefaultStore($companyId, $companyName, $companyAddress){
    $store = [
      'company_id' => $companyId,
      'name' => $companyName,
      'address' => $companyAddress,
      'store_terminals' => [
        // [
        //   'description' => 'Default Terminal'
        // ]
      ]
    ];
    $storeModel = new App\Store();
    $storeModel->useSessionCompanyID = false;
    $tableStructure = (new Core\TableStructure([
      'columns' => [
      ],
      'foreign_tables' => [
        'store_terminals' => []
      ]
    ], $storeModel))->getStructure();
    $storeResult = (new Core\GenericCreate($tableStructure, $storeModel))->create($store);
    return $storeResult;
  }
  private function addCompanyUser($companyID, $userID){
    $companyUser = ['company_id' => $companyID, 'user_id' => $userID];
    $companyUserModel = new App\CompanyUser();
    $companyUserModel->useSessionCompanyID = false;
    $companyUserResult = (new Core\GenericCreate((new Core\TableStructure([
      'columns' => [
      ],
      'foreign_tables' => [
        'user_basic_information' => []
      ]
    ], $companyUserModel))->getStructure(), $companyUserModel))->create($companyUser);
    return $companyUserResult;
  }
  private function addUserDefaultRole($companyID, $userID){
    $userRoleAdmin = ['company_id' => $companyID, 'user_id' => $userID, 'role_id' => 100];
    $userRoleModel = new App\UserRole();
    $userRoleModel->useSessionCompanyID = false;
    $userRoleResult = (new Core\GenericCreate((new Core\TableStructure([], $userRoleModel))->getStructure(), $userRoleModel));
    $userRoleResult->create($userRoleAdmin);
    $userRoleModelCashier = new App\UserRole();
    $userRoleCashierResult = (new Core\GenericCreate((new Core\TableStructure([], $userRoleModel))->getStructure(), $userRoleModelCashier));
    $userRoleCashier = ['company_id' => $companyID, 'user_id' => $userID, 'role_id' => 101];
    $userRoleCashierResult->create($userRoleCashier);
    $userRoleModelManager = new App\UserRole();
    $userRoleManagerResult = (new Core\GenericCreate((new Core\TableStructure([], $userRoleModel))->getStructure(), $userRoleModelManager));
    $userRoleManager = ['company_id' => $companyID, 'user_id' => $userID, 'role_id' => 102];
    $userRoleManagerResult->create($userRoleManager);
    return $userRoleResult;
  }
}
