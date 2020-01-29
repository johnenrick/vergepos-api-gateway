<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('products', function(Blueprint $table){
        $table->bigIncrements('id');
        $table->unsignedInteger('category_id');
        $table->char('description', 50);
        $table->char('short_description', 20)->nullable();
        $table->float('cost');
        $table->float('price');
        $table->boolean('is_available')->default(1);
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
        Schema::dropIfExists('products');
    }
}
