<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-6 py-10">
    <!-- Card utama -->
    <div class="bg-white shadow-xl rounded-2xl w-full max-w-4xl p-8 text-center border border-gray-100">
        <div class="flex flex-col md:flex-row items-center justify-between space-y-6 md:space-y-0">

            <!-- Bagian kiri: teks -->
            <div class="md:w-1/2 text-left">
                <h1 class="text-3xl font-bold text-gray-800 mb-3">
                    Selamat Datang, <span class="text-indigo-600">{{ Auth::user()->name }}</span> 👋
                </h1>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    Ini adalah dashboard utama Anda.
                    Dari sini Anda bisa mulai percakapan dengan pengguna lain, berbagi file, dan terhubung dengan tim
                    Anda.
                </p>

                <!-- Tombol Mulai -->
                <a href="{{ route('user.chat') }}"
                    class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg shadow-md transition duration-200">
                    🚀 Mulai Chat
                </a>
            </div>

            <!-- Bagian kanan: ilustrasi -->
            <div class="md:w-1/2 flex justify-center">
                <img src="https://illustrations.popsy.co/gray/communication.svg" alt="Chat Illustration"
                    class="w-72 md:w-80 opacity-90">
            </div>
        </div>
    </div>

    <!-- Statistik ringan -->
    <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4 w-full max-w-4xl">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 text-center">
            <h3 class="text-2xl font-semibold text-indigo-600">{{ $userCount ?? 12 }}</h3>
            <p class="text-gray-600 text-sm">Total Pengguna</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 text-center">
            <h3 class="text-2xl font-semibold text-indigo-600">{{ $messageCount ?? 58 }}</h3>
            <p class="text-gray-600 text-sm">Pesan Dikirim</p>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 text-center">
            <h3 class="text-2xl font-semibold text-indigo-600">{{ $uploadedFiles ?? 0 }}</h3>
            <p class="text-gray-600 text-sm">File Terunggah</p>
        </div>
    </div>
</div>
