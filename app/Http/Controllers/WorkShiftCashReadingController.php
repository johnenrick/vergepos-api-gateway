<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;

class WorkShiftCashReadingController extends GenericController
{
    function __construct(){
        $this->model = new App\WorkShiftCashReading();
        $this->tableStructure = [
            'columns' => [
            ],
            'foreign_tables' => [
            ]
        ];
        $this->initGenericController();
        $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
            $leftJoinedTable[] = 'work_shifts';
            $queryModel = $queryModel->join('work_shifts', "work_shifts.id", "=", "work_shift_cash_readings.work_shift_id");

            $leftJoinedTable[] = 'company_users';
            $queryModel = $queryModel->join('company_users', 'company_users.user_id', '=', 'work_shifts.user_id');
            $queryModel = $queryModel->where('company_users.company_id', $this->userSession('company_id'));
            // $queryModel = $queryModel->where('work_shifts.company_id', $this->userSession('company_id'));
            // $queryModel = $queryModel->select('work_shifts.company_id');
            return $queryModel;
        };
    }
    function sync(Request $request){
        $entries = $request->all();
        $validator = Validator::make($entries, [
            "work_shift_cash_readings" => "required|array",
            "work_shift_cash_readings.*.work_shift_id" => "required|exists:work_shifts,id",
            "work_shift_cash_readings.*.type" => "required|in:1,2,3,4",
            "work_shift_cash_readings.*.approved_by_user_id" => "exists:users,id|nullable",
            "work_shift_cash_readings.*.bill_1_cent" => "numeric",
            "work_shift_cash_readings.*.bill_5_cent" => "numeric",
            "work_shift_cash_readings.*.bill_10_cent" => "numeric",
            "work_shift_cash_readings.*.bill_25_cent" => "numeric",
            "work_shift_cash_readings.*.bill_1_peso" => "numeric",
            "work_shift_cash_readings.*.bill_5_peso" => "numeric",
            "work_shift_cash_readings.*.bill_10_peso" => "numeric",
            "work_shift_cash_readings.*.bill_20_peso" => "numeric",
            "work_shift_cash_readings.*.bill_50_peso" => "numeric",
            "work_shift_cash_readings.*.bill_100_peso" => "numeric",
            "work_shift_cash_readings.*.bill_200_peso" => "numeric",
            "work_shift_cash_readings.*.bill_500_peso" => "numeric",
            "work_shift_cash_readings.*.bill_1000_peso" => "numeric",
            "work_shift_cash_readings.*.discrepancy" => "numeric",
            "work_shift_cash_readings.*.other_payments" => "numeric",
            "work_shift_cash_readings.*.updated_at" => "required|date_format:Y-m-d H:i:s",
            "work_shift_cash_readings.*.created_at" => "required|date_format:Y-m-d H:i:s",
            "work_shift_cash_readings.*.deleted_at" => "date_format:Y-m-d H:i:s|nullable"
        ]);
        if($validator->fails()){
            $this->responseGenerator->setFail([
              "code" => 1,
              "message" => $validator->errors()->toArray()
            ]);
        }else{
            foreach($entries['work_shift_cash_readings'] as $key => $cashReadings){
                $entries['work_shift_cash_readings'][$key]['approved_by_user_id'] = isset($cashReadings['approved_by_user_id']) ? $cashReadings['approved_by_user_id'] : NULL;
                $entries['work_shift_cash_readings'][$key]['bill_1_cent'] = isset($cashReadings['bill_1_cent']) ? $cashReadings['bill_1_cent'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_5_cent'] = isset($cashReadings['bill_5_cent']) ? $cashReadings['bill_5_cent'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_10_cent'] = isset($cashReadings['bill_10_cent']) ? $cashReadings['bill_10_cent'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_25_cent'] = isset($cashReadings['bill_25_cent']) ? $cashReadings['bill_25_cent'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_1_peso'] = isset($cashReadings['bill_1_peso']) ? $cashReadings['bill_1_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_5_peso'] = isset($cashReadings['bill_5_peso']) ? $cashReadings['bill_5_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_10_peso'] = isset($cashReadings['bill_10_peso']) ? $cashReadings['bill_10_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_20_peso'] = isset($cashReadings['bill_20_peso']) ? $cashReadings['bill_20_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_50_peso'] = isset($cashReadings['bill_50_peso']) ? $cashReadings['bill_50_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_100_peso'] = isset($cashReadings['bill_100_peso']) ? $cashReadings['bill_100_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_200_peso'] = isset($cashReadings['bill_200_peso']) ? $cashReadings['bill_200_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_500_peso'] = isset($cashReadings['bill_500_peso']) ? $cashReadings['bill_500_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['bill_1000_peso'] = isset($cashReadings['bill_1000_peso']) ? $cashReadings['bill_1000_peso'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['discrepancy'] = isset($cashReadings['discrepancy']) ? $cashReadings['discrepancy'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['other_payments'] = isset($cashReadings['other_payments']) ? $cashReadings['other_payments'] * 1 : 0;
                $entries['work_shift_cash_readings'][$key]['deleted_at'] = isset($cashReadings['deleted_at']) ? $cashReadings['deleted_at'] : null;
                $entries['work_shift_cash_readings'][$key]['remarks'] = isset($cashReadings['remarks']) ? strip_tags($cashReadings['remarks']) : null;
            }
            $result = $this->syncEntries($entries['work_shift_cash_readings']);
            $this->responseGenerator->setSuccess($result);
        }
        return $this->responseGenerator->generate();
    }
}
