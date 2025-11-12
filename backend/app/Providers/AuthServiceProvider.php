<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
    * Register services.
    */
    public function register(): void
    {
        //
    }

    /**
    * Bootstrap services.
    */
    public function boot(): void
    {
        Gate::define('admin', function ($user): bool {
            return $user->role === 'admin';
        });

        Gate::define('user', function($user): bool {
            return $user->role === 'user';
        });
    }
}
