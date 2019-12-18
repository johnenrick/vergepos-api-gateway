<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Terminal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('terminals', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->unsignedInteger('company_id');
          $table->char('description', 30);
          $table->char('serial_number', 50)->nullable();
          $table->timestamp('permit_number')->nullable();
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
        //
    }
}
