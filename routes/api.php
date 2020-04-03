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
Route::get('/', function (Request $request) {
    return 'Hello';
});



Route::group(['namespace' => 'Api'], function () {
    Route::post('auth/login', [ 'as' => 'login', 'uses' => 'AuthController@login']);

    Route::group(['middleware' => ['auth.api']], function() {
        Route::get('/users', function (Request $request) {
            return $request->user();
        });
    });
});
