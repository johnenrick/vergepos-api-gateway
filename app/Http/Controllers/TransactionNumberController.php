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
        'transaction' => [ "is_child" => true]
      ]
    ];
    $this->initGenericController();
  }
}
