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
    public function create(Request $request){
        $entry = $request->all();
        $resultObject = [
            "success" => false,
            "fail" => false
        ];
        $validation = new Core\GenericFormValidation($this->tableStructure, 'create');
        if($validation->isValid($entry)){
            $existingStoreTerminal = (new App\StoreTerminal())
                ->join('stores', "stores.id", "=", "store_terminals.store_id")
                ->where('stores.company_id', $this->userSession('company_id'))
                ->where('store_terminals.serial_number', $entry['serial_number'])
                ->get()->toArray();
            if(count($existingStoreTerminal) > 0){
                $this->responseGenerator->addDebug('$entry', $entry);
                $this->responseGenerator->addDebug('existingStoreTerminal', $existingStoreTerminal);
                $resultObject['fail'] = [
                    "code" => 101,
                    "message" => 'You already created a terminal with the serial number. Set this from existing terminal instead'
                ];
            }else{
                $genericCreate = new Core\GenericCreate($this->tableStructure, $this->model);
                $resultObject['success'] = $genericCreate->create($entry);
            }
        }else{
            $resultObject['fail'] = [
                "code" => 1,
                "message" => $validation->validationErrors
            ];
        }
        $this->responseGenerator->setSuccess($resultObject['success']);
        $this->responseGenerator->setFail($resultObject['fail']);
        return $this->responseGenerator->generate();
    }
}
