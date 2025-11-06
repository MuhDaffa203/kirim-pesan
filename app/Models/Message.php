<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Encryption\ScytaleRSAEncryptor;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'file_path',
        'type',
        'is_read',
    ];


    public function setContentAttribute($value)
    {
        if (!empty($value)) {
            $encryptor = new ScytaleRSAEncryptor();
            $this->attributes['content'] = $encryptor->encryptContent($value);
        }
    }

    public function getContentAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            $encryptor = new ScytaleRSAEncryptor();
            return $encryptor->decryptContent($value);
        } catch (\Exception $e) {
            return '[DECRYPTION ERROR]';
        }
    }
}
