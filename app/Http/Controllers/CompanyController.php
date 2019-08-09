<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class CompanyController extends GenericController
{
  function __construct(){
    $this->model = new App\Company();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->initGenericController();
  }
}
