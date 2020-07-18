<?php

use Illuminate\Database\Seeder;

class MasterListSample extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timeStarted = time();
        $companyId = 39;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('products')->leftJoin('categories', 'categories.id', '=', 'products.category_id')->where('categories.company_id', $companyId)->delete();
        DB::table('categories')->where('company_id', $companyId)->delete();
        $categoryData = [];
        $date = date('Y-m-d', time());
        for($x = 1; $x < 300; $x++){
            $categoryData[] = [
                'description' => 'Category ' . $x,
                'company_id' => $companyId,
                'created_at' => $date,
                'updated_at' => $date
            ];
        }
        DB:: table('categories')->insert($categoryData);
        $categories = DB::table('categories')->where('company_id', $companyId)->get()->toArray();
        $productData = [];
        for($x = 0; $x < count($categories); $x++){
            $maxProduct = rand(0, 500);
            $minCost = rand(0.25, 100000);
            $maxCost = rand($minCost, 200000);
            for($y = 0; $y < $maxProduct; $y++){
                $cost = rand($minCost, $maxCost);
                $productData[] = [
                    'category_id' => $categories[$x]->id,
                    'description' => "Product " . $categories[$x]->id . " - $y",
                    'short_description' => "P " . $categories[$x]->id . " - $y",
                    'barcode' => $categories[$x]->id . $x . $y . rand(1000,2000),
                    'cost' => $cost,
                    'price' => $cost + rand($cost * 0.10, $cost * 3),
                    'is_available' => 1,
                    'created_at' => $date,
                    'updated_at' => $date
                ];
            }
        }
        DB:: table('products')->insert($productData);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        echo 'Categories Created: '. count($categoryData) . ' Products Created: ' . count($productData) . ' Total Time: ' . (time() - $timeStarted) . '\n';
    }
}
