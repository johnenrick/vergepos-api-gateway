<?php

use Illuminate\Database\Seeder;

class PredefinedCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      DB::table('companies')->truncate();
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');
      $roles = [
        ['id' => 1, 'name' => 'VergePOS', 'code' => 'VPOS'],
      ];
      DB:: table('companies') -> insert($roles);
    }
}
