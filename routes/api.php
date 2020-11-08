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

    Route::post('register', 'Api\LoginController@register')->name('register_jwt');

    Route::post('logout', 'Api\LoginController@logout')->name('logout_jwt');
    Route::post('login', 'Api\LoginController@login')->name('login_jwt');
    Route::post('refresh', 'Api\LoginController@refresh')->name('refresh_jwt');
    Route::get('me', 'Api\LoginController@me')->name('me_jwt');

    Route::post('forgot-password', 'Api\LoginController@forgotPassword')->middleware('throttle:10,1')->name('forgot_password');
    Route::post('restore-password', 'Api\LoginController@restorePassword')->name('restore_password');

});

Route::group(['middleware' => ['jwt.authenticate', 'cors']], function () {
    Route::get('/projects', 'Api\ProjectsController@getAllProjects');
    Route::get('/project/{id}', 'Api\ProjectsController@project');
    Route::get('/pages', 'Api\PagesController@getAllPages');
});


