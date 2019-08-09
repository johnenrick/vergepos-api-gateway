<?php

use Illuminate\Database\Seeder;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class CountryLookUpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('countries')->truncate();
        DB::table('regions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $jsonString = file_get_contents(base_path('resources/json/countr-state-list.json'));

        $countryList = json_decode($jsonString, true);
        $countryData = [];
        $regionData = [];
        $regionID = 1;
        foreach($countryList as $key => $country){
          $countryID = $key + 1;
          $countryData[$key] = ["id" => $countryID, "name" => $country["countryName"], "code" => $country["countryShortCode"]];
          foreach($country['regions'] as $region){
            $regionData[$regionID++] = ["name" => $region["name"], "country_id" => $countryID];
          }
        }
        DB:: table('countries') -> insert($countryData);
        DB:: table('regions') -> insert($regionData);
    }
}
