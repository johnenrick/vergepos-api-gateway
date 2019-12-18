<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api_resource = function($apiResource){
  $apiResources = (is_array($apiResource))? $apiResource : [$apiResource];
  foreach($apiResources as $apiResourceValue){
    $pascalCase = preg_replace_callback("/(?:^|-)([a-z])/", function($matches) {
        return strtoupper($matches[1]);
    }, $apiResourceValue) . 'Controller';
    Route::get($apiResourceValue."/",$pascalCase."@index");
    Route::get($apiResourceValue."/test",$pascalCase."@test");
    Route::post($apiResourceValue."/create",$pascalCase."@create");
    Route::post($apiResourceValue."/retrieve",$pascalCase."@retrieve");
    Route::post($apiResourceValue."/update",$pascalCase."@update");
    Route::post($apiResourceValue."/delete",$pascalCase."@delete");
  }
};
$custom_api = function($customAPIResource, $method = 'post'){
  // print_r($customAPI);
  for($x = 0; $x < count($customAPIResource); $x++){
    $customAPI = $customAPIResource[$x];
    $splitAPI = explode('/', $customAPIResource[$x]);
    $pascalCase = preg_replace_callback("/(?:^|-)([a-z])/", function($matches) {
      return strtoupper($matches[1]);
    }, $splitAPI[0]) . 'Controller';
    if($method == 'post'){
      Route::post($customAPI, $pascalCase."@".$splitAPI[1]);
    }else{
      Route::get($customAPI, $pascalCase."@".$splitAPI[1]);
    }
  }
};

Route::get('/', function(){
  echo str_plural('registry');
  echo 'API GET';
});


$apiResource = [
  'user',
  'role',
  'user-role',
  'user-access-list',
  'role-access-list',
  'service',
  'service-action',
  'company',
  'category',
  'product',
  'discount',
  'transaction-number',
  'transaction',
  'customer'
];
$customAPIResources = [
];
$api_resource($apiResource);
$custom_api($customAPIResources);