<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompanyUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('company_users', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('company_id');
        $table->unsignedBigInteger('user_id');
        $table->smallInteger('status')->comment('1 - active, 2 - inactive, 3 - not verified, 4 - confirmed');
        $table->timestamps();
        $table->softDeletes();
      });

      Schema::table('company_users', function (Blueprint $table) {
        $table->foreign('user_id')->references('id')->on('users');
        $table->foreign('company_id')->references('id')->on('companies');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_users');
    }
}
