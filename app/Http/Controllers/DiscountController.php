<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class DiscountController extends GenericController
{
  function __construct(){
    $this->model = new App\Discount();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->basicOperationAuthRequired["retrieve"] = false;
    $this->initGenericController();
    $this->retrieveCustomQueryModel = function($queryModel){
      if($this->user('user_type_id') >= 10){
        return $queryModel->where('company_id', $this->user('company_id'));
      }else{
        return $queryModel;
      }
    };
  }
}
