<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Company extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('companies', function (Blueprint $table) {
          $table->increments('id');
          $table->char('name', 100)->comment('brand name of the company');
          $table->char('code', 50);
          $table->unsignedBigInteger('parent_company_id')->nullable();
          $table->timestamps();
          $table->softDeletes();
      });
      Schema::table('user_roles', function (Blueprint $table) {
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
        Schema::dropIfExists('companies');
    }
}
