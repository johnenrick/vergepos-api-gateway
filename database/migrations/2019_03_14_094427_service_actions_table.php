<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('service_actions', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('service_id');
          $table->string('description');
          $table->string('link')->comment('endpoint link. Domain is not included');
          $table->boolean('auth_required')->default(1);
          $table->timestamps();
          $table->softDeletes();

      });
      Schema::table('service_actions', function (Blueprint $table) {
        $table->foreign('service_id')->references('id')->on('services');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_actions');
    }
}
