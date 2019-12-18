<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
  }
}
