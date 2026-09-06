<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WhatsappService
{
    public function createShareLink(string $phone, string $message): string
    {
        $cleanPhone = $this->formatPhone($phone);
        $encodedMessage = rawurlencode($message);

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }

    public function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return $digits;
    }

    public function sendDocument(string $phone, string $filePath, string $caption = ''): array
    {
        $cleanPhone = $this->formatPhone($phone);

        Log::info("WhatsApp Document queued for {$cleanPhone}: {$filePath} with caption: {$caption}");

        return [
            'status' => 'queued',
            'phone' => $cleanPhone,
            'file_path' => $filePath,
        ];
    }
}
