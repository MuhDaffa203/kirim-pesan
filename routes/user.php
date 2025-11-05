<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\User\Dashboard\Index;


Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {

    Route::view('/dashboard', 'user.dashboard.index')->name('dashboard');
    Route::view('/chat', 'user.chat.index')->name('chat');
    Route::view('/stegano', 'user.stegano.index')->name('stegano');

});
