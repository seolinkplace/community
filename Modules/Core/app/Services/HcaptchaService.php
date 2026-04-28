<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Http;

class HcaptchaService
{
    public function verify(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $response = Http::asForm()->post('https://api.hcaptcha.com/siteverify', [
            'secret'   => config('hcaptcha.secret'),
            'response' => $token,
        ]);

        return (bool) ($response->json('success') ?? false);
    }
}
