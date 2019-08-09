<?php

use Illuminate\Database\Seeder;

class DefaultServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB:: table('services')->truncate();
        DB:: table('service_actions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $baseUrl = (true) ? "localhost/vergepos/api/vergepos-api-gateway/public/" : "http://ec2-54-161-197-150.compute-1.amazonaws.com";
        $services = [
          ["id" => 1, "description" => "User", "link" => $baseUrl . "api"],
          ["id" => 2, "description" => "Service", "link" => $baseUrl . "api"],
          ["id" => 3, "description" => "Auth", "link" => $baseUrl . "api"],
          ["id" => 4, "description" => "Role", "link" => $baseUrl . "api"],
          ["id" => 5, "description" => "Service Action", "link" => $baseUrl . "api"],
          ["id" => 6, "description" => "Company", "link" => $baseUrl . "api"]
        ];
        $actions = [
          // USER
          ["id" => 1, "service_id" => 1, "description" => "Create User", "link" => "/create", "auth_required" => 1],
          ["id" => 2, "service_id" => 1, "description" => "Retrieve User", "link" => "/retrieve", "auth_required" => 1],
          ["id" => 3, "service_id" => 1, "description" => "Update User", "link" => "/update", "auth_required" => 1],
          ["id" => 4, "service_id" => 1, "description" => "Delete User", "link" => "/delete", "auth_required" => 1],
          // SERVICE
          ["id" => 5, "service_id" => 2, "description" => "Create Service", "link" => "/create", "auth_required" => 1],
          ["id" => 6, "service_id" => 2, "description" => "Retrieve Service", "link" => "/retrieve", "auth_required" => 1],
          ["id" => 7, "service_id" => 2, "description" => "Update Service", "link" => "/update", "auth_required" => 1],
          ["id" => 8, "service_id" => 2, "description" => "Delete Service", "link" => "/delete", "auth_required" => 1],
          // Auth
          ["id" => 9, "service_id" => 3, "description" => "Log In", "link" => "/login", "auth_required" => 0],
          ["id" => 10, "service_id" => 3, "description" => "User Details", "link" => "/user", "auth_required" => 0],
          ["id" => 11, "service_id" => 3, "description" => "Refresh", "link" => "/refresh", "auth_required" => 0],
          ["id" => 12, "service_id" => 3, "description" => "Log out", "link" => "/logout", "auth_required" => 0],
          // ROle
          ["id" => 13, "service_id" => 4, "description" => "Create Role", "link" => "/create", "auth_required" => 1],
          ["id" => 14, "service_id" => 4, "description" => "Retrieve Role", "link" => "/retrieve", "auth_required" => 1],
          ["id" => 15, "service_id" => 4, "description" => "Update Role", "link" => "/update", "auth_required" => 1],
          ["id" => 16, "service_id" => 4, "description" => "Delete Role", "link" => "/delete", "auth_required" => 1],
          // Service Action
          ["id" => 17, "service_id" => 5, "description" => "Create Service Action", "link" => "/create", "auth_required" => 1],
          ["id" => 18, "service_id" => 5, "description" => "Retrieve Service Action", "link" => "/retrieve", "auth_required" => 1],
          ["id" => 19, "service_id" => 5, "description" => "Update Service Action", "link" => "/update", "auth_required" => 1],
          ["id" => 20, "service_id" => 5, "description" => "Delete Service Action", "link" => "/delete", "auth_required" => 1],
          // Company
          ["id" => 21, "service_id" => 6, "description" => "Create Company", "link" => "/create", "auth_required" => 1],
          ["id" => 22, "service_id" => 6, "description" => "Retrieve Company", "link" => "/retrieve", "auth_required" => 1],
          ["id" => 23, "service_id" => 6, "description" => "Update Company", "link" => "/update", "auth_required" => 1],
          ["id" => 24, "service_id" => 6, "description" => "Delete Company", "link" => "/delete", "auth_required" => 1]
        ];
        DB::table('services') -> insert($services);
        DB::table('service_actions') -> insert($actions);
    }
}
