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
        Route::get('/me', function (Request $request) {
            return $request->user();
        });

        Route::post('auth/logout', 'AuthController@logout');

        Route::get('/stats', 'StatsController@stats');
        Route::get('/instructors-stats', 'StatsController@instructorsStats');

        Route::resource('instructors', 'InstructorController', [
            'only' => [
                'index'
            ]
        ]);

        Route::resource('clients', 'ClientController', [
            'only' => [
                'index'
            ]
        ]);
        Route::get('lessons/{lesson}/prepare-pay','LessonController@preparePay');
        Route::post('lessons/pay','LessonController@pay');

        Route::resource('lessons', 'LessonController', [
            'only' => [
                'index', 'store', 'update', 'destroy'
            ]
        ]);

        Route::post('attendance/bulk','AttendanceController@bulkStore');
        Route::delete('attendance/bulk','AttendanceController@bulkDestroy');
        Route::resource('attendance', 'AttendanceController', [
            'only' => [
                'index', 'store', 'update', 'destroy'
            ]
        ]);


    });
});
