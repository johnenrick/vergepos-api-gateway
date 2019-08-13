<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class ProductController extends GenericController
{
  function __construct(){
    $this->model = new App\Product();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
        'category' => [
        ]
      ]
    ];
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
      $leftJoinedTable[] = 'categories';
      $queryModel = $queryModel->join('categories', "categories.id", "=", "products.category_id");
      $queryModel = $queryModel->where('categories.company_id', $this->userSession('company_id'));
      return $queryModel;
    };
    $this->initGenericController();
  }
}
