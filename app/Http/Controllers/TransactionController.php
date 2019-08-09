<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class TransactionController extends GenericController
{
  function __construct(){
    $this->model = new App\Transaction();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'transaction_number' => [],
        'transaction_products' => [],
        'transaction_computation' => []
      ]
    ];
    $this->initGenericController();
  }

  public function create(Request $request){
    $entry = $request->all();
    $resultObject = [
      "success" => false,
      "fail" => false
    ];
    $validation = new Core\GenericFormValidation($this->tableStructure, 'create');
    if($validation->isValid($entry)){

      $result = $this->createTransaction($entry); // $genericCreate->create($entry);
      $this->responseGenerator->addDebug('txId', $result);
      if($result){
        $this->responseGenerator->setSuccess($result);
      }
    }else{
      $resultObject['fail'] = [
        "code" => 1,
        "message" => $validation->validationErrors
      ];
      $this->responseGenerator->setFail($resultObject['fail']);
    }
    return $this->responseGenerator->generate();
  }
  public function createTransaction($entry){
    $transactionNumber = new App\TransactionNumber();
    $transactionNumberResult = $transactionNumber->createEntry([
      'operation' => 1
    ]);
    if($transactionNumberResult){
      $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
      $entry['transaction_number_id'] = $transactionNumberResult;
      $result = $genericCreate->create($entry);
      if($result){
        $result['transaction_number_id'] = $transactionNumberResult;
        return $result;
      }else{
        $this->responseGenerator->setFail([
          "code" => 102,
          "message" => "Failed to create transaction"
        ]);
      }
    }else{
      $this->responseGenerator->setFail([
        "code" => 101,
        "message" => "Failed to create transaction number"
      ]);
      return false;
    }
  }
}
