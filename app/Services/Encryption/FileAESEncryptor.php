<?php

namespace App\Services\Encryption;

class FileAESEncryptor
{
    private $key;
    private $cipher = 'AES-256-CBC';

    public function __construct()
    {
        // Gunakan APP_KEY Laravel sebagai basis key
        $this->key = hash('sha256', config('app.key'), true);
    }

    public function encryptFile($inputPath, $outputPath)
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $data = file_get_contents($inputPath);

        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        file_put_contents($outputPath, base64_encode($iv . $encrypted));
    }

    public function decryptFile($inputPath, $outputPath)
    {
        $data = base64_decode(file_get_contents($inputPath));
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = substr($data, 0, $ivLength);
        $encryptedData = substr($data, $ivLength);

        $decrypted = openssl_decrypt($encryptedData, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        file_put_contents($outputPath, $decrypted);
    }
}
