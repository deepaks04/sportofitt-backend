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
    Route::put('facility/{id}',array('uses' => 'Vendor\VendorsController@enableDisableFacility'));
    Route::put('facility/{id}',array('uses' => 'Vendor\VendorsController@updateFacility'));
    //NEW
    Route::get('package-types',array('uses' => 'Vendor\SessionPackageController@types'));
    Route::post('package',array('uses' => 'Vendor\SessionPackageController@createPackage'));
    Route::put('package/{id}',array('uses' => 'Vendor\SessionPackageController@updatePackage'));
    Route::get('package/{id}',array('uses' => 'Vendor\SessionPackageController@getPackage'));
    Route::get('delete-package/{id}',array('uses' => 'Vendor\SessionPackageController@deletePackage'));
    Route::post('opening-time',array('uses' => 'Vendor\SessionPackageController@createOpeningTime'));
    Route::put('opening-time/{id}',array('uses' => 'Vendor\SessionPackageController@updateOpeningTime'));

    Route::get('opening-time/{id}',array('uses' => 'Vendor\SessionPackageController@getOpeningTime'));
    Route::get('delete-opening-time/{id}',array('uses' => 'Vendor\SessionPackageController@deleteOpeningTime'));
    Route::post('session-duration',array('uses' => 'Vendor\SessionPackageController@updateDuration'));
    Route::get('facility-detail/{id}',array('uses' => 'Vendor\VendorsController@getFacilityDetailInformation'));
    Route::get('duration',array('uses' => 'Vendor\SessionPackageController@getDuration'));
    Route::post('multiple-sessions',array('uses' => 'Vendor\SessionPackageController@createSession'));
    Route::put('multiple-sessions/{id}',array('uses' => 'Vendor\SessionPackageController@updateSession'));
    Route::get('multiple-sessions/{id}',array('uses' => 'Vendor\SessionPackageController@deleteSession'));
    Route::get('sessions-data/{id}',array('uses' => 'Vendor\SessionPackageController@getSessionData'));

    Route::post('calendar-block',array('uses' => 'Vendor\SessionPackageController@blockCalendar'));
    Route::get('calendar-block/{id}',array('uses' => 'Vendor\SessionPackageController@deleteBlockedData'));
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
    // Password reset link request routes...
    Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
    //Route::get('password/reset', 'Auth\PasswordController@getReset');
    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');
    /* Day master */
    Route::get('day-master', 'UsersController@dayMaster');
});

/* Admin Routes */
Route::group(['prefix' => 'api/v1/admin/vendor'], function () {
    Route::get('all',array('uses' => 'Admin\VendorController@getVendorList'));
    Route::post('create',array('uses' => 'Admin\VendorController@create'));
    Route::get('my-profile/{uid}',array('uses' => 'Admin\VendorController@getProfile'));
    Route::put('my-profile/{uid}',array('uses' => 'Admin\VendorController@updateProfile'));
    Route::get('billing-info/{uid}',array('uses' => 'Admin\VendorController@getBillingInformation'));
    Route::put('billing-info/{uid}',array('uses' => 'Admin\VendorController@updateBillingInformation'));
    Route::get('bank-info/{uid}',array('uses' => 'Admin\VendorController@getBankDetails'));
    Route::put('bank-info/{uid}',array('uses' => 'Admin\VendorController@updateBankDetails'));
    Route::post('images/{uid}',array('uses' => 'Admin\VendorController@addImages'));
    Route::get('images/{uid}',array('uses' => 'Admin\VendorController@getImages'));
    Route::get('images/{uid}/{id}',array('uses' => 'Admin\VendorController@deleteImage'));
    Route::post('facility/{uid}',array('uses' => 'Admin\VendorController@createFacility'));
    Route::get('facility/{uid}',array('uses' => 'Admin\VendorController@getFacility'));
    Route::get('facility/{uid}/{id}',array('uses' => 'Admin\VendorController@getFacilityById'));
    Route::put('facility/{uid}/{id}',array('uses' => 'Admin\VendorController@updateFacility'));
    Route::get('package-types',array('uses' => 'Vendor\SessionPackageController@types'));
    Route::post('package/{uid}',array('uses' => 'Admin\SessionPackageController@createPackage'));
    Route::put('package/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@updatePackage'));
    Route::get('package/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@getPackage'));
    Route::get('delete-package/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@deletePackage'));
    Route::post('opening-time/{uid}',array('uses' => 'Admin\SessionPackageController@createOpeningTime'));
    Route::put('opening-time/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@updateOpeningTime'));
    Route::get('opening-time/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@getOpeningTime'));
    Route::get('delete-opening-time/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@deleteOpeningTime'));
    //Route::post('session-duration/{uid}',array('uses' => 'Admin\SessionPackageController@updateDuration'));
    //Route::get('facility-detail/{id}',array('uses' => 'Admin\VendorsController@getFacilityDetailInformation'));
    //Route::get('duration',array('uses' => 'Admin\SessionPackageController@getDuration'));
    Route::post('multiple-sessions/{uid}',array('uses' => 'Admin\SessionPackageController@createSession'));
    Route::put('multiple-sessions/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@updateSession'));
    Route::get('multiple-sessions/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@deleteSession'));
    Route::get('sessions-data/{uid}/{id}',array('uses' => 'Admin\SessionPackageController@getSessionData'));
    Route::post('calendar-block/{uid}',array('uses' => 'Admin\SessionPackageController@blockCalendar'));
    Route::get('calendar-block/{uid}/{yearmonth}',array('uses' => 'Admin\SessionPackageController@getBlockData'));
    Route::get('calendar-block/{uid}/{id}/{yearmonth}',array('uses' => 'Admin\SessionPackageController@getBlockDataFacilityWise'));

});

Route::get('temp', array(
    'uses' => 'Vendor\VendorsController@index'
));
