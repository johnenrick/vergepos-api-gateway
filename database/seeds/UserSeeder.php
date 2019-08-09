<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      DB:: table('users')->truncate();
      DB:: table('company_users')->truncate();
      DB:: table('user_roles')->truncate();
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');
      $users = [
        ["id" => 1, "username" => 'dev', "email" => "plenosjohn@yahoo.com", "password" => Hash::make("dev"), "user_type_id" => "1", "status" => 1],
      ];
      $companyUsers = [
        ["user_id" => 1, 'company_id' => 1, 'status' => 1]
      ];
      $userRoles = [
        ["user_id" => 1, "role_id" => 1, 'company_id' => 1]
      ];
      DB:: table('users') -> insert($users);
      DB:: table('company_users') -> insert($companyUsers);
      DB:: table('user_roles') -> insert($userRoles);
    }
}
