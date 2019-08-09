<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends GenericModel
{
  protected $validationRuleNotRequired = ['transaction_number_id', 'customer_id', 'status'];
  public function systemGenerateValue($data){
    $data['status'] = 1;
    return $data;
  }
  protected $formulatedColumn = [
    'total_vat_sales' => "SUM('transaction_products.vat_sales')"
  ];
  public function transaction_products(){
    return $this->hasMany('App\TransactionProduct');
  }
  public function transaction_computation(){
    return $this->hasOne('App\TransactionComputation');
  }
}
