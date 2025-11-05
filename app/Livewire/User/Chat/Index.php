<?php

namespace App\Livewire\User\Chat;

use App\Models\User;
use App\Models\Message;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use App\Services\Encryption\FileTripleDESEncryptor;
use App\Services\Encryption\PvdSteganography;

class Index extends Component
{
    use WithFileUploads;

    public $selectedUser = null;
    public $message = '';
    public $file;
    public $secretMessage = '';
    public $decryptedMessage;
    public $showImageModal = false;
    public $previewImage = null;
    public $toastMessage = null;
    public $toastType = 'success';
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'showSecretMessage' => '$refresh',
    ];

    protected $rules = [
        'message' => 'nullable|string|max:2000',
        'file' => 'nullable|file|max:10240',
        'secretMessage' => 'nullable|string|max:2000',
    ];

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
    }

    public function sendMessage()
    {
        $this->validate();
        if (!$this->selectedUser) return;
        if (trim($this->message) === '' && !$this->file) return;

        $path = null;
        $type = 'text';
        $originalName = null;
        $safeName = null;

        if ($this->file) {
            $extension = strtolower($this->file->getClientOriginalExtension());
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
            $type = $isImage ? 'image' : 'file';

            $originalName = $this->file->getClientOriginalName();
            $safeName = time() . '_' . str_replace(' ', '_', $originalName);

            // Path dasar
            $baseDir = storage_path('app/public/chat/files');
            $tmpDir = storage_path('app/chat/tmp');
            if (!is_dir($baseDir)) mkdir($baseDir, 0777, true);
            if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);

            $storedPath = $this->file->storeAs('chat/tmp', $safeName, 'local');
            $tempPath = Storage::disk('local')->path($storedPath);

            // 📸 Jika gambar + pesan rahasia
            if ($isImage && $this->secretMessage) {
                $outputPath = $baseDir . DIRECTORY_SEPARATOR . 'stego_' . $safeName;
                $pvd = new \App\Services\Encryption\PvdSteganography();
                $pvd->embedMessage($tempPath, $this->secretMessage, $outputPath);
                Log::info('🧩 [STEGANO] Pesan rahasia disisipkan.', ['file' => $outputPath]);

                $path = 'chat/files/stego_' . $safeName;
                @unlink($tempPath); // hapus file tmp
            }
            // 📁 Jika file bukan gambar → lakukan enkripsi TripleDES
            elseif (!$isImage) {
                $encryptor = new \App\Services\Encryption\FileTripleDESEncryptor();
                $encryptedPath = $baseDir . DIRECTORY_SEPARATOR . 'enc_' . $safeName;
                $encryptor->encryptFile($tempPath, $encryptedPath);
                Log::info('🔐 [ENCRYPT] File terenkripsi disimpan.', ['file' => $encryptedPath]);

                $path = 'chat/files/enc_' . $safeName;
                @unlink($tempPath);
            }
            // 📸 Jika gambar tanpa pesan rahasia
            else {
                $this->file->storeAs('chat/files', $safeName, 'public');
                Log::info('🖼 [IMAGE] Gambar disimpan tanpa enkripsi.', ['file' => $safeName]);
                $path = 'chat/files/' . $safeName;
            }
        }

        // Simpan ke database
        \App\Models\Message::create([
            'sender_id' => \Illuminate\Support\Facades\Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'content' => trim($this->message) !== '' ? $this->message : null,
            'file_path' => $path,
            'type' => $type,
            'original_name' => $originalName,
        ]);

        $this->reset(['message', 'file', 'secretMessage']);
        $this->dispatch('$refresh');
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pesan berhasil dikirim!'
        ]);
    }

    public function removeFile()
    {
        $this->file = null;
    }

    public function downloadFile($fileName)
    {
        $filesDir = storage_path('app/public/chat/files');
        $encryptedPath = $filesDir . DIRECTORY_SEPARATOR . $fileName;
        if (!file_exists($encryptedPath)) abort(404, 'File tidak ditemukan.');

        $tmpDir = storage_path('app/public/chat/tmp');
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);

        $safeOriginal = str_replace('enc_', '', $fileName);
        $decryptedPath = $tmpDir . DIRECTORY_SEPARATOR . $safeOriginal;

        $decryptor = new FileTripleDESEncryptor();
        $decryptor->decryptFile($encryptedPath, $decryptedPath);

        $mime = mime_content_type($decryptedPath) ?: 'application/octet-stream';
        return response()->download($decryptedPath, $safeOriginal, [
            'Content-Type' => $mime,
        ])->deleteFileAfterSend(true);
    }

    public function decryptStegoMessage($filePath)
    {
        try {
            Log::info('🧩 [DECRYPT] Mulai ekstraksi pesan...', ['filePath' => $filePath]);

            $inputPath = storage_path('app/public/chat/files/' . basename($filePath));
            $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));

            if (!file_exists($inputPath)) {
                Log::error('❌ [DECRYPT] File tidak ditemukan.', ['expected' => $inputPath]);
                throw new \Exception('File tidak ditemukan di lokasi yang diharapkan.');
            }

            // Jika gambar
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                Log::info('🖼 [DECRYPT] File gambar terdeteksi, langsung proses steganografi.');

                $pvd = new \App\Services\Encryption\PvdSteganography();
                $message = $pvd->extractMessage($inputPath);

                if (!$message) {
                    throw new \Exception('Tidak ditemukan pesan tersembunyi di gambar.');
                }

                $this->secretMessage = $message;
                Log::info('✅ [DECRYPT] Pesan rahasia berhasil diekstrak.');
                Log::info('🧠 [DEBUG] secretMessage diset: ' . substr($message, 0, 100));

                // 🧩 Paksa Livewire update DOM (bukan $refresh)
                $this->dispatch('showSecretMessage');

                return;
            }

            // Jika bukan gambar
            Log::info('🔐 [DECRYPT] File bukan gambar, mulai proses dekripsi.');
            $decryptor = new \App\Services\Encryption\FileTripleDESEncryptor();
            $tmpOutput = storage_path('app/public/chat/tmp/dec_' . basename($filePath));
            $decryptor->decryptFile($inputPath, $tmpOutput);
            Log::info('✅ [DECRYPT] File berhasil didekripsi.', ['output' => $tmpOutput]);
        } catch (\Throwable $e) {
            Log::error('❌ [DECRYPT] Gagal mengekstrak pesan.', ['error' => $e->getMessage()]);
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Gagal mengekstrak pesan!'
            ]);
        }
    }

    public function showImage($messageId)
    {
        $message = Message::find($messageId);

        if (!$message || !$message->file_path) {
            Log::warning('⚠️ [PREVIEW] Gagal: pesan/file_path tidak ditemukan.', [
                'id' => $messageId,
            ]);
            return;
        }

        $absolutePath = storage_path('app/public/' . $message->file_path);

        if (!file_exists($absolutePath)) {
            Log::error('❌ [PREVIEW] File tidak ditemukan di storage.', ['path' => $absolutePath]);
            return;
        }

        $publicUrl = asset('storage/' . $message->file_path);

        $this->previewImage = $publicUrl;
        $this->showImageModal = true;

        Log::info('🖼 [PREVIEW] Gambar siap ditampilkan.', [
            'id' => $messageId,
            'url' => $publicUrl,
        ]);
    }


    public function closeImage()
    {
        $this->showImageModal = false;
        $this->previewImage = null;
    }

    public function deleteMessage($id)
    {
        $message = Message::find($id);

        if (!$message || $message->sender_id !== Auth::id()) {
            session()->flash('toastType', 'error');
            session()->flash('toastMessage', 'Pesan tidak ditemukan atau Anda tidak memiliki izin.');
            $this->resetErrorBag();
            $this->resetValidation();
            return;
        }

        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            Storage::disk('public')->delete($message->file_path);
        }

        $message->delete();

        session()->flash('toastType', 'success');
        session()->flash('toastMessage', 'Pesan berhasil dihapus!');

        $this->resetErrorBag();
        $this->resetValidation();

        // kirim event toast langsung tanpa session
        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Pesan berhasil dihapus!'
        ]);

        $this->dispatch('messageDeleted');
    }

    public function render()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $messages = [];

        if ($this->selectedUser) {
            $messages = Message::where(function ($q) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $this->selectedUser->id);
            })->orWhere(function ($q) {
                $q->where('sender_id', $this->selectedUser->id)->where('receiver_id', Auth::id());
            })->orderBy('created_at', 'asc')->get();
        }

        return view('livewire.user.chat.index', [
            'users' => $users,
            'messages' => $messages,
        ]);
    }
}
