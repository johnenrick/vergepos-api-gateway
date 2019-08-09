<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class UserAccessListController extends GenericController
{
  function __construct(){
    $this->model = new App\UserAccessList();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->initGenericController();
  }
}
