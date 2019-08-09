<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RoleAccessListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('role_access_lists', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('role_id');
          $table->unsignedInteger('service_action_registry_id');
          $table->timestamps();
          $table->softDeletes();
      });
      Schema::table('role_access_lists', function (Blueprint $table) {
        $table->foreign('role_id')->references('id')->on('roles');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_access_lists');
    }
}
