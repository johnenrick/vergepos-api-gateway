<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;

class CustomerController extends GenericController
{
  function __construct(){
    $this->model = new App\Customer();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->initGenericController();
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
      $queryModel = $queryModel->where('company_id', $this->userSession('company_id'));
      return $queryModel;
    };
  }
  function batchCreate(Request $request){
    $entries = $request->all();
    $validator = Validator::make($entries, [
      "customers" => "required|array",
      "customers.*.name" => "required|max:30|regex:/^[a-zA-Z0-9\s]+$/",
      "customers.*.address" => "max:100|regex:/^[a-zA-Z0-9\s]+$/",
      "customers.*.birthdate" => "date",
    ]);

    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      foreach($entries['customers'] as $entryKey => $entry){
        $entries['customers'][$entryKey]['company_id'] = $this->userSession('company_id');
        unset($entries['customers'][$entryKey]['id']);
        unset($entries['customers'][$entryKey]['db_id']);
        unset($entries['customers'][$entryKey]['created_at']);
        unset($entries['customers'][$entryKey]['updated_at']);
        unset($entries['customers'][$entryKey]['deleted_at']);
      }
      $customerDB = new App\Customer();
      $this->responseGenerator->addDebug('pis', $entries);
      $result = $customerDB->insert($entries['customers']);
      $ids = ($customerDB->orderBy('id', 'desc')->where('company_id', $this->userSession('company_id'))->take(count($entries['customers']))->pluck('id'))->toArray();
      $ids = array_reverse($ids);
      $this->responseGenerator->setSuccess(["customer_ids" => $ids]);
    }
    return $this->responseGenerator->generate();
  }
}
