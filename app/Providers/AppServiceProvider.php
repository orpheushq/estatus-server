<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        // https://laravel-news.com/laravel-5-4-key-too-long-error mentions 191 should be max key length
        // However, spatie-permissions limits it even more https://spatie.be/docs/laravel-permission/v5/prerequisites#content-schema-limitation-in-mysql
        Schema::defaultStringLength(125);

        Password::defaults(function () {
            $rule = Password::min(8);

            return $rule;
        });
    }
}
