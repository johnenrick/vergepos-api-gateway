<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;

class TransactionController extends GenericController
{
  private $startingTransactionNumber = NULL;
  private $storeTerminalId = NULL;
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
  public function sync(Request $request){
    $entry = $request->all();
    $resultObject = [
      "success" => false,
      "fail" => false  
    ];
    // printR($this->userSession('company_id') * 1, 'session');
    $terminalModel = new App\StoreTerminal();
    $terminals = collect((new Core\GenericRetrieve(
      (new Core\TableStructure([
        'columns' => [
        ],
        'foreign_tables' => [
          'store' => []
        ]
      ], $terminalModel))->getStructure()
      , $terminalModel, [
        'condition' => [
          [
            'column' => 'store.company_id',
            'value' => $this->userSession('company_id') * 1
          ]
        ],
        'limit' => 100,
        'offset' => 1,
        'select' => [
          'id',
          // 'store' => [
          //   'select' => [
          //     'id',
          //     'company_id'
          //   ]
          // ]
        ]
    ]))->executeQuery())->pluck('id')->toArray();
    // printR(implode($terminals, ','), 'terminals');
    $this->responseGenerator->addDebug('terminals', $terminals);
    $validator = Validator::make($entry, [
      'store_terminal_id' => "required|numeric|in:".implode($terminals, ','),
      
      /* Transaction */
      'transactions' => "required|array",
      'transactions.*.id' => "required",
      'transactions.*.status' => "required|in:1,2,3",
      'transactions.*.cash_tendered' => "required|numeric",
      'transactions.*.cash_amount_paid' => "required|numeric",
      /* Transaction Product */
      'transactions.*.transaction_products' => "required|array",
      'transactions.*.transaction_products.*.product_id' => "required|exists:products,id",
      'transactions.*.transaction_products.*.quantity' => "required|numeric",
      'transactions.*.transaction_products.*.vat_sales' => "required|numeric",
      'transactions.*.transaction_products.*.vat_exempt_sales' => "required|numeric",
      'transactions.*.transaction_products.*.vat_zero_rated_sales' => "required|numeric",
      'transactions.*.transaction_products.*.vat_amount' => "required|numeric",
      'transactions.*.transaction_products.*.discount_amount' => "required|numeric",
      /* Transaction Number*/
      'transactions.*.transaction_number' => 'required|array',
      'transactions.*.transaction_number.number' => "required|numeric",
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
      $this->storeTerminalId = $entry['store_terminal_id'];
      $transactions = $entry['transactions'];
      $transactionResults = [];
      for($x = 0; $x < count($transactions); $x++){
        $transactionResult = [
          'id' => false,
          'transaction_number' => false,
          'transaction_products' => false,
          'error' => false
        ];
        $transaction = new App\Transaction();
        $transactionNumber = $this->saveTransactionNumber($transactions[$x]['transaction_number']);
        if($transactionNumber == 'already_exists'){
          $transactionResult['error'] = 'transaction_number_already_exists';
        }else{
          $transactionResult['transaction_number'] = [
            'id' => $transactionNumber
          ];
          $transaction->transaction_number_id = $transactionNumber;
          $transaction->status = $transactions[$x]['status'];
          $transaction->cash_tendered = $transactions[$x]['cash_tendered'];
          $transaction->cash_amount_paid = $transactions[$x]['cash_amount_paid'];
          if($transaction->save()){
            $transactionResult['id'] = $transaction->id;
            $transactionResult['transaction_products'] = $this->saveTransactionProducts($transactionResult['id'], $transactions[$x]['transaction_products']);
          }else{
            $transactionResult['error'] = 'create_transaction_failed';
          }
        }
        $transactionResults[] = $transactionResult;
      }
      $this->responseGenerator->setSuccess($transactionResults);
    }
    return $this->responseGenerator->generate();
  }
  public function saveTransactionProducts($transactionId, $transactionProducts){
    $productResults = [];
    for($x = 0; $x < count($transactionProducts); $x++){
      $productResult = [
        'id' => false,
        'error' => false
      ];
      $transactionProductModel = new App\TransactionProduct();
      $transactionProductModel->transaction_id = $transactionId;
      $transactionProductModel->product_id = $transactionProducts[$x]['product_id'];
      $transactionProductModel->quantity = $transactionProducts[$x]['quantity'];
      $transactionProductModel->vat_sales = $transactionProducts[$x]['vat_sales'];
      $transactionProductModel->vat_exempt_sales = $transactionProducts[$x]['vat_exempt_sales'];
      $transactionProductModel->vat_zero_rated_sales = $transactionProducts[$x]['vat_zero_rated_sales'];
      $transactionProductModel->vat_amount = $transactionProducts[$x]['vat_amount'];
      $transactionProductModel->discount_amount = $transactionProducts[$x]['discount_amount'];
      if($transactionProductModel->save()){
        $productResult['id'] = $transactionProductModel->id;
      }else{
        $productResult['error'] = 'create_transaction_product_failed';
      }
      $productResults[] = $productResult;
    }
    return $productResults;
  }
  public function saveTransactionNumber($transactionNumber){
    $doesExists = count((new App\TransactionNumber())->where('store_terminal_id', $this->storeTerminalId)->where('number', $transactionNumber['number'])->get()->toArray()) > 0;
    if($doesExists){
      return 'already_exists';
    }
    $transactionNumberModel = new App\TransactionNumber();
    $transactionNumberModel->store_terminal_id = $this->storeTerminalId;
    $transactionNumberModel->number = $transactionNumber['number'];
    $transactionNumberModel->operation = $transactionNumber['operation'];
    $transactionNumberModel->user_id = $transactionNumber['user_id'];
    $result = $transactionNumberModel->save();
    if($result){
      return $transactionNumberModel->id;
    }else{
      return false;
    }
    // printR($result->id, 'result');
  }
}
