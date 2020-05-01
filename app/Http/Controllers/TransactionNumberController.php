<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
          'is_child' => true,
          'foreign_tables' => [
            'transaction_products' => [
              'foreign_tables' => [
                'product' => []
              ]
            ],
            'transaction_computation' => [],
          ]
        ],
        'transaction_void' => [
          'is_child' => true,
          'foreign_tables' => [
            'transaction' => [
              'foreign_tables' => [
                'transaction_products' => [
                  'foreign_tables' => [
                    'product' => []
                  ]
                ],
                'transaction_computation' => [],
              ]
            ]
          ]
        ]
      ]
    ];
    $this->initGenericController();
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
      $leftJoinedTable[] = 'company_users';
      $queryModel = $queryModel->join('company_users', 'company_users.user_id', '=', 'transaction_numbers.user_id');
      $queryModel = $queryModel->where('company_id', $this->userSession('company_id'));
      return $queryModel;
    };
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
      'transaction_numbers.*.operation' => "required|in:1,2,3",
      'transaction_numbers.*.user_id' => "required|numeric",
      'transaction_numbers.*.created_at' => "required|date_format:Y-m-d H:i:s",
      /* Transaction Void */
      'transaction_numbers.*.transaction_void' => "array",
      'transaction_numbers.*.transaction_void.*.transaction_id' => "required_with:transaction_numbers.*.transaction_voids|numeric",
      'transaction_numbers.*.transaction_void.*.remarks' => "required_with:transaction_numbers.*.transaction_voids|exists:products,id",
      /* Transactions*/
      'transaction_numbers.*.transaction' => 'array',
      'transaction_numbers.*.transaction.id' => "required_with:transaction_numbers.*.transactions|numeric",
      'transaction_numbers.*.transaction.status' => "required_with:transaction_numbers.*.transactions|in:1,2,3",
      'transaction_numbers.*.transaction.cash_tendered' => "required_with:transaction_numbers.*.transactions|numeric",
      'transaction_numbers.*.transaction.cash_amount_paid' => "required_with:transaction_numbers.*.transactions|numeric",
      /* Transactions: Transaction Products */
      'transaction_numbers.*.transaction.transaction_products' => "array",
      'transaction_numbers.*.transaction.transaction_products.*.product_id' => "required|exists:products,id",
      'transaction_numbers.*.transaction.transaction_products.*.quantity' => "required|numeric",
      'transaction_numbers.*.transaction.transaction_products.*.cost' => "required|numeric",
      'transaction_numbers.*.transaction.transaction_products.*.vat_sales' => "required|numeric",
      'transaction_numbers.*.transaction.transaction_products.*.vat_exempt_sales' => "required|numeric",
      'transaction_numbers.*.transaction.transaction_products.*.vat_zero_rated_sales' => "required|numeric",
      'transaction_numbers.*.transaction.transaction_products.*.vat_amount' => "required|numeric",
      'transaction_numbers.*.transaction.transaction_products.*.discount_amount' => "required|numeric",

    ]);
    if($validator->fails()){
      $resultObject['fail'] = [
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ];
      $this->responseGenerator->setFail($resultObject['fail']);
    }else{
      $storeTerminalId = $entry['store_terminal_id'] * 1;
      $transactionNumbers = [];
      $transactionNumberCount = count($entry['transaction_numbers']);
      for($x = 0; $x < $transactionNumberCount; $x++){
        $transactionNumber = [
          'id' => null,
          'transaction' => null,
          'transaction_void' => null,
          'error' => null
        ];
        $transactionNumberEntry = $entry['transaction_numbers'][$x];
        $transactionNumberResult = $this->createTransactionNumber($transactionNumberEntry, $storeTerminalId);
        $transactionNumber['id'] = $transactionNumberResult['id'];
        if(!$transactionNumberResult['error']){
          $createdAt = $transactionNumberEntry['created_at'];
          if(isset($transactionNumberEntry['transaction']) && $transactionNumberEntry['transaction'] && $transactionNumberEntry['operation'] * 1 == 1){
            $transactionNumber['transaction'] = $this->createTransaction($transactionNumber['id'], $transactionNumberEntry['transaction'], $createdAt); //create transaction and transaction products
          }else if(isset($transactionNumberEntry['transaction_void']) && $transactionNumberEntry['transaction_void'] && $transactionNumberEntry['operation'] * 1 == 2){
            $transactionNumber['transaction_void'] = $this->createTransactionVoid($transactionNumber['id'], $transactionNumberEntry['transaction_void'], $createdAt, $storeTerminalId); //create transaction void
          }
        }else{ // failed to create transaction number
          $transactionNumber['error'] = $transactionNumberResult['error'];
        }
        $transactionNumbers[] = $transactionNumber;
      }
      $this->responseGenerator->setSuccess($transactionNumbers);
    }
    return $this->responseGenerator->generate();
  }
  private function createTransaction($transactionNumberId, $transactionEntry, $createdAt){
    $result = [
      'id' => null,
      'transaction_products' => [],
      'error' => null
    ];
    $transaction = new App\Transaction();
    $transaction->transaction_number_id = $transactionNumberId;
    $transaction->status = $transactionEntry['status'];
    $transaction->cash_tendered = $transactionEntry['cash_tendered'] . '';
    $transaction->cash_amount_paid = $transactionEntry['cash_amount_paid'] . '';
    
    $transaction->created_at = $createdAt;
    $transaction->updated_at = $createdAt;
    if(isset($transactionEntry['discount_id']) && $transactionEntry['discount_id']){
      $transaction->discount_id = $transactionEntry['discount_id'];
      $transaction->discount_remarks = $transactionEntry['discount_remarks'];
    }
    if($transaction->save()){
      $result['id'] = $transaction->id;
      $result['transaction_products'] = [];
      if(isset($transactionEntry['transaction_products']) && $transactionEntry['transaction_products']){
        $result['transaction_products'] = $this->createTransactionProducts($result['id'], $transactionEntry['transaction_products'], $createdAt);
      }
    }else{
      $result['error'] = 'create_failed';
    }
    return $result;
  }
  private function createTransactionProducts($transactionId, $transactionProducts, $createdAt){
    $result = [];
    for($x = 0; $x < count($transactionProducts); $x++){
      $productResult = [
        'id' => false,
        'error' => false
      ];
      $transactionProductModel = new App\TransactionProduct();
      $transactionProductModel->transaction_id = $transactionId;
      $transactionProductModel->product_id = $transactionProducts[$x]['product_id'] * 1;
      $transactionProductModel->quantity = $transactionProducts[$x]['quantity'] . '';
      $transactionProductModel->vat_sales = $transactionProducts[$x]['vat_sales'] . '';
      $transactionProductModel->cost = $transactionProducts[$x]['cost'] . '';
      $transactionProductModel->vat_exempt_sales = $transactionProducts[$x]['vat_exempt_sales'] . '';
      $transactionProductModel->vat_zero_rated_sales = $transactionProducts[$x]['vat_zero_rated_sales'] . '';
      $transactionProductModel->vat_amount = $transactionProducts[$x]['vat_amount'] . '';
      $transactionProductModel->created_at = $createdAt;
      $transactionProductModel->updated_at = $createdAt;
      $discountAmount = 0;
      if(isset($transactionProducts[$x]['discount_id']) && $transactionProducts[$x]['discount_id']){
        $transactionProductModel->discount_id = $transactionProducts[$x]['discount_id'] * 1;
        $discountAmount = $transactionProducts[$x]['discount_amount'] . '';
      }
      $transactionProductModel->discount_amount = $discountAmount;
      if($transactionProductModel->save()){
        $productResult['id'] = $transactionProductModel->id;
      }else{
        $productResult['error'] = 'create_failed';
      }
      $result[] = $productResult;
    }
    return $result;
  }
  private function createTransactionNumber($transactionNumber, $storeTerminalId){
    $result = [
      'id' => null,
      'error' => null
    ];
    $existingTransactionNumber = (new App\TransactionNumber())->where('store_terminal_id', $storeTerminalId)->where('number', $transactionNumber['number'])->get()->toArray();
    $doesExists = count($existingTransactionNumber) > 0;
    if($doesExists){
      $result['id'] = $existingTransactionNumber[0]['id'];
      $result['error'] = 'already_exists';
    }else{
      $transactionNumberModel = new App\TransactionNumber();
      $transactionNumberModel->store_terminal_id = $storeTerminalId;
      $transactionNumberModel->number = $transactionNumber['number'];
      $transactionNumberModel->operation = $transactionNumber['operation'];
      $transactionNumberModel->user_id = $transactionNumber['user_id'];
      $transactionNumberModel->created_at = $transactionNumber['created_at'];
      $transactionNumberModel->updated_at = $transactionNumber['created_at'];
      $insertResult = $transactionNumberModel->save();
      if($insertResult){
        $result['id'] = $transactionNumberModel->id * 1;
      }else{
        $result['error'] = 'create_failed';
      }
    }
    return $result;
  }
  
  private function createTransactionVoid($transactionNumberId, $transactionVoidEntry, $createdAt, $storeTerminalId){
    $result = [
      'id' => null,
      'error' => null
    ];
    $existingTransactionNumber = (new App\TransactionNumber())->where('number', $transactionVoidEntry['voided_transaction_number'])->where('store_terminal_id', $storeTerminalId)->with('transaction')->get()->toArray();
    if(count($existingTransactionNumber) == 0){ // check if the transaction to be voided exists
      $result['error'] = 'transaction_not_found';
    }else{
      $transactionVoid = new App\TransactionVoid();
      $transactionVoid->transaction_number_id = $transactionNumberId;
      $transactionVoid->transaction_id = $existingTransactionNumber[0]['transaction'] ? $existingTransactionNumber[0]['transaction']['id'] : null;
      $transactionVoid->voided_transaction_number = $transactionVoidEntry['voided_transaction_number'];
      $transactionVoid->remarks = $transactionVoidEntry['remarks'];
      $transactionVoid->created_at = $createdAt;
      $transactionVoid->updated_at = $createdAt;
      if($transactionVoid->save()){
        $result['id'] = $transactionVoid->id;
      }else{
        $result['error'] = 'create_failed';
      }
    }
    return $result;
  }
  private function getStoreTerminal($companyId){
    $terminalModel = new App\StoreTerminal();
    $storeTerminalTableStructure = (new Core\TableStructure([
      'foreign_tables' => [
        'store' => []
      ]
    ], $terminalModel))->getStructure();
    $storeTerminalRequestArray = [
      'condition' => [
        [
          'column' => 'store.company_id',
          'value' => $companyId
        ]
      ],
      'limit' => 100,
      'offset' => 0,
      'select' => [
        'id',
        'store' => [
          'select' => [
            'id',
            'company_id'
          ]
        ]
      ]
    ];
    $storeTerminalGenericRetrieve = new Core\GenericRetrieve($storeTerminalTableStructure, $terminalModel, $storeTerminalRequestArray);
    return collect($storeTerminalGenericRetrieve->executeQuery())->pluck('id')->toArray();
  }
}
