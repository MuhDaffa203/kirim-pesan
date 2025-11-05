<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Import middleware kita
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\UserMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/admin.php',
            __DIR__.'/../routes/user.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /**
         * Daftarkan alias middleware
         * Jadi kamu bisa panggil langsung 'admin' atau 'user' di route
         */
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'user'  => UserMiddleware::class,
        ]);

        /**
         * (Opsional) Tambahkan middleware ke grup 'web'
         * Biasanya tidak wajib, cukup daftarkan alias saja.
         * Kalau kamu mau mereka selalu aktif di web routes, bisa tambahkan seperti ini:
         */
        // $middleware->appendToGroup('web', [
        //     AdminMiddleware::class,
        //     UserMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
