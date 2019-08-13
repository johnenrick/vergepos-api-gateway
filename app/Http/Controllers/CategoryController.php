<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class CategoryController extends GenericController
{
  function __construct(){
    $this->model = new App\Category();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'category' => [
          'is_child' => false,
          'validation_required' => false
        ],
        'categories' => [
        ],
        'products' => []
      ]
    ];
    $this->initGenericController();
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
      $queryModel = $queryModel->where('company_id', $this->userSession('company_id'));
      return $queryModel;
    };
  }
}
