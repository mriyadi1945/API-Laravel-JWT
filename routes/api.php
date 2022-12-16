<?php

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

use Illuminate\Support\Facades\Auth;

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
try 
{
    //Checking Database Connection 
    DB::connection()->getPdo();
    
    Route::group([
        'prefix' => 'v1'
    ],function () {
        if (Request::isMethod('post')) {
            Route::post('register', [AuthController::class, 'register']);
            Route::post('login', [AuthController::class, 'login']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
        }
    });
}
catch (Exception $e) 
{
    die("<span style='font-size: 30px; font-weight: bold'>No Establishing Database Connection</span>");
}