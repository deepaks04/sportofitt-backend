<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
use App\Http\Controllers\Auth\AuthController;
Route::get('/', function () {
    return view('welcome');
});
/* For Vendor Only */
Route::group(['prefix' => 'api/vendor/'], function () {
    Route::post('create',array('uses' => 'UsersController@storeVendor'));
});
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
/* Common to All Users */
Route::group(['prefix' => 'api/user/'], function () {
    Route::get('confirm/{token}',array('uses' => 'UsersController@confirm'));
    Route::post('authenticate',array('uses' => 'Auth\AuthController@authenticate'));
    Route::get('logout',array('uses' => 'Auth\AuthController@logout'));
});