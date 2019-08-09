<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(ServiceRestore::class);
        $this->call(PredefinedCompanySeeder::class);
        $this->call(DefaultServiceSeeder::class);
        $this->call(PredefinedRoleSeeder::class);
        $this->call(UserSeeder::class);
    }
}
