<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionComputation extends Model
{
    public function getTableColumns() {
      return $this->getConnection()->getSchemaBuilder()->getColumnListing(str_plural($this->getTable()));
    }
    public function getFormulatedColumn(){
      return [];
    }
    public function getValidationRule(){
      return [];
    }
}
