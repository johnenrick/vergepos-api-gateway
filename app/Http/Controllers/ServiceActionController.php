<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class ServiceActionController extends GenericController
{
  function __construct(){
    $this->model = new App\ServiceAction();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->initGenericController();
  }
}
