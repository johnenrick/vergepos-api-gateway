<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkShiftCashReading extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_shift_cash_readings', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('work_shift_id');
            $table->tinyInteger('type')->comment('1 - beginning, 2 - cash in adjustment, 3 - cash out adjustment, 4 - closing');
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->integer('bill_1_cent')->default(0);
            $table->integer('bill_5_cent')->default(0);
            $table->integer('bill_10_cent')->default(0);
            $table->integer('bill_25_cent')->default(0);
            $table->integer('bill_1_peso')->default(0);
            $table->integer('bill_5_peso')->default(0);
            $table->integer('bill_10_peso')->default(0);
            $table->integer('bill_20_peso')->default(0);
            $table->integer('bill_50_peso')->default(0);
            $table->integer('bill_100_peso')->default(0);
            $table->integer('bill_200_peso')->default(0);
            $table->integer('bill_500_peso')->default(0);
            $table->integer('bill_1000_peso')->default(0);
            $table->double('discrepancy')->default(0);
            $table->double('other_payments')->default(0);
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('work_shift_cash_readings');
    }
}
