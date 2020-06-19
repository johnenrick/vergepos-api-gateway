<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
class StoreController extends GenericController
{
    function __construct(){
        $this->model = new App\Store();
        $this->tableStructure = [
          'columns' => [
          ],
          'foreign_tables' => [
            'category' => [
            ]
          ]
        ];
        $this->initGenericController();
        $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
          $queryModel = $queryModel->where('company_id', $this->userSession('company_id'));
          return $queryModel;
        };
      }
}
