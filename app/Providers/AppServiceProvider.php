<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Hashing\CryptHasher;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Daftar hasher custom

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Hash::extend('crypt', function () {
            return new CryptHasher;
        });
    }
}
