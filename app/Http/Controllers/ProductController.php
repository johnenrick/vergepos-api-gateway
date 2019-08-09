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
    $this->initGenericController();
  }
}
