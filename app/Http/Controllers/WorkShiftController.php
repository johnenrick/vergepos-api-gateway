<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;

class WorkShiftController extends GenericController
{
    function __construct(){
        $this->model = new App\WorkShift();
        $this->tableStructure = [
            'columns' => [
            ],
            'foreign_tables' => [
              'work_shift_cash_readings' => []
            ]
        ];
        $this->initGenericController();
        $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
            $leftJoinedTable[] = 'company_users';
            $queryModel = $queryModel->join('company_users', 'company_users.user_id', '=', 'work_shifts.user_id');
            $queryModel = $queryModel->where('company_id', $this->userSession('company_id'));
            return $queryModel;
        };
    }
    function sync(Request $request){
        $entries = $request->all();
        $terminals = $this->getStoreTerminal($this->userSession('company_id'));
        $validator = Validator::make($entries, [
            'store_terminal_id' => "required|numeric|in:".implode($terminals, ','),
            "work_shifts" => "required|array",
            "work_shifts.*.id" => "required|numeric",
            "work_shifts.*.user_id" => "required|exists:users,id",
            "work_shifts.*.closed_by_user_id" => "nullable|exists:users,id",
            "work_shifts.*.updated_at" => "required|date_format:Y-m-d H:i:s",
            "work_shifts.*.deleted_at" => "date_format:Y-m-d H:i:s|nullable",
            "work_shifts.*.end_datetime" => "date_format:Y-m-d H:i:s|nullable"
        ]);

        if($validator->fails()){
            $this->responseGenerator->setFail([
              "code" => 1,
              "message" => $validator->errors()->toArray()
            ]);
        }else{
          $storeTerminal = $entries['store_terminal_id'];
          $overrideValues = [ "store_terminal_id" => $storeTerminal ];
          $result = $this->syncEntries($entries['work_shifts'], $overrideValues);
          $this->responseGenerator->setSuccess($result);
        }
        return $this->responseGenerator->generate();
    }
    private function getStoreTerminal($companyId){
        $terminalModel = new App\StoreTerminal();
        $storeTerminalTableStructure = (new Core\TableStructure([
          'foreign_tables' => [
            'store' => []
          ]
        ], $terminalModel))->getStructure();
        $storeTerminalRequestArray = [
          'condition' => [
            [
              'column' => 'store.company_id',
              'value' => $companyId
            ]
          ],
          'limit' => 100,
          'offset' => 0,
          'select' => [
            'id',
            'store' => [
              'select' => [
                'id',
                'company_id'
              ]
            ]
          ]
        ];
        $storeTerminalGenericRetrieve = new Core\GenericRetrieve($storeTerminalTableStructure, $terminalModel, $storeTerminalRequestArray);
        return collect($storeTerminalGenericRetrieve->executeQuery())->pluck('id')->toArray();
    }
}
