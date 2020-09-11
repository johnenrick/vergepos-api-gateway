<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_methods', function(Blueprint $table){
            $table->increments('id');
            $table->char('description', 50);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('transaction_payments', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedInteger('payment_method_id');
            $table->double('amount');
            $table->char('remarks', 60);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
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
        Schema::dropIfExists('transaction_payments');
        Schema::dropIfExists('payment_methods');
    }
}
