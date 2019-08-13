<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CompanyDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('company_details', function(Blueprint $table){
        $table->increments('id');
        $table->unsignedInteger('company_id');
        $table->text('nature')->comment('Nature of Business');
        $table->text('address');
        $table->text('contact_number');
        $table->timestamps();
        $table->softDeletes();
      });
      Schema::table('company_details', function (Blueprint $table) {
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
      Schema::dropIfExists('company_details');
    }
}
