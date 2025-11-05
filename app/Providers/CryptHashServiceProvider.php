<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Hashing\HashManager;
use App\Hashing\CryptHasher;

class CryptHashServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->make('hash')->extend('crypt', function () {
            return new CryptHasher;
        });
    }
}
