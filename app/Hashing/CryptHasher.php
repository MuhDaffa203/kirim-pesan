<?php

namespace App\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HasherContract;

class CryptHasher implements HasherContract
{
    public function make($value, array $options = [])
    {
        // Gunakan crypt() dengan salt acak
        $salt = substr(strtr(base64_encode(random_bytes(16)), '+', '.'), 0, 22);
        return crypt($value, '$2y$10$' . $salt);
    }

    public function check($value, $hashedValue, array $options = [])
    {
        return hash_equals($hashedValue, crypt($value, $hashedValue));
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        return false;
    }

    public function info($hashedValue)
    {
        return [
            'algo' => 'crypt',
            'options' => [],
        ];
    }
}
