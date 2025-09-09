<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
      "inventory_adjustments.*.client_uuid" => "nullable|string",
      "inventory_adjustments.*.updated_at" => "required|date",
    ]);

    if($validator->fails()){
      $this->responseGenerator->setFail([
        "code" => 1,
        "message" => $validator->errors()->toArray()
      ]);
    }else{
      foreach ($entries['inventory_adjustments'] as $entryKey => $entry) {
        // sanitize
        $entries['inventory_adjustments'][$entryKey]['remarks'] = strip_tags($entry['remarks'] ?? '');

        // set store
        $entries['inventory_adjustments'][$entryKey]['store_id'] = $entries['store_id'];

        // generate deterministic fingerprint used as idempotency key
        // use fields that uniquely identify the adjustment payload (store, product, user, type, quantity, previous_quantity, created_at)
        $fingerprintSource = sprintf(
            '%s|%s|%s|%s|%s|%s|%s',
            $entries['store_id'],
            $entry['product_id'] ?? '',
            $entry['user_id'] ?? '',
            $entry['type'] ?? '',
            (string)($entry['quantity'] ?? ''),
            (string)($entry['previous_quantity'] ?? ''),
            $entry['created_at'] ?? ''
        );
        // sha1 gives a deterministic hex string; save into client_uuid
        $entries['inventory_adjustments'][$entryKey]['client_uuid'] = hash('sha1', $fingerprintSource);

        // force no id fields coming from client
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
    // $inventoryAdjustments: array of associative arrays; each must contain client_uuid (see batchCreate)
    if (empty($inventoryAdjustments)) {
      return [];
    }

    $insertedIds = [];

    DB::transaction(function() use (&$insertedIds, $inventoryAdjustments) {
      // Use insertOrIgnore to skip duplicates based on unique client_uuid
      DB::table('inventory_adjustments')->insertOrIgnore($inventoryAdjustments);

      // collect client_uuids in original order
      $uuids = array_map(function($row){
        return isset($row['client_uuid']) ? $row['client_uuid'] : null;
      }, $inventoryAdjustments);

      // fetch IDs for those client_uuids (mapping client_uuid => id)
      $rows = DB::table('inventory_adjustments')
        ->whereIn('client_uuid', array_filter($uuids))
        ->pluck('id', 'client_uuid')
        ->toArray();

      // preserve original input order: build array of ids (or false if not found)
      foreach ($uuids as $uuid) {
        $insertedIds[] = isset($rows[$uuid]) ? (int)$rows[$uuid] : false;
      }
    });

    return $insertedIds;
  }
  
}
