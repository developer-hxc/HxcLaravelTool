<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

$api_func = function ($api){
    //默认命名空间指向到App\Http\Controllers\Api下
    $api->group(['middleware' => 'auth:api'],function($api){//需要jwt验证

    });
    $api->group([],function($api){//无需jwt验证

    });
};

//由Dingo来接管api路由
$api = app('Dingo\Api\Routing\Router');
$api->version('v1',['namespace'=>'App\Http\Controllers\Api\V1','middleware' => 'api.throttle', 'limit' => 60, 'expires' => 1],$api_func);







