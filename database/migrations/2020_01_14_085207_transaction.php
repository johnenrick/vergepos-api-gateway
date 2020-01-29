<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Transaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('transactions', function(Blueprint $table){
        $table->bigIncrements('id');
        $table->unsignedBigInteger('transaction_number_id');
        $table->unsignedBigInteger('customer_id')->nullable();
        $table->tinyInteger('status')->comment('1 - transaction, 2 - void, 3 - reprint');
        $table->double('cash_tendered');
        $table->double('cash_amount_paid');
        $table->text('discount_remarks')->nullable();
        $table->timestamps();
        $table->softDeletes();
      });
      Schema::table('transactions', function (Blueprint $table) {
        $table->foreign('transaction_number_id')->references('id')->on('transaction_numbers');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
