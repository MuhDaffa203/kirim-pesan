<?php

namespace App\Services\Encryption;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA\PublicKey;
use phpseclib3\Crypt\RSA\PrivateKey;

class ScytaleRSAEncryptor
{
    /** @var PublicKey */
    protected $publicKey;

    /** @var PrivateKey */
    protected $privateKey;

    public function __construct()
    {
        $publicPath = storage_path('app/keys/public.pem');
        $privatePath = storage_path('app/keys/private.pem');

        if (!file_exists($publicPath) || !file_exists($privatePath)) {
            throw new \Exception('RSA key files not found. Please generate them in storage/app/keys.');
        }

        $this->publicKey = PublicKeyLoader::load(file_get_contents($publicPath));
        $this->privateKey = PublicKeyLoader::load(file_get_contents($privatePath));
    }

    // ============= SCYTALE ENCRYPTION =============
    private function scytaleEncrypt(string $text, int $key = 5): string
    {
        $text = str_replace(' ', '_', $text);
        $cipher = '';

        for ($i = 0; $i < $key; $i++) {
            for ($j = $i; $j < strlen($text); $j += $key) {
                $cipher .= $text[$j];
            }
        }

        return $cipher;
    }

    private function scytaleDecrypt(string $cipher, int $key = 5): string
    {
        $cols = ceil(strlen($cipher) / $key);
        $matrix = array_fill(0, $key, []);
        $idx = 0;

        for ($i = 0; $i < $key; $i++) {
            for ($j = 0; $j < $cols; $j++) {
                if ($idx < strlen($cipher)) {
                    $matrix[$i][$j] = $cipher[$idx++];
                }
            }
        }

        $plain = '';
        for ($j = 0; $j < $cols; $j++) {
            for ($i = 0; $i < $key; $i++) {
                if (isset($matrix[$i][$j])) {
                    $plain .= $matrix[$i][$j];
                }
            }
        }

        return str_replace('_', ' ', $plain);
    }

    // ============= RSA ENCRYPTION =============
    public function rsaEncrypt(string $data): string
    {
        // pastikan kunci public valid
        if (!$this->publicKey instanceof PublicKey) {
            throw new \RuntimeException('Invalid RSA public key');
        }

        return base64_encode($this->publicKey->encrypt($data));
    }

    public function rsaDecrypt(string $data): string
    {
        if (!$this->privateKey instanceof PrivateKey) {
            throw new \RuntimeException('Invalid RSA private key');
        }

        return $this->privateKey->decrypt(base64_decode($data));
    }

    // ============= COMBINED =============
    public function encryptContent(string $text, int $scytaleKey = 5): string
    {
        $scytale = $this->scytaleEncrypt($text, $scytaleKey);
        return $this->rsaEncrypt($scytale);
    }

    public function decryptContent(string $cipher, int $scytaleKey = 5): string
    {
        $rsa = $this->rsaDecrypt($cipher);
        return $this->scytaleDecrypt($rsa, $scytaleKey);
    }
}
