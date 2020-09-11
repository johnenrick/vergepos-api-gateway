<?php

use Illuminate\Database\Seeder;

class AddPaymentMethods extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = [
            [ "id"=> 1, "description" => "Cash" ],
            [ "id"=> 2, "description" => "Bank" ],
            [ "id"=> 3, "description" => "GCash" ],
            [ "id"=> 4, "description" => "Paymaya" ],
            [ "id"=> 5, "description" => "Coins.ph" ],
            [ "id"=> 6, "description" => "PayPal" ],
        ];
        $paymentMethodIds = collect($paymentMethods)->pluck('id');
        $existingPaymentMethodCollections = collect(DB:: table('payment_methods')->whereIn("id", $paymentMethodIds)->get()->toArray()); 
        $toInsert = [];
        foreach($paymentMethods as $paymentMethod){
            $alreadyExists = $existingPaymentMethodCollections->where("id", $paymentMethod["id"])->count();
            if(!$alreadyExists){
                $toInsert[] = $paymentMethod;
            }
        }
        DB:: table('payment_methods')->insert($toInsert);
    }
}
