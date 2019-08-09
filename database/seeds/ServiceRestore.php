<?php

use Illuminate\Database\Seeder;

class ServiceRestore extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      DB::table('services')->truncate();
      DB::table('service_actions')->truncate();
      DB::table('roles')->truncate();
      DB::table('role_access_lists')->truncate();
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');
      $baseUrl = env('APP_SERVER_LOCATION');
      $services = json_decode(str_replace('http:\/\/localhost\/intraactiveops\/api\/api-gateway', $baseUrl, Storage::disk('local')->get('bu\services.json')), true);
      $serviceAction = json_decode(Storage::disk('local')->get('bu\service_actions.json'), true);
      $roles = json_decode(Storage::disk('local')->get('bu\roles.json'), true);
      $roleAccessList = json_decode(Storage::disk('local')->get('bu\role_access_lists.json'), true);
      DB:: table('services') -> insert($services);
      DB:: table('service_actions') -> insert($serviceAction);
      DB:: table('roles') -> insert($roles);
      DB:: table('role_access_lists') -> insert($roleAccessList);
    }
}
