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
        'company_users' => []
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
      'user.user_basic_information.first_name' => 'required',
      'user.user_basic_information.last_name' => 'required',
      'user.user_basic_information.last_name' => 'required',

    ];
    $this->responseGenerator->addDebug('entry', $entry);
    if($validation->isValid($entry)){
        $userEntry = $entry['user'];
        unset($entry['user']);
        $this->model->useSessionCompanyID = false;
        $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
        $resultObject['success'] = $genericCreate->create($entry);
        if($resultObject['success']){
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
            $this->addCompanyUser($resultObject['success']['id'], $userResult['id']);
            $this->addUserDefaultRole($resultObject['success']['id'], $userResult['id']);
          }
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
    $userRole = ['company_id' => $companyID, 'user_id' => $userID, 'role_id' => 100];
    $userRoleModel = new App\UserRole();
    $userRoleModel->useSessionCompanyID = false;
    $userRoleResult = (new Core\GenericCreate((new Core\TableStructure([
      'columns' => [
      ],
      'foreign_tables' => [
        'user_basic_information' => []
      ]
    ], $userRoleModel))->getStructure(), $userRoleModel))->create($userRole);
    return $userRoleResult;
  }
}
