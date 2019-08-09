<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('categories', function(Blueprint $table){
        $table->increments('id');
        $table->unsignedInteger('company_id');
        $table->unsignedInteger('category_id')->default(0)->comment('parent category');
        $table->char('description', 50);

        $table->timestamps();
        $table->softDeletes();
      });
      Schema::table('categories', function(Blueprint $table){
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
        Schema::dropIfExists('categories');
    }
}
