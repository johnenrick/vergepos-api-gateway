<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_roles', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedBigInteger('user_id');
          $table->unsignedInteger('role_id');
          $table->unsignedInteger('company_id');
          $table->timestamps();
          $table->softDeletes();
      });
      Schema::table('user_roles', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_roles');
    }
}
