<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServiceActionRegistryView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW service_action_registries AS select service_actions.id, concat(REPLACE(LOWER(services.description), ' ', '-'), service_actions.link) as link, service_actions.auth_required as auth_required from service_actions left join services on services.id = service_actions.service_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_action_registries');
    }
}
