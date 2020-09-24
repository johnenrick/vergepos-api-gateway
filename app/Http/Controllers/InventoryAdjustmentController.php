<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;

class InventoryAdjustmentController extends GenericController
{
    function __construct(){
    $this->model = new App\InventoryAdjustment();
    $this->tableStructure = [
      'columns' => [
      ],
      'foreign_tables' => [
      ]
    ];
    $this->retrieveCustomQueryModel = function($queryModel, &$leftJoinedTable){
        $leftJoinedTable[] = 'company_users';
        $queryModel = $queryModel->join('company_users', "company_users.user_id", "=", "inventory_adjustments.user_id");
        $queryModel = $queryModel->where('company_users.company_id', $this->userSession('company_id'));
        $queryModel = $queryModel->select('company_users.company_id');
        return $queryModel;
    };
    $this->initGenericController();
  }

  function batchCreate(Request $request){
    $entries = $request->all();
    $validator = Validator::make($entries, [
      "last_datetime_update" => 'required|date',
      "store_id" => "required|exists:store_terminals,store_id",
      "inventory_adjustments" => "required|array",
      "inventory_adjustments.*.product_id" => "required|exists:products,id",
      "inventory_adjustments.*.user_id" => "required|exists:users,id",
      "inventory_adjustments.*.type" => "required|in:1,2,3",
      "inventory_adjustments.*.quantity" => "required|numeric",
      "inventory_adjustments.*.previous_quantity" => "required|numeric",
      "inventory_adjustments.*.created_at" => "required|date",
      "inventory_adjustments.*.updated_at" => "required|date",
    ]);

    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      foreach($entries['inventory_adjustments'] as $entryKey => $entry){
        $entries['inventory_adjustments'][$entryKey]['remarks'] = strip_tags($entries['inventory_adjustments'][$entryKey]['remarks']);
        $entries['inventory_adjustments'][$entryKey]['store_id'] = $entries['store_id'];
        unset($entries['inventory_adjustments'][$entryKey]['id']);
        unset($entries['inventory_adjustments'][$entryKey]['db_id']);
      }
      
      $newIds = $this->insertNewInventoryAdjustments($entries['inventory_adjustments']); // the function will modify the first parameter array
      $this->groupSyncInventoryAdjustments($entries['inventory_adjustments'], $entries['store_id'], $entries['last_datetime_update']);
      $updatedInventoryAdjustments = $this->getUpdatedInventoryadjustments($entries['last_datetime_update'], $entries['store_id']);
      $this->responseGenerator->setSuccess([
        "new_inventory_adjustment_ids" => $newIds,
        "updated_inventory_adjustments" => $updatedInventoryAdjustments
      ]);
    }
    return $this->responseGenerator->generate();
  }
  private function groupSyncInventoryAdjustments($inventoryAdjustments, $storeId, $lastDatetimeUpdate){
    $groupedProducts = collect($inventoryAdjustments)->groupBy('product_id')->toArray();
    $productInventories = [];
    foreach($groupedProducts as $productId => $productInventoryAdjustments){
      $inventoryAdjustmentDB = new App\InventoryAdjustment();
      $allInventoryAdjustments = $inventoryAdjustmentDB->where('product_id', $productId)
        ->where('store_id', $storeId)->where('created_at', '>=', $lastDatetimeUpdate)
        ->orderBy('created_at', 'asc')->get()->toArray();
      $this->recountPreviousQuantity($allInventoryAdjustments);
    }
  }
  private function recountPreviousQuantity($inventoryAdjustments){
    if(count($inventoryAdjustments) > 1){
      for($x = 1; $x < count($inventoryAdjustments); $x++){
        $previousQuantity = $inventoryAdjustments[$x - 1]['previous_quantity'] * 1;
        $multiplier = $inventoryAdjustments[$x - 1]['type'] == 1 ? 1 : -1;
        $previousQuantity += ($inventoryAdjustments[$x - 1]['quantity'] * $multiplier);
        $inventoryAdjustments[$x]['previous_quantity'] = $previousQuantity;
        $this->updateInventoryAdjustment($inventoryAdjustments[$x]);
      }
    }
  }
  private function updateInventoryAdjustment($inventoryAdjustment){
    $inventoryAdjustmentDb = new App\InventoryAdjustment();
    $inventoryAdjustmentDb = $inventoryAdjustmentDb->find($inventoryAdjustment['id']);
    $inventoryAdjustmentDb->previous_quantity = $inventoryAdjustment['previous_quantity'];
    $inventoryAdjustmentDb->save();
  }
  private function getUpdatedInventoryadjustments($lastDatetimeUpdate, $storeId){
    $inventoryAdjustmentDB = new App\InventoryAdjustment();
    $allInventoryAdjustments = $inventoryAdjustmentDB->where('store_id', $storeId)->where('created_at', '>=', $lastDatetimeUpdate)->orderBy('created_at', 'asc')->get()->toArray();
    return $allInventoryAdjustments;
  }
  private function insertNewInventoryAdjustments($inventoryAdjustments){
    $inventoryAdjustmentDB = new App\InventoryAdjustment();
    $this->responseGenerator->addDebug('pis', $inventoryAdjustments);
    $result = $inventoryAdjustmentDB->insert($inventoryAdjustments);
    $ids = ($inventoryAdjustmentDB->orderBy('id', 'desc')
      ->join('company_users', "company_users.user_id", "=", "inventory_adjustments.user_id")
      ->where('company_users.company_id', $this->userSession('company_id'))
      ->take(count($inventoryAdjustments))->select('inventory_adjustments.id')->pluck('id'))->toArray();
    $ids = array_reverse($ids);
    return $ids;
  }
  
}
