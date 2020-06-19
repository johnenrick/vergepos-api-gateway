<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class StoreTerminalController extends GenericController
{
    function __construct(){
        $this->model = new App\StoreTerminal();
        $this->tableStructure = [
            'columns' => [
            ],
            'foreign_tables' => [
            ]
        ];
        $this->initGenericController();
        $this->retrieveCustomQueryModel = function($queryModel, $leftJoinedTable){
            $queryModel = $queryModel->join('stores', "stores.id", "=", "store_terminals.store_id");
            $queryModel = $queryModel->where('stores.company_id', $this->userSession('company_id'));
            return $queryModel;
        };
    }
}
