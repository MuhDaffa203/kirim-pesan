<?php

namespace App\Livewire\User\Dashboard;

use App\Models\User;
use App\Models\Message;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    public $selectedUser;

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
    }

    public function render()
    {
        $users = User::where('id', '!=', Auth::id())->get();

        return view('livewire.user.dashboard.index', [
            'users' => $users,
            'selectedUser' => $this->selectedUser,
            'userCount' => User::count(),
            'messageCount' => Message::where('sender_id', Auth::id())->count(),
            'uploadedFiles' => Message::where('sender_id', Auth::id())
                ->whereNotNull('file_path')
                ->count(),

        ]);
    }
}
