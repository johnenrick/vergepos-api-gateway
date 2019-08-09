<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('discounts', function(Blueprint $table){
        $table->increments('id');
        $table->unsignedInteger('company_id');
        $table->tinyInteger('type')->comment('1 - percentage on receipt, 2 - percentage on items,  3 - exact value on receipt, 4 - exact value on  items');
        $table->char('description', 50);
        $table->float('value');
        $table->boolean('is_vat_exempt');
        $table->boolean('require_identification_card')->default(0);
        $table->timestamps();
        $table->softDeletes();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
