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
