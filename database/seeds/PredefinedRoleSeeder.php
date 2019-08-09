<?php

use Illuminate\Database\Seeder;

class PredefinedRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::table('role_access_lists')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $roles = [
          ['id' => 1, 'description' => 'superdoer', 'is_predefined' => 1],
          ['id' => 2, 'description' => 'developer', 'is_predefined' => 1],
          ['id' => 3, 'description' => 'analyst', 'is_predefined' => 1],
          ['id' => 50, 'description' => 'demo_user', 'is_predefined' => 1],
          ['id' => 100, 'description' => 'company_admin', 'is_predefined' => 1]
        ];
        $roleAccessList = [
          ["role_id" => 1, "service_action_registry_id" => 1],
          ["role_id" => 1, "service_action_registry_id" => 2],
          ["role_id" => 1, "service_action_registry_id" => 3],
          ["role_id" => 1, "service_action_registry_id" => 4],
          ["role_id" => 1, "service_action_registry_id" => 5],
          ["role_id" => 1, "service_action_registry_id" => 6],
          ["role_id" => 1, "service_action_registry_id" => 7],
          ["role_id" => 1, "service_action_registry_id" => 8],
          ["role_id" => 1, "service_action_registry_id" => 9],
          ["role_id" => 1, "service_action_registry_id" => 10],
          ["role_id" => 1, "service_action_registry_id" => 11],
          ["role_id" => 1, "service_action_registry_id" => 12],
          ["role_id" => 1, "service_action_registry_id" => 13],
          ["role_id" => 1, "service_action_registry_id" => 14],
          ["role_id" => 1, "service_action_registry_id" => 15],
          ["role_id" => 1, "service_action_registry_id" => 16],
          ["role_id" => 1, "service_action_registry_id" => 17],
          ["role_id" => 1, "service_action_registry_id" => 18],
          ["role_id" => 1, "service_action_registry_id" => 19],
          ["role_id" => 1, "service_action_registry_id" => 20],
          ["role_id" => 1, "service_action_registry_id" => 21],
          ["role_id" => 1, "service_action_registry_id" => 22],
          ["role_id" => 1, "service_action_registry_id" => 23],
          ["role_id" => 1, "service_action_registry_id" => 24]
        ];
        DB:: table('roles') -> insert($roles);
        DB:: table('role_access_lists') -> insert($roleAccessList);
    }
}
