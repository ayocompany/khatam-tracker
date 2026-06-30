<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SagaWhatsappGatewayService
{
    public function sendText(string $phone, string $message): void
    {
        $this->send($phone, [
            'type' => 'text',
            'text' => $message,
        ]);
    }

    public function sendImage(string $phone, string $imageUrl, ?string $caption = null): void
    {
        $payload = [
            'type' => 'media',
            'mediaType' => 'image',
            'url' => $imageUrl,
        ];

        if ($caption !== null && $caption !== '') {
            $payload['caption'] = $caption;
        }

        $this->send($phone, $payload);
    }

    public function sendDocument(string $phone, string $documentUrl, ?string $caption = null, ?string $filename = null): void
    {
        $payload = [
            'type' => 'media',
            'mediaType' => 'document',
            'url' => $documentUrl,
        ];

        if ($caption !== null && $caption !== '') {
            $payload['caption'] = $caption;
        }

        if ($filename !== null && $filename !== '') {
            $payload['fileName'] = $filename;
            $payload['filename'] = $filename;
            $payload['documentName'] = $filename;
        }

        $this->send($phone, $payload);
    }

    /**
     * Send OTP message to phone. Returns provider metadata.
     */
    public function sendOtp(string $phone, string $otp): array
    {
        $message = "Kode OTP Quran Khatam Tracker kamu: {$otp}. Berlaku 5 menit. Jangan bagikan kode ini.";

        try {
            $this->sendText($phone, $message);

            return [
                'success' => true,
                'provider_message_id' => null,
                'simulated' => false,
            ];
        } catch (RuntimeException $e) {
            Log::warning('Saga send OTP failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'provider_message_id' => null,
                'simulated' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function send(string $phone, array $payload): void
    {
        $apiKey = config('services.saga_gateway.api_key');

        if (empty($apiKey)) {
            throw new RuntimeException('SAGA_GATEWAY_API_KEY belum dikonfigurasi.');
        }

        $response = Http::baseUrl(config('services.saga_gateway.base_url'))
            ->withToken($apiKey)
            ->acceptJson()
            ->post('/whatsapp/send', [
                'to' => $phone,
                ...$payload,
            ]);

        if ($response->failed()) {
            $message = $response->json('message') ?? $response->body();

            throw new RuntimeException(
                'Saga Gateway gagal mengirim pesan WhatsApp'
                .($message !== '' ? ': '.$message : '.'),
            );
        }
    }
}
