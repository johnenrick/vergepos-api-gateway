<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class PaymentMethodController extends GenericController
{
    function __construct(){
        $this->model = new App\PaymentMethod();
        $this->tableStructure = [
          'columns' => [
          ],
          'foreign_tables' => [
          ]
        ];
        $this->initGenericController();
    }
}
