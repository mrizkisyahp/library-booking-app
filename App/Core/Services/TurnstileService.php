<?php

namespace App\Core\Services;

class TurnstileService
{
    public function __construct(
        private string $secretKey,
        private bool $enabled
    ) {
    }

    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        if (!$this->enabled) {
            return true;
        }
        if (!$token || !$this->secretKey) {
            error_log("Turnstile: Missing token ('$token') or secret key ('" . (empty($this->secretKey) ? 'EMPTY' : 'SET') . "')");
            return false;
        }
        $payload = http_build_query([
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
            ],
        ]);
        $verify = @file_get_contents(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            false,
            $context
        );
        if ($verify === false) {
            error_log("Turnstile: API request failed - " . error_get_last()['message'] ?? 'unknown');
            return false;
        }
        $result = json_decode($verify, true);
        error_log("Turnstile response: " . print_r($result, true));

        return ($result['success'] ?? false) === true;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}