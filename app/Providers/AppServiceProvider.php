<?php
namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });
        Validator::extend('float', function($attribute, $value)
        {
            return preg_match('/^\d+(\.\d)?$/', $value);
        });
        Validator::extend('alpha_specialchars', function($attribute, $value)
        {
            return preg_match('/(^[A-Za-z ,&._-]+$)+/', $value);
        });
        Validator::extend('zip', function($attribute, $value)
        {
            return preg_match('/^[0-9]{6}(\-[0-9]{4})?$/', $value);
        });
        Validator::extend('mobile', function($attribute, $value)
        {
            return preg_match('/^[0-9]{10}(\-[0-9]{4})?$/', $value);
        });
        Validator::extend('ifsc', function($attribute, $value)
        {
            return preg_match('/^[A-Za-z0-9]{11}(\-[0-9]{4})?$/', $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
