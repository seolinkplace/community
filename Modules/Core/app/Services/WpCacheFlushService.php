<?php
namespace Modules\Core\Services;

use Modules\Core\Models\TenantToken;
use Illuminate\Support\Facades\Log;

class WpCacheFlushService
{
    public function flushUrl(string $domain, string $url, string $token): void
    {
        $siteUrl  = rtrim('https://' . $domain, '/');
        $endpoint = $siteUrl . '/wp-json/seolinkplace/v1/flush';
        $body     = json_encode(['url' => $url], JSON_UNESCAPED_SLASHES);
        $sig      = hash_hmac('sha256', $body, $token);
        try {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'X-Seohands-Signature: ' . $sig,
                ],
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            Log::info('[seolinkplace] flush url ' . $url . ': ' . $result);
        } catch (\Throwable $e) {
            Log::warning('[seolinkplace] flush url failed: ' . $e->getMessage());
        }
    }

    public function flushOrder(Order $order): void
    {
        if (!$order->donor_url || !$order->site) return;

        $tenantToken = TenantToken::where('site_id', $order->site_id)->first();
        if (!$tenantToken) return;

        $siteUrl  = rtrim('https://' . $order->site->domain, '/');
        $endpoint = $siteUrl . '/wp-json/seolinkplace/v1/flush';
        $body     = json_encode(['url' => $order->donor_url], JSON_UNESCAPED_SLASHES);
        $sig      = hash_hmac('sha256', $body, $tenantToken->token);

        try {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'X-Seohands-Signature: ' . $sig,
                ],
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
            Log::info('[seolinkplace] wp flush: ' . $result);
        } catch (\Throwable $e) {
            Log::warning('[seolinkplace] wp flush failed: ' . $e->getMessage());
        }
    }
}
