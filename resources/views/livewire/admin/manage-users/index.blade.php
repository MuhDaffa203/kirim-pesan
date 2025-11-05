<div class="max-w-6xl mx-auto p-6">
    {{-- Tombol Navigasi --}}

    <div class="flex flex-wrap gap-3 mb-6">
        <button wire:click="pilihMenu('lihat')"
            class="px-4 py-2 rounded-md font-semibold transition
                {{ $pilihanMenu == 'lihat' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-blue-600 text-blue-600 hover:bg-blue-50' }}">
            Semua Pengguna
        </button>

        <button wire:click="pilihMenu('tambah')"
            class="px-4 py-2 rounded-md font-semibold transition
                {{ $pilihanMenu == 'tambah' ? 'bg-blue-600 text-white shadow' : 'bg-white border border-blue-600 text-blue-600 hover:bg-blue-50' }}">
            Tambah Pengguna
        </button>

        <button wire:loading
            class="px-4 py-2 rounded-md bg-blue-100 text-blue-700 font-semibold cursor-not-allowed shadow">
            Loading ...
        </button>
    </div>

    {{-- Konten Utama --}}
    <div>
        @if ($pilihanMenu == 'lihat')
            {{-- Daftar Pengguna --}}
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 font-semibold text-gray-700 flex justify-between">
                    <span>Semua Pengguna</span>
                    <span class="text-sm text-gray-500">{{ $semuaPengguna->count() }} total pengguna</span>
                </div>

                <div class="p-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">No</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Email</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Role</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($semuaPengguna as $pengguna)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2">{{ $pengguna->name }}</td>
                                    <td class="px-4 py-2">{{ $pengguna->email }}</td>
                                    <td class="px-4 py-2 capitalize">{{ $pengguna->role }}</td>
                                    <td class="px-4 py-2 flex gap-2">
                                        <button wire:click="pilihEdit({{ $pengguna->id }})"
                                            class="px-3 py-1 rounded-md text-sm bg-white border border-blue-600 text-blue-600 hover:bg-blue-50 transition">
                                            Edit
                                        </button>
                                        <button wire:click="pilihHapus({{ $pengguna->id }})"
                                            class="px-3 py-1 rounded-md text-sm bg-white border border-red-600 text-red-600 hover:bg-red-50 transition">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif ($pilihanMenu == 'tambah')
            {{-- Form Tambah Pengguna --}}
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 font-semibold text-gray-700">Tambah Pengguna</div>
                <div class="p-4">
                    <form wire:submit.prevent="simpan" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" wire:model="nama"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('nama')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" wire:model="password"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Peran</label>
                            <select wire:model="role"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">--Pilih Role--</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                            @error('role')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="!bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:!bg-blue-700 focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
                                    Simpan
                                </button>

                                <button type="button" wire:click="batal"
                                    class="!bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:!bg-gray-300 transition">
                                    Batal
                                </button>
                            </div>

                    </form>
                </div>
            </div>
        @elseif ($pilihanMenu == 'edit')
            {{-- Form Edit Pengguna --}}
            <div class="bg-white shadow rounded-lg border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 font-semibold text-gray-700">Edit Pengguna</div>
                <div class="p-4">
                    <form wire:submit.prevent="simpanEdit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" wire:model="nama"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('nama')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password (opsional)</label>
                            <input type="password" wire:model="password"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('password')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Peran</label>
                            <select wire:model="role"
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">--Pilih Role--</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                            @error('role')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                                Simpan
                            </button>
                            <button type="button" wire:click="batal"
                                class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif ($pilihanMenu == 'hapus')
            {{-- Konfirmasi Hapus --}}
            <div class="bg-white shadow rounded-lg border border-red-300">
                <div class="px-4 py-3 border-b border-red-200 bg-red-50 text-red-700 font-semibold">Hapus Pengguna</div>
                <div class="p-4 text-gray-700">
                    <p>Apakah Anda yakin ingin menghapus pengguna ini?</p>
                    <p class="mt-2"><span class="font-semibold">Nama:</span> {{ $penggunaTerpilih->name }}</p>
                    <div class="mt-4 flex gap-2">
                        <button wire:click="hapus"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                            Hapus
                        </button>
                        <button wire:click="batal"
                            class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
