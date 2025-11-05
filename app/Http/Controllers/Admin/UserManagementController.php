<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('livewire.admin.manage-users', [
            'users' => $users,
        ]);
    }
}
