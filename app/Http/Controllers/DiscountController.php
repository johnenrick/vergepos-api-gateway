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
    // $this->basicOperationAuthRequired["retrieve"] = true;
    $this->initGenericController();
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
      $queryModel = $queryModel->where('company_id', $this->userSession('company_id'));
      return $queryModel;
    };
  }
}
