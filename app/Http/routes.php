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
    return view('STANDARD/index');
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
    Route::post('images',array('uses' => 'Vendor\VendorsController@addImages'));
    Route::get('images',array('uses' => 'Vendor\VendorsController@getImages'));
    Route::get('images/{id}',array('uses' => 'Vendor\VendorsController@deleteImage'));
    Route::post('facility',array('uses' => 'Vendor\VendorsController@createFacility'));//modify
    Route::get('facility',array('uses' => 'Vendor\VendorsController@getFacility')); //modify
    Route::get('facility/{id}',array('uses' => 'Vendor\VendorsController@getFacilityById'));//modify
    Route::put('facility/{id}',array('uses' => 'Vendor\VendorsController@updateFacility'));//modify
    //NEW
    Route::get('package-types',array('uses' => 'Vendor\SessionPackageController@types'));
    Route::post('package',array('uses' => 'Vendor\SessionPackageController@createPackage'));
    Route::post('session',array('uses' => 'Vendor\SessionPackageController@createSession'));
    Route::post('session-duration',array('uses' => 'Vendor\SessionPackageController@updateDuration'));
    Route::get('facility-detail/{id}',array('uses' => 'Vendor\VendorsController@getFacilityDetailInformation'));//modify
    Route::get('duration',array('uses' => 'Vendor\SessionPackageController@getDuration'));
});
//Route::controllers([
//    'auth' => 'Auth\AuthController',
//    'password' => 'Auth\PasswordController',
//]);
/* For Customer Only */
Route::group(['prefix' => 'api/v1/customer/'], function () {
    Route::post('create/',array('uses' => 'UsersController@storeCustomer'));
    //New
    Route::put('profile',array('uses' => 'Customer\CustomersController@updateProfileInformation'));
    Route::get('profile',array('uses' => 'Customer\CustomersController@getProfileInformation'));
});

/* For Superadmin Only */
Route::group(['prefix' => 'api/v1/superadmin/'], function () {

});

/* Common to All Users */
Route::group(['prefix' => 'api/v1/user/'], function () {
    Route::get('confirm/{token}',array('uses' => 'UsersController@confirm'));
    Route::post('auth',array('uses' => 'Auth\AuthController@authenticate'));//modify
    Route::get('logout',array('uses' => 'Auth\AuthController@logout'));
    Route::get('get-root-category',array('uses' => 'UsersController@getRootCategory'));
    Route::get('get-sub-category/{id}',array('uses' => 'UsersController@getSubCategory'));
    Route::get('areas',array('uses' => 'UsersController@getArea'));
    //New
    Route::put('change-password',array('uses' => 'Auth\PasswordController@change'));
});


Route::get('temp',array('uses' => 'Vendor\VendorsController@index'));
