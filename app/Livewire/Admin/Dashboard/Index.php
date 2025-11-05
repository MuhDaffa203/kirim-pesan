<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public function render()
    {
        $userCount = User::count();
        $messageCount = Message::count();
        $fileCount = Message::whereNotNull('file_path')->count();

        // Statistik 7 hari terakhir
        $chartData = Message::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $maxMessages = $chartData->max('total') ?? 1;

        // 5 pengguna paling aktif
        $topUsers = User::select('users.name', DB::raw('COUNT(messages.id) as total'))
            ->join('messages', 'messages.sender_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('livewire.admin.dashboard.index', [
            'userCount' => $userCount,
            'messageCount' => $messageCount,
            'fileCount' => $fileCount,
            'chartData' => $chartData,
            'maxMessages' => $maxMessages,
            'topUsers' => $topUsers,
        ]);
    }
}
