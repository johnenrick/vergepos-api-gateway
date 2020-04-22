<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePasswordRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_password_requests', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('email');
            $table->char('confirmation_code', 7);
            $table->tinyInteger('status')->default(0)->comment('0 - unused, 1 - used, 2 - invalidated');
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
        Schema::dropIfExists('change_password_requests');
    }
}
