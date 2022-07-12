<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*(if(session()->has('token')){
            $localUser = User::where('token', session()->get('token'));
            View::share('name', $localUser->name);
        } else {
            View::share('name', "Guest");
        }*/
    }
}
