<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class RoleController extends GenericController
{
  function __construct(){
    $this->model = new App\Role();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'role_access_lists' => [ "is_child" => true,'validation_required' => false]
      ]
    ];
    $this->initGenericController();
  }
}
