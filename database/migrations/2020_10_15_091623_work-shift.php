<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_shifts', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('User started the shift');
            $table->unsignedBigInteger('closed_by_user_id')->nullable()->comment('User closed the shift');
            $table->unsignedBigInteger('store_terminal_id');
            $table->datetime('end_datetime')->nullable();
            $table->boolean('close_overidden')->nullable();
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
        Schema::dropIfExists('work_shifts');
    }
}
