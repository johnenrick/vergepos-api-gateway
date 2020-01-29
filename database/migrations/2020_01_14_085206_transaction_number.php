<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionNumber extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('transaction_numbers', function(Blueprint $table){
        $table->bigIncrements('id');
        $table->unsignedBigInteger('number'); // NEED TO FIX
        $table->tinyInteger('operation')->comment('1 - transaction, 2 - void, 3 - reprint');
        $table->unsignedBigInteger('user_id')->comment('User who made the transaction');
        $table->unsignedBigInteger('store_terminal_id')->nullable();
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
        Schema::dropIfExists('transaction_numbers');
    }
}
