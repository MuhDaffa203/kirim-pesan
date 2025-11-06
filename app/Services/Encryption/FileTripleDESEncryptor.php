<?php

namespace App\Services\Encryption;

use phpseclib3\Crypt\TripleDES;
use phpseclib3\Crypt\Random;
use Illuminate\Support\Facades\Log;

class FileTripleDESEncryptor
{
    protected string $key;

    public function __construct()
    {
        $pass = env('TRIPLEDES_KEY', '');
        if (empty($pass)) {
            throw new \RuntimeException('TRIPLEDES_KEY not set in .env');
        }

        $raw = hash('sha256', $pass, true);
        $this->key = substr($raw, 0, 24);
    }

    public function encryptFile(string $inputPath, string $outputPath): void
    {
        if (!file_exists($inputPath)) {
            throw new \RuntimeException("Input file not found: {$inputPath}");
        }

        $cipher = new TripleDES('cbc');
        $iv = Random::string(8);

        $cipher->setKey($this->key);
        $cipher->setIV($iv);
        $cipher->disablePadding(); 

        $plaintext = file_get_contents($inputPath);
        if ($plaintext === false) {
            throw new \RuntimeException("Cannot read input file: {$inputPath}");
        }

        // Tambahkan padding manual agar kelipatan 8 byte
        $padLen = 8 - (strlen($plaintext) % 8);
        if ($padLen !== 8) {
            $plaintext .= str_repeat("\0", $padLen);
        }

        $ciphertext = $cipher->encrypt($plaintext);
        file_put_contents($outputPath, $iv . $ciphertext);
    }

    public function decryptFile(string $inputPath, string $outputPath): void
    {
        if (!file_exists($inputPath)) {
            throw new \RuntimeException("Encrypted file not found: {$inputPath}");
        }

        $data = file_get_contents($inputPath);
        if ($data === false || strlen($data) <= 8) {
            throw new \RuntimeException("Encrypted file invalid or too short: {$inputPath}");
        }

        $iv = substr($data, 0, 8);
        $ciphertext = substr($data, 8);

        $cipher = new TripleDES('cbc');
        $cipher->setKey($this->key);
        $cipher->setIV($iv);
        $cipher->disablePadding();

        $plaintext = $cipher->decrypt($ciphertext);
        // Hilangkan padding null
        $plaintext = rtrim($plaintext, "\0");

        file_put_contents($outputPath, $plaintext);
    }
}
