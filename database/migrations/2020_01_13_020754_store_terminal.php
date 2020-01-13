<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoreTerminal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store_terminals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('store_id');
            $table->char('description', 30);
            $table->char('serial_number', 50)->nullable();
            $table->char('permit_number')->nullable();
            $table->char('accreditation_number', 30)->nullable();
            $table->char('machine_identification_number', 20)->nullable();
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
