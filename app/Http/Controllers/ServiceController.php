<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class ServiceController extends GenericController
{
  function __construct(){
    $this->model = new App\Service();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'service_actions' => ["is_child" => true]
      ]
    ];
    $this->initGenericController();
  }

}
