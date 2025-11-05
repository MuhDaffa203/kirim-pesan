<?php

use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard.index')->name('dashboard');
    Route::view('/users', 'admin.manage-users.index')->name('manage-users');
});
