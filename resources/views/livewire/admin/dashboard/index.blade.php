<div class="max-w-7xl mx-auto p-6 space-y-8">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-800">📊 Dashboard Admin</h1>
        <p class="text-gray-500 text-sm mt-1">Ringkasan aktivitas pengguna & pesan</p>
    </div>

    <!-- Statistik utama -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
            <h2 class="text-sm font-medium text-gray-500">Jumlah Pengguna</h2>
            <p class="text-3xl font-bold text-indigo-600 mt-2">{{ $userCount }}</p>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
            <h2 class="text-sm font-medium text-gray-500">Pesan Dikirim</h2>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $messageCount }}</p>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6 border border-gray-100">
            <h2 class="text-sm font-medium text-gray-500">File Terunggah</h2>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $fileCount }}</p>
        </div>
    </div>

    <!-- Statistik Aktivitas & Pengguna -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Aktivitas Pesan (7 Hari Terakhir) -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Pesan (7 Hari Terakhir)</h3>

            <div class="space-y-3">
                @foreach ($chartData as $day)
                    <div>
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>{{ \Carbon\Carbon::parse($day->date)->translatedFormat('l, d M') }}</span>
                            <span>{{ $day->total }} pesan</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div
                                class="h-2 rounded-full bg-indigo-500 transition-all duration-300"
                                style="width: {{ ($day->total / max(1, $maxMessages)) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Pengguna Paling Aktif -->
        <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Top 5 Pengguna Paling Aktif</h3>
            <ul class="divide-y divide-gray-200">
                @forelse ($topUsers as $user)
                    <li class="py-3 flex justify-between items-center">
                        <span class="font-medium text-gray-700">{{ $user->name }}</span>
                        <span class="text-sm text-gray-500">{{ $user->total }} pesan</span>
                    </li>
                @empty
                    <li class="py-3 text-gray-400 text-sm italic">Belum ada data pengguna aktif</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
