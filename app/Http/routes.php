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
Route::group(['prefix' => 'api/v1/vendor/'], function () {
    Route::post('create',array('uses' => 'UsersController@storeVendor'));
    Route::put('update-first-login',array('uses' => 'Vendor\VendorsController@updateFirstLoginFlag'));
    Route::get('my-profile',array('uses' => 'Vendor\VendorsController@getProfile'));
    Route::put('my-profile',array('uses' => 'Vendor\VendorsController@updateProfile'));
    Route::get('billing-info',array('uses' => 'Vendor\VendorsController@getBillingInformation'));
    Route::put('billing-info',array('uses' => 'Vendor\VendorsController@updateBillingInformation'));
    Route::get('bank-info',array('uses' => 'Vendor\VendorsController@getBankDetails'));
    Route::put('bank-info',array('uses' => 'Vendor\VendorsController@updateBankDetails'));
    Route::put('images',array('uses' => 'Vendor\VendorsController@updateImages'));
    Route::post('add-new-facility',array('uses' => 'Vendor\VendorsController@createFacility'));
});
//Route::controllers([
//    'auth' => 'Auth\AuthController',
//    'password' => 'Auth\PasswordController',
//]);

/* For Customer Only */
Route::group(['prefix' => 'api/v1/customer/'], function () {
    Route::post('create/',array('uses' => 'UsersController@storeCustomer'));
});

/* Common to All Users */
Route::group(['prefix' => 'api/v1/user/'], function () {
    Route::get('confirm/{token}',array('uses' => 'UsersController@confirm'));
    Route::post('auth',array('uses' => 'Auth\AuthController@authenticate'));
    Route::get('logout',array('uses' => 'Auth\AuthController@logout'));
    Route::get('get-root-category',array('uses' => 'UsersController@getRootCategory'));
    Route::get('get-sub-category/{id}',array('uses' => 'UsersController@getSubCategory'));
});

Route::get('temp',array('uses' => 'Vendor\VendorsController@index'));