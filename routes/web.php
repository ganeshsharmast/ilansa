<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('site_down/index');
});

//Route::group([ 'middleware' => 'rental'], function(){
    Route::post('/register', 'UserController@register');
    Route::post('/request/{equipment_url}', 'PagesController@request');
    Route::post('/request/create', 'RequestsController@create');
    Route::post('/request/accept', 'RequestsController@accept');
//   });