<?php

/*
 * |--------------------------------------------------------------------------
 * | Application Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register all of the routes for an application.
 * | It's a breeze. Simply tell Laravel the URIs it should respond to
 * | and give it the controller to call when that URI is requested.
 * |
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
    Route::post('facility',array('uses' => 'Vendor\VendorsController@createFacility'));
    Route::get('facility',array('uses' => 'Vendor\VendorsController@getFacility'));
    Route::get('facility/{id}',array('uses' => 'Vendor\VendorsController@getFacilityById'));
    Route::put('facility/{id}',array('uses' => 'Vendor\VendorsController@updateFacility'));
    //NEW
    Route::get('package-types',array('uses' => 'Vendor\SessionPackageController@types'));
    Route::post('package',array('uses' => 'Vendor\SessionPackageController@createPackage'));
    Route::put('package/{id}',array('uses' => 'Vendor\SessionPackageController@updatePackage'));
    Route::get('package/{id}',array('uses' => 'Vendor\SessionPackageController@getPackage'));//New
    Route::get('delete-package/{id}',array('uses' => 'Vendor\SessionPackageController@deletePackage'));//New
    Route::post('opening-time',array('uses' => 'Vendor\SessionPackageController@createOpeningTime'));
    Route::put('opening-time/{id}',array('uses' => 'Vendor\SessionPackageController@updateOpeningTime'));

    Route::get('opening-time/{id}',array('uses' => 'Vendor\SessionPackageController@getOpeningTime'));//NEW
    Route::get('delete-opening-time/{id}',array('uses' => 'Vendor\SessionPackageController@deleteOpeningTime'));//NEW
    Route::post('session-duration',array('uses' => 'Vendor\SessionPackageController@updateDuration'));
    Route::get('facility-detail/{id}',array('uses' => 'Vendor\VendorsController@getFacilityDetailInformation'));
    Route::get('duration',array('uses' => 'Vendor\SessionPackageController@getDuration'));
    Route::post('multiple-sessions',array('uses' => 'Vendor\SessionPackageController@createSession'));
    Route::put('multiple-sessions/{id}',array('uses' => 'Vendor\SessionPackageController@updateSession'));
    Route::get('multiple-sessions/{id}',array('uses' => 'Vendor\SessionPackageController@deleteSession'));
    Route::get('sessions-data/{id}',array('uses' => 'Vendor\SessionPackageController@getSessionData'));

    Route::post('calendar-block',array('uses' => 'Vendor\SessionPackageController@blockCalendar'));
    Route::get('calendar-block/{yearmonth}',array('uses' => 'Vendor\SessionPackageController@getBlockData'));
    Route::get('calendar-block/{id}/{yearmonth}',array('uses' => 'Vendor\SessionPackageController@getBlockDataFacilityWise'));
});
// Route::controllers([
// 'auth' => 'Auth\AuthController',
// 'password' => 'Auth\PasswordController',
// ]);
/* For Customer Only */
Route::group([
    'prefix' => 'api/v1/customer/'
], function () {
    Route::post('create/', array(
        'uses' => 'UsersController@storeCustomer'
    ));
    // New
    Route::put('profile', array(
        'uses' => 'Customer\CustomersController@updateProfileInformation'
    ));
    Route::get('profile', array(
        'uses' => 'Customer\CustomersController@getProfileInformation'
    ));
});

/* For Superadmin Only */
Route::group([
    'prefix' => 'api/v1/superadmin/'
], function () {});

/* Common to All Users */
Route::group([
    'prefix' => 'api/v1/user/'
], function () {
    Route::get('confirm/{token}', array(
        'uses' => 'UsersController@confirm'
    ));
    Route::post('auth', array(
        'uses' => 'Auth\AuthController@authenticate'
    ));
    Route::get('logout', array(
        'uses' => 'Auth\AuthController@logout'
    ));
    Route::get('get-root-category', array(
        'uses' => 'UsersController@getRootCategory'
    ));
    Route::get('get-sub-category/{id}', array(
        'uses' => 'UsersController@getSubCategory'
    ));
    Route::get('areas', array(
        'uses' => 'UsersController@getArea'
    ));
    // New
    Route::put('change-password', array(
        'uses' => 'Auth\PasswordController@change'
    ));
});

/* Admin Routes */
Route::group(['prefix' => 'api/v1/admin/'], function () {
    Route::get('vendors',array('uses' => 'Admin\VendorController@getVendorList'));
    Route::post('vendor/create',array('uses' => 'Admin\VendorController@create'));
    Route::put('vendor/my-profile/{id}',array('uses' => 'Admin\VendorController@updateProfile'));
});

Route::get('temp', array(
    'uses' => 'Vendor\VendorsController@index'
));
