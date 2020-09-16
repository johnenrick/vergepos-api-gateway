<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_customers', function(Blueprint $table){
            $table->increments('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('transaction_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('transaction_customers', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('transaction_id')->references('id')->on('transactions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_customers');
    }
}
