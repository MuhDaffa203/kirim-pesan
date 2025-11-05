<?php

namespace App\Services\Encryption;

use Illuminate\Support\Facades\Log;

class PvdSteganography
{
    public function embedMessage($imagePath, $message, $outputPath)
    {

        $encodedMessage = base64_encode($message);

        $img = imagecreatefromstring(file_get_contents($imagePath));
        $width = imagesx($img);
        $height = imagesy($img);

        // Ubah ke biner
        $binary = '';
        foreach (str_split($encodedMessage) as $char) {
            $binary .= sprintf('%08b', ord($char));
        }

        // Tambahkan EOF marker (00000011)
        $binary .= '00000011';

        $dataIndex = 0;
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($dataIndex >= strlen($binary)) break 2;

                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // Sisipkan bit ke komponen biru
                $b = ($b & 0xFE) | $binary[$dataIndex++];
                $newColor = imagecolorallocate($img, $r, $g, $b);
                imagesetpixel($img, $x, $y, $newColor);
            }
        }

        // ✅ Simpan sebagai PNG (lossless)
        imagepng($img, $outputPath);
        imagedestroy($img);

        Log::info('✅ [STEGO] Pesan rahasia berhasil disisipkan ke gambar.', [
            'output' => $outputPath,
            'encoded_length' => strlen($encodedMessage)
        ]);
    }

    public function extractMessage($imagePath)
    {
        $img = imagecreatefromstring(file_get_contents($imagePath));
        $width = imagesx($img);
        $height = imagesy($img);

        $binary = '';
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($img, $x, $y);
                $b = $rgb & 1;
                $binary .= $b;
            }
        }
        imagedestroy($img);

        // Pisahkan setiap 8 bit → karakter
        $chars = str_split($binary, 8);
        $raw = '';
        foreach ($chars as $bin) {
            if ($bin === '00000011') break; // EOF marker
            $raw .= chr(bindec($bin));
        }

        // ✅ Decode dari Base64
        $decoded = base64_decode($raw, true);

        if ($decoded === false) {
            Log::warning('⚠️ [STEGO] Gagal decode base64, menampilkan raw data.');
            return $raw;
        }

        return $decoded;
    }
}
