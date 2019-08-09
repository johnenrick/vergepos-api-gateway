<?php

use Illuminate\Database\Seeder;

class ServiceBackUp extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = DB:: table('services')->get()->toArray();
        $serviceActions = DB:: table('service_actions')->get()->toArray();
        $roles = DB:: table('roles')->get()->toArray();
        $roleAccessLists = DB:: table('role_access_lists')->get()->toArray();
        // print_r(json_encode($services));
        echo Storage::disk('local')->put('bu\services.json', json_encode($services));
        echo Storage::disk('local')->put('bu\service_actions.json', json_encode($serviceActions));
        echo Storage::disk('local')->put('bu\roles.json', json_encode($roles));
        echo Storage::disk('local')->put('bu\role_access_lists.json', json_encode($roleAccessLists));
    }
}
