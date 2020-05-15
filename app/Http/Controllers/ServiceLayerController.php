<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App;
class ServiceLayerController extends Controller
{
    public $contentType = 'text/json';
    private $userTokenData = null;
    public function index($module, $function, Request $request){
      $serviceActionRegistry = $this->getService("$module/$function");
      // dd($serviceActionRegistry);
      if($serviceActionRegistry){
        if($serviceActionRegistry['auth_required'] * 1){ // service needs token
          if($this->isAuthorized($serviceActionRegistry['id'])){
            $resource = $this->requestResource($serviceActionRegistry, $request->all());
            return $this->generateResponse($resource);
          }else{
            return response()->json(['error' => 'Unauthorized', 'token' => auth()->user(), 'token_data' => $this->userTokenData], 401)->header('Content-Type', $this->contentType);;
          }
        }else{ // service dont need token
          $resource = $this->requestResource($serviceActionRegistry, $request->all());
          return $this->generateResponse($resource);
        }
      }else{
        return response()->json(['error' => 'Registry not found!'], 404)->header('Content-Type', $this->contentType);
      }
    }
    public function requestResource($serviceActionRegistry, $param){
      $request = [
        'data' => null, // the data if request is success
        'error' => null, // array of errors
        'debug' => null
      ];
      $param['PAYLOAD'] = $this->user(null);
      $request['DEBUG'] = $this->userTokenData; $this->getSubPermissions($serviceActionRegistry['id']);
      $request['api_link'] = $serviceActionRegistry['base_link'].'/'.$serviceActionRegistry['link'];
      // printR($param);
      try {
        $client = new Client(); //GuzzleHttp\Client
        $result = $client->request('POST', $serviceActionRegistry['base_link'].'/'.$serviceActionRegistry['link'], [
          'json' => $param
        ]);
        $result = json_decode((string)$result->getBody(), true);
        $request['data'] = $result['data'];
        $request['additional_data'] = $result['additional_data'];
        $request['debug'] = $result['debug'];
      } catch (GuzzleException $e) {
        $responseRaw = $e->getResponse();
        if($responseRaw){
          $response = json_encode($responseRaw->getBody());
          if($responseRaw->getStatusCode() == 422){ // validation error
            $response = json_decode((string)$e->getResponse()->getBody(), true);
            $request['error'] = $response['error'];
          }else if($responseRaw->getStatusCode() == 500){
            $request['error'] = [
              "code" => 500,
              "message" => 'Server Error in the Resource',
              "shot" => (string)$e->getResponse()->getBody()
            ];
          }else{
            $request['error'] = [
              "link" => $serviceActionRegistry['base_link'].'/'.$serviceActionRegistry['link'],
              "code" => $responseRaw->getStatusCode(),
              "message" => 'Unknow Error',
              "shot" => (string)$responseRaw->getBody()
            ];
          }
        }else{
          $request['error'] = [
            "link" => $serviceActionRegistry['base_link'].'/'.$serviceActionRegistry['link'],
            "code" => $responseRaw->getStatusCode(),
            "message" => 'Response is null',
            "shot" => (string)$responseRaw->getBody()
          ];
        }
        
        $request['debug'] = isset($response['debug']) ? $response['debug'] : null;
      }
      return $request;
    }
    public function getSubPermissions($serviceID){
      $userID = $this->user();
      return array();
    }
    public function generateResponse($response){
      if($response['error']){
        $httpErrorCode = $response['error']['code'];
        switch($response['error']['code'] * 1){
          case 1:
            $httpErrorCode = 422;
            break;
          case 2:
            $httpErrorCode = 401;
        }
        return response($response, $httpErrorCode)->header('Content-Type', $this->contentType);;
      }else{
        return response($response, 200)->header('Content-Type', $this->contentType);;
      }
    }
    public function isAuthorized($serviceActionRegistryID){
      if($this->user()){
        $user = $this->userTokenData;
        $roleAccessList = [];
        $userAccessList = [];
        $userAccessList = (new App\UserAccessList())->where('user_id', $user['id'])->where('service_action_registry_id', $serviceActionRegistryID)->get()->toArray();
        if($user['role_list'] && count($user['role_list'])){
          $roleAccessList = (new App\RoleAccessList())->whereIn('role_id', $user['role_list'])->where('service_action_registry_id', $serviceActionRegistryID)->get()->toArray();
        }
        if(count($userAccessList) || count($roleAccessList)){
          return true;
        }else{
          return false;
        }
      }else{
        return false;
      }
    }
    public function getService($link){
      $serviceActionRegistry = (new App\ServiceActionRegistry())->select(['service_action_registries.link as link', 'service_action_registries.id as id', 'service_action_registries.auth_required as auth_required', 'service_actions.service_id'])->where('service_action_registries.link', $link)->leftJoin('service_actions', 'service_actions.id', '=', 'service_action_registries.id')->first(); // get the service
      $serviceActionRegistry = $serviceActionRegistry? $serviceActionRegistry->toArray() : null;
      /**
      The services is not joined in the previous table because theoritically, the JOIN is executed first before the WHERE.
      To make retrieval faster, specially of failed, getting the details of the service such as `auth_required` and `base_link`, a separate query is created.
      Please NOTE that this is just a theory. Feel free to change it if you can provide test data.
      */
      if($serviceActionRegistry){
        $serviceAction = (new App\ServiceAction())
        ->where('service_actions.id', $serviceActionRegistry['id'])
        ->join('services', 'services.id', '=', 'service_actions.service_id')
        ->select(['services.link as base_link', 'auth_required'])
        ->first()->toArray();
        $serviceActionRegistry['base_link'] = $serviceAction['base_link'];
      }
      return $serviceActionRegistry;
    }
    public function user($key = "id"){
      if($this->userTokenData == null && auth()->user()){
        $this->userTokenData = auth()->user()->toArray();;
        if(auth()->getPayload()->get('custom')){
          $custom = get_object_vars(auth()->getPayload()->get('custom'));
          $this->userTokenData = array_merge($this->userTokenData, $custom);
          if(isset($this->userTokenData['roles'])){
            $this->userTokenData['role_list'] = collect($this->userTokenData['roles'])->keys()->toArray();
          }else{
            $this->userTokenData['role_list'] = null;
          }

        }
      }
      if($this->userTokenData){
        if($key){
          return $this->userTokenData[$key];
        }else{
          return $this->userTokenData;
        }
      }else{
        return null;
      }
    }
}
