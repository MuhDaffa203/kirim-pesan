<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Kamu bisa ubah ini ke view dashboard admin kamu sendiri
        return view('livewire.admin.dashboard');
    }
}
