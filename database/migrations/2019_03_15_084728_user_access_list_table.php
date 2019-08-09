<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAccessListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_access_lists', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedBigInteger('user_id');
          $table->unsignedInteger('service_action_registry_id');
          $table->timestamps();
          $table->softDeletes();
      });
      Schema::table('user_access_lists', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_access_lists');
    }
}
