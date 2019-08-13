<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;
class RoleController extends GenericController
{
  function __construct(){
    $this->model = new App\Role();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'role_access_lists' => [ "is_child" => true,'validation_required' => false]
      ]
    ];
    $this->initGenericController();
  }
  public function retrieve(Request $request){

    // printR($request->all());
    $requestArray = $this->systemGenerateRetrieveParameter($request->all());
    $validator = Validator::make($requestArray, ["select" => "required|array|min:1"]);
    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
      return $this->responseGenerator->generate();
    }
    if(!config('payload.roles.1')){
      if(!isset($requestArray['condition'])){
        $requestArray['condition'] = [];
      }
      $requestArray['condition'][] = [
        'column' => 'id',
        'clause' => '>=',
        'value' => 100
      ];
    }
    $genericRetrieve = new Core\GenericRetrieve($this->tableStructure, $this->model, $requestArray, $this->retrieveCustomQueryModel);
    $this->responseGenerator->setSuccess($genericRetrieve->executeQuery());
    if($genericRetrieve->totalResult != null){
      $this->responseGenerator->setTotalResult($genericRetrieve->totalResult);
    }
    return $this->responseGenerator->generate();
  }
}
