<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileValidator
{
    /**
     * Validate Cloudflare Turnstile Token
     */
    public static function validate(?string $token, ?string $ip = null): bool
    {
        $secretKey = config('services.cloudflare.turnstile_secret_key');

        // If Turnstile Secret Key is not configured in .env, bypass validation (for local/testing)
        if (empty($secretKey)) {
            return true;
        }

        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ip,
            ]);

            return $response->json('success') === true;
        } catch (\Throwable $e) {
            Log::error("Cloudflare Turnstile Validation Error: " . $e->getMessage());
            return false;
        }
    }
}
