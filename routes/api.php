<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('clear_cache', function () {

    \Artisan::call('cache:clear');
    dd("Cache is cleared");

});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    

    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/forgetPassword', [UserController::class, 'forgetPassword']);
    Route::post('/resetPassword', [UserController::class, 'resetPassword']);
    Route::post('/changePassword', [UserController::class, 'changePassword']);
    Route::post('/changeLanguage', [UserController::class, 'changeLanguage']);  
    Route::post('/verifyEmailPhone', [UserController::class, 'verifyEmailPhone']);
    Route::post('/getProfileDetails', [UserController::class, 'getProfileDetails']);
    Route::post('/updateProfileDetails', [UserController::class, 'updateProfileDetails']);  
    Route::post('/getServiceList', [ServiceController::class, 'getServiceList']);     
    Route::post('/getSubServiceList', [ServiceController::class, 'getSubServiceList']);  
    Route::post('/searchServiceList', [ServiceController::class, 'searchServiceList']);
    Route::post('/searchSubServiceList', [ServiceController::class, 'searchSubServiceList']);
    Route::post('/getServiceProductList', [ServiceController::class, 'getServiceProductList']);    

});


Route::post('/request/{equipment_url}', 'PagesController@request');
Route::post('/request/create', 'RequestsController@create');
Route::post('/request/accept', 'RequestsController@accept');