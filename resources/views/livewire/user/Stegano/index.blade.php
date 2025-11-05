<div class="max-w-5xl mx-auto p-8 bg-white rounded-2xl shadow-lg space-y-10">

    {{-- ==================== JUDUL ==================== --}}
    <div class="text-center border-b pb-4">
        <h1 class="text-3xl font-extrabold text-gray-800 tracking-wide">
            Steganografi Gambar <span class="text-indigo-600">(Metode PVD)</span>
        </h1>
        <p class="text-gray-500 mt-1 text-sm">Sembunyikan dan ambil pesan rahasia di dalam gambar secara aman</p>
    </div>

    {{-- ==================== 🔐 ENKRIPSI ==================== --}}
    <div class="bg-gray-50 p-6 rounded-2xl shadow-inner space-y-5">
        <h2 class="text-lg font-semibold text-indigo-600 flex items-center gap-2">
            Enkripsi Pesan ke Gambar
        </h2>

        {{-- Upload Gambar --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Cover</label>
            <input type="file" wire:model="coverImage" accept="image/*"
                class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            @error('coverImage')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror

            @if ($coverImage)
                <div class="mt-3 flex justify-center">
                    <img src="{{ $coverImage->temporaryUrl() }}" class="rounded-lg border max-h-56 shadow-md">
                </div>
            @endif
        </div>

        {{-- Pesan Rahasia --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pesan Rahasia</label>
            <textarea wire:model="secretMessage" rows="3"
                placeholder="Masukkan pesan rahasia di sini..."
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            @error('secretMessage')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tombol --}}
        <div class="flex flex-wrap gap-3">
            <button wire:click="encryptImage"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg shadow transition transform hover:scale-105">
                🔐 Enkripsi & Sisipkan
            </button>

            <button wire:click="resetForm"
                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2.5 rounded-lg shadow transition">
                🔁 Reset
            </button>
        </div>

        {{-- Hasil Enkripsi --}}
        @if ($stegoImage)
            <div class="mt-6 border-t pt-4 space-y-3 text-center">
                <h3 class="text-sm font-medium text-gray-700">🖼 Gambar Hasil Stego</h3>
                <img src="{{ asset('storage/' . $stegoImage) }}" class="rounded-lg border max-h-72 mx-auto shadow-lg">

                <div class="flex justify-center gap-3 mt-4">
                    <a href="{{ asset('storage/' . $stegoImage) }}" download
                        class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow transition transform hover:scale-105">
                        💾 Unduh Gambar Stego
                    </a>
                    <button wire:click="resetForm"
                        class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow">
                        🔄 Enkripsi Ulang
                    </button>
                </div>
            </div>
        @endif
    </div>



    {{-- ==================== LOADING ANIMATION ==================== --}}
    <div wire:loading
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm z-50">
        <div class="bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center space-y-4">
            <div class="w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        </div>
    </div>
</div>
