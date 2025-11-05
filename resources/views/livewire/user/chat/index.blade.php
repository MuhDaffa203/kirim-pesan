<div class="relative">
    <!-- === Komponen utama Chat === -->
    <div class="flex h-[80vh] bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Sidebar daftar user -->
        <div class="w-1/3 border-r border-gray-200 bg-gray-50">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-700">Daftar Pengguna</h2>
            </div>
            <ul class="divide-y divide-gray-200 max-h-[70vh] overflow-y-auto">
                @foreach ($users as $u)
                    <li wire:click="selectUser({{ $u->id }})"
                        class="p-3 cursor-pointer hover:bg-indigo-100 {{ $selectedUser && $selectedUser->id === $u->id ? 'bg-indigo-50' : '' }}">
                        <div class="flex items-center space-x-3">
                            <div class="h-8 w-8 bg-indigo-500 text-white rounded-full flex items-center justify-center">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $u->name }}</p>
                                <p class="text-xs text-gray-500">{{ $u->email }}</p>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Area chat -->
        <div class="flex-1 flex flex-col">
            @if ($selectedUser)
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center space-x-2">
                        <div class="h-10 w-10 bg-indigo-500 text-white rounded-full flex items-center justify-center">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $selectedUser->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Body chat -->
                <div class="flex-1 p-4 space-y-2 overflow-y-auto bg-gray-50">
                    @forelse($messages as $msg)
                        @php
                            $isSender = $msg->sender_id === auth()->id();
                        @endphp

                        <div class="flex {{ $isSender ? 'justify-end' : 'justify-start' }}">
                            <div x-data="{ confirmDelete: false }"
                                class="{{ $isSender ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-800' }} 
                                       px-4 py-2 rounded-2xl max-w-xs text-sm shadow relative group">

                                {{-- Tombol hapus hanya untuk pengirim --}}
                                @if ($isSender)
                                    <button @click="confirmDelete = true"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs px-1.5 py-0.5 
                                               opacity-0 group-hover:opacity-100 transition"
                                        title="Hapus pesan">
                                        🗑️
                                    </button>

                                    <!-- Konfirmasi hapus -->
                                    <div x-show="confirmDelete" x-transition.opacity.duration.200ms
                                        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-[9999]">
                                        <div class="bg-white rounded-xl shadow-xl p-4 w-64 text-center">
                                            <p class="text-gray-800 text-sm mb-3 font-medium">
                                                Yakin ingin menghapus pesan ini?</p>
                                            <div class="flex justify-center space-x-3">
                                                <button @click="confirmDelete = false"
                                                    class="px-3 py-1 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 text-sm">
                                                    Batal
                                                </button>
                                                <button wire:click="deleteMessage({{ $msg->id }})"
                                                    @click="confirmDelete = false"
                                                    class="px-3 py-1 rounded-md bg-red-500 text-white hover:bg-red-600 text-sm">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- File / Gambar --}}
                                @if ($msg->file_path)
                                    @php
                                        $url = Storage::url($msg->file_path);
                                        $ext = strtolower(pathinfo($msg->file_path, PATHINFO_EXTENSION));
                                    @endphp

                                    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ $url }}" alt="gambar"
                                            class="rounded-lg max-h-40 mb-1 cursor-pointer hover:opacity-90 transition"
                                            wire:click="showImage({{ $msg->id }})">
                                    @endif

                                    <div class="flex flex-col space-y-1">
                                        <a href="#"
                                            wire:click.prevent="downloadFile('{{ basename($msg->file_path) }}')"
                                            class="underline text-xs {{ $isSender ? 'text-gray-200 hover:text-white' : 'text-blue-600 hover:text-blue-800' }}">
                                            ⬇️ {{ basename($msg->file_path) }}
                                        </a>

                                        {{-- Tombol decode gambar --}}
                                        @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                            <button wire:click="decryptStegoMessage('{{ basename($msg->file_path) }}')"
                                                class="bg-green-500 hover:bg-green-600 text-white text-xs px-2 py-1 rounded shadow-sm">
                                                🔍 Lihat Pesan Rahasia
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                {{-- Pesan teks --}}
                                @if ($msg->content)
                                    <p>{{ $msg->content }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 text-sm mt-4">Belum ada pesan</p>
                    @endforelse
                </div>

                {{-- Hasil Dekripsi (global) --}}
                @if ($decryptedMessage)
                    <div class="mt-3 mx-4 p-3 bg-green-100 text-green-800 rounded-xl">
                        <strong>Pesan Rahasia:</strong>
                        <p class="mt-1">{{ $decryptedMessage }}</p>
                    </div>
                @endif

                <!-- Input area -->
                <div class="p-4 border-t border-gray-200 bg-white">
                    <form wire:submit.prevent="sendMessage" class="flex flex-col space-y-2">
                        {{-- Preview file sebelum dikirim --}}
                        @if ($file)
                            <div class="flex items-center space-x-3">
                                @php $ext = strtolower($file->getClientOriginalExtension()); @endphp
                                @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $file->temporaryUrl() }}" alt="Preview"
                                        class="h-16 rounded-lg border">
                                @else
                                    <p class="text-sm text-gray-600">📄 {{ $file->getClientOriginalName() }}</p>
                                @endif
                                <button type="button" wire:click="removeFile"
                                    class="text-red-500 text-xs hover:underline">Hapus</button>
                            </div>
                        @endif

                        {{-- Input pesan --}}
                        <div class="flex items-center space-x-2">
                            <input type="file" wire:model="file" id="fileInput" class="hidden">
                            <label for="fileInput"
                                class="cursor-pointer bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm shadow">
                                +
                            </label>

                            <input type="text" wire:model="message" placeholder="Ketik pesan..."
                                class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">

                            <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm shadow">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center text-gray-500">
                    <p>Pilih pengguna untuk mulai chat</p>
                </div>
            @endif
        </div>
    </div>

    <!-- === Modal Preview Gambar === -->
    @if ($showImageModal)
        <div x-data="{ open: @entangle('showImageModal') }" x-show="open" @click.self="open = false; $wire.closeImage()"
            x-transition.opacity.duration.300ms x-cloak
            class="fixed inset-0 bg-black bg-opacity-70 z-[9999] flex items-center justify-center">

            <button @click="open = false; $wire.closeImage()"
                class="fixed top-4 right-4 bg-gray-800 text-white rounded-full p-2 hover:bg-gray-700 shadow-lg z-[10000]"
                title="Tutup">
                ✕
            </button>

            <div class="relative">
                @if ($previewImage)
                    <img src="{{ $previewImage }}" alt="Preview"
                        class="max-h-[80vh] max-w-[90vw] rounded-lg shadow-2xl">
                @endif
            </div>
        </div>
    @endif


    <!-- 🔓 Modal Pesan Rahasia -->
    @if ($secretMessage)
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-[9999]">
            <div class="bg-white rounded-lg shadow-lg p-6 w-96 text-center">
                <h2 class="text-lg font-semibold mb-2">Pesan Rahasia 🔐</h2>
                <p class="text-gray-700 break-words mb-4">{{ $secretMessage }}</p>
                <button wire:click="$set('secretMessage', null)"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    @if ($secretMessage)
        <pre class="text-xs text-gray-500 mt-2">DEBUG: {{ $secretMessage }}</pre>
    @endif





</div>
