<?php

namespace App\Livewire\User\Stegano;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use App\Services\Encryption\PvdSteganography;

class Index extends Component
{
    use WithFileUploads;

    public $coverImage;
    public $secretMessage;
    public $stegoImage;
    public $stegoInput;
    public $decryptedMessage;


    /**
     * 🔒 Enkripsi & sisipkan pesan ke gambar
     */
    public function encryptImage()
    {
        $this->validate([
            'coverImage' => 'required|image|max:10240',
            'secretMessage' => 'required|string|max:2000',
        ]);

        try {
            Log::info('🟢 [ENCRYPT] Mulai proses embed...');

            // 1️⃣ Simpan gambar cover ke storage
            $path = $this->coverImage->store('stegano/original', 'public');

            // 2️⃣ Tentukan path hasil output
            $outputPath = 'stegano/output/stego_' . basename($path);

            // 3️⃣ Jalankan proses embed PVD
            $pvd = new PvdSteganography();
            $pvd->embedMessage(
                storage_path('app/public/' . $path),
                $this->secretMessage,
                storage_path('app/public/' . $outputPath)
            );

            if (!file_exists(storage_path('app/public/' . $outputPath))) {
                throw new \Exception('Gagal membuat gambar hasil steganografi.');
            }

            $this->stegoImage = $outputPath;

            $this->dispatch('stegoImageReady', ['url' => asset('storage/' . $outputPath)]);
            $this->dispatch('toast', ['type' => 'success', 'message' => 'Pesan berhasil disisipkan ke gambar!']);
        } catch (\Throwable $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Terjadi kesalahan saat menyembunyikan pesan.']);
        }
    }

    public function decryptImage()
    {
        $this->validate([
            'stegoInput' => 'required|image|max:10240',
        ]);

        try {
            // Simpan file ke folder decrypt
            $path = $this->stegoInput->store('stegano/output', 'public');
            $pvd = new PvdSteganography();

            $message = $pvd->extractMessage(storage_path('app/public/' . $path));
            $this->decryptedMessage = $message ?: '(Tidak ditemukan pesan tersembunyi)';

            $this->dispatch('toast', ['type' => 'success', 'message' => 'Pesan berhasil diambil dari gambar!']);
        } catch (\Throwable $e) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Gagal mendekripsi gambar.']);
        }
    }


    public function resetForm()
    {
        $this->reset(['coverImage', 'secretMessage', 'stegoImage', 'stegoInput', 'decryptedMessage']);
    }

    public function render()
    {
        return view('livewire.user.stegano.index');
    }
}
