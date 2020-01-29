<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserBasicInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_basic_informations', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedBigInteger('user_id');
          $table->char('first_name', 30);
          $table->char('middle_name', 30)->nullable();
          $table->char('last_name', 30);
          $table->timestamp('birthdate')->nullable();
          $table->timestamps();
          $table->softDeletes();
      });
      Schema::table('user_basic_informations', function (Blueprint $table) {
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
        Schema::dropIfExists('user_basic_informations');
    }
}
