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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['cors']], function () {

    Route::post('logout', 'Api\LoginController@logout')->name('logout_jwt');
    Route::post('login', 'Api\LoginController@login')->name('login_jwt');
    Route::post('refresh', 'Api\LoginController@refresh')->name('refresh_jwt');
    Route::get('me', 'Api\LoginController@me')->name('me_jwt');
});

Route::group(['middleware' => ['jwt.authenticate', 'cors']], function () {
    Route::get('/projects', 'Api\ProjectsController@getAllProjects');
    Route::get('/pages', 'Api\PagesController@getAllPages');
});


