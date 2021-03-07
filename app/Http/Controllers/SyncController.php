<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App;
class SyncController extends GenericController
{
    function __construct(){
        $this->initGenericController();
    }
    public function sync(Request $request){
        $entry = $request->all();
        $validator = Validator::make($entry, [
            'category_latest_datetime' => "nullable|date_format:Y-m-d H:i:s",
            'product_latest_datetime' => "nullable|date_format:Y-m-d H:i:s",
            'discount_latest_datetime' => "nullable|date_format:Y-m-d H:i:s",
            'payment_method_latest_datetime' => "nullable|date_format:Y-m-d H:i:s",
        ]);
        if($validator->fails()){
            $resultObject['fail'] = [
                "code" => 1,
                "message" => $validator->errors()->toArray()
            ];
            $this->responseGenerator->setFail($resultObject['fail']);
        }else{
            $results = [
                'categories' => $this->getCategories(isset($entry['category_latest_datetime']) ? $entry['category_latest_datetime'] : null),
                'products' => $this->getProducts(isset($entry['product_latest_datetime']) ? $entry['product_latest_datetime'] : null),
                'discounts' => $this->getDiscounts(isset($entry['discount_latest_datetime']) ? $entry['discount_latest_datetime'] : null),
                'payment_methods' => $this->getPaymentMethods(isset($entry['payment_method_latest_datetime']) ? $entry['payment_method_latest_datetime'] : null),
            ];
            $this->responseGenerator->setSuccess($results); // $this->userSession('company_id')
        }
        return $this->responseGenerator->generate();
    }
    private function getCategories($latestDate){
        $categoryModel = (new App\Category())->where('company_id', $this->userSession('company_id'));
        if($latestDate){
            $categoryModel->where('updated_at', '>=', $latestDate);
        }
        return $categoryModel->withTrashed()->get()->toArray();
    }
    private function getProducts($latestDate){
        $productModel = (new App\Product())->select('products.*')->join('categories', 'categories.id', '=', 'products.category_id')->where('categories.company_id', $this->userSession('company_id'));
        if($latestDate){
            $productModel->where('products.updated_at', '>=', $latestDate);
        }
        return $productModel->withTrashed()->get()->toArray();
    }
    private function getDiscounts($latestDate){
        $discountModel = (new App\Discount())->where('company_id', $this->userSession('company_id'));
        if($latestDate){
            $discountModel->where('updated_at', '>=', $latestDate);
        }
        return $discountModel->withTrashed()->get()->toArray();
    }
    private function getPaymentMethods($latestDate){
        $paymentMethodModel = (new App\PaymentMethod());
        if($latestDate){
            $paymentMethodModel->where('updated_at', '>=', $latestDate);
        }
        return $paymentMethodModel->withTrashed()->get()->toArray();
    }
}
