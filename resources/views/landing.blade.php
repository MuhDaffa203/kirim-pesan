<x-landing>
    <div class="bg-white text-gray-900 min-h-screen flex flex-col font-sans">
        <!-- 🌟 Navbar -->
        <nav class="w-full bg-white shadow-sm fixed top-0 left-0 z-50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
                <a href="/" class="text-2xl font-bold text-indigo-600">KirimPesan</a>
                <div class="space-x-3">
                    <a href="{{ route('login') }}"
                       class="text-gray-700 hover:text-indigo-600 font-semibold">Masuk</a>
                    <a href="{{ route('register') }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold transition">
                       Daftar
                    </a>
                </div>
            </div>
        </nav>

        <!-- 🏠 Hero Section -->
        <section id="home" class="flex flex-col-reverse md:flex-row items-center justify-between max-w-7xl mx-auto px-6 pt-32 pb-20 gap-12">
            <!-- Text -->
            <div class="md:w-1/2 text-center md:text-left">
                <h1 class="text-5xl font-extrabold leading-tight mb-6">
                    Kirim Pesan <span class="text-indigo-600">Lebih Mudah</span> dan Cepat 🚀
                </h1>
                <p class="text-gray-600 text-lg leading-relaxed mb-10">
                    Komunikasi tanpa batas — cepat, aman, dan modern untuk kebutuhan personal maupun bisnis.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="{{ route('register') }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition text-center">
                       Mulai Sekarang
                    </a>
                    <a href="#features"
                       class="border border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-semibold px-8 py-3 rounded-lg transition text-center">
                       Lihat Fitur
                    </a>
                </div>
            </div>

            <!-- Image -->
            <div class="md:w-1/2 flex justify-center">
                <img src="{{ asset('img/chat.png') }}" alt="Chat Illustration" class="w-full max-w-md md:max-w-lg drop-shadow-xl">
            </div>
        </section>

        <!-- ⚙️ Features Section -->
        <section id="features" class="bg-gray-50 py-24">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h2 class="text-4xl font-bold mb-4 text-gray-900">Fitur Unggulan</h2>
                <p class="text-gray-600 max-w-2xl mx-auto mb-12">
                    Didesain untuk mempermudah komunikasi dan meningkatkan produktivitas Anda.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <!-- Card 1 -->
                    <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition">
                        <div class="text-indigo-600 text-4xl mb-4">💬</div>
                        <h3 class="text-xl font-semibold mb-2">Chat Real-time</h3>
                        <p class="text-gray-500">Nikmati pengalaman pesan instan tanpa perlu memuat ulang halaman.</p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition">
                        <div class="text-indigo-600 text-4xl mb-4">📁</div>
                        <h3 class="text-xl font-semibold mb-2">Berbagi File</h3>
                        <p class="text-gray-500">Kirim gambar, dokumen, atau file penting dengan aman.</p>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition">
                        <div class="text-indigo-600 text-4xl mb-4">🔒</div>
                        <h3 class="text-xl font-semibold mb-2">Keamanan Terjamin</h3>
                        <p class="text-gray-500">Setiap pesan terenkripsi untuk melindungi privasi Anda.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 🧭 Footer -->
        <footer class="bg-white border-t border-gray-100 py-8 mt-auto">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center text-gray-600 text-sm gap-3">
                <p>© {{ date('Y') }} <span class="text-indigo-600 font-semibold">KirimPesan</span>. Semua hak dilindungi.</p>
                <p>Dibuat dengan ❤️ menggunakan Laravel & Tailwind CSS.</p>
            </div>
        </footer>
    </div>
</x-landing>
