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
  for($x = 0; $x < count($customAPIResource); $x++){
    $customAPI = $customAPIResource[$x];
    $splitAPI = explode('/', $customAPIResource[$x]);
    $pascalCase = preg_replace_callback("/(?:^|-)([a-z])/", function($matches) {
      return strtoupper($matches[1]);
    }, $splitAPI[0]) . 'Controller';
    $functionCamelCase = str_replace('-', '', lcfirst(ucwords($splitAPI[1], '-')));
    if($method == 'post'){
      Route::post($customAPI, $pascalCase."@" . $functionCamelCase);
    }else{
      Route::get($customAPI, $pascalCase."@". $functionCamelCase);
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
  'customer',
  'store-terminal',
  'store'
];
$customAPIResources = [
  'transaction/sync',
  'transaction-number/sync',
  'user/request-change-password',
  'user/confirm-change-password',
];
$api_resource($apiResource);
$custom_api($customAPIResources);