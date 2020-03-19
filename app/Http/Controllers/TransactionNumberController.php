<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class TransactionNumberController extends GenericController
{
  function __construct(){
    $this->model = new App\TransactionNumber();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'transaction' => [
          "is_child" => true,
          'foreign_tables' => [
            'transaction_products' => []
          ]
        ]
      ]
    ];
    $this->initGenericController();
  }
  public function sync(Request $request){
    $entry = $request->all();
    $resultObject = [
      "success" => false,
      "fail" => false  
    ];
    $terminals = $this->getStoreTerminal($this->userSession('company_id'));
    $this->responseGenerator->addDebug('terminals:'.$this->userSession('company_id'), $terminals);
    $validator = Validator::make($entry, [
      'store_terminal_id' => "required|numeric|in:".implode($terminals, ','),
      
      /* Transaction */
      'transaction_numbers' => "required|array",
      'transaction_numbers.*.id' => "required",
      'transaction_numbers.*.number' => "required|numeric",
      'transaction_numbers.*.operation' => "required|numeric",
      'transaction_numbers.*.user_id' => "required|numeric",
      /* Transaction Product */
      'transaction_numbers.*.transaction_voids' => "array",
      'transaction_numbers.*.transaction_voids.*.transaction_id' => "required",
      'transaction_numbers.*.transaction_voids.*.remarks' => "required|exists:products,id",
      'transaction_numbers.*.transaction_voids' => "array",
      'transaction_numbers.*.transaction_voids.*.remarks' => "required|exists:products,id",
      /* Transaction Number*/
      'transactions.*.transaction_number' => 'required|array',
      'transactions.*.transaction_number.number' => "required_id|numeric",
      'transactions.*.transaction_number.user_id' => "required|exists:users,id",
      'transactions.*.transaction_number.operation' => "required|in:1,2,3",

    ]);
    if($validator->fails()){
      $resultObject['fail'] = [
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ];
      $this->responseGenerator->setFail($resultObject['fail']);
    }else{

    }
  }
}
