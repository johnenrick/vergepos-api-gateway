<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('transaction_products', function(Blueprint $table){
        $table->bigIncrements('id');
        $table->unsignedBigInteger('transaction_id');
        $table->unsignedBigInteger('product_id');
        $table->float('quantity');
        $table->double('cost');
        $table->double('vat_sales');
        $table->double('vat_exempt_sales');
        $table->double('vat_zero_rated_sales');
        $table->double('vat_amount');
        $table->double('discount_amount');
        $table->timestamps();
        $table->softDeletes();
      });
      Schema::table('transaction_products', function (Blueprint $table) {
        $table->foreign('transaction_id')->references('id')->on('transactions');
        $table->foreign('product_id')->references('id')->on('products');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_products');
    }
}
