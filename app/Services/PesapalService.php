<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalService
{
    private string $baseUrl;
    private string $consumerKey;
    private string $consumerSecret;
    private ?string $ipnId;

    public function __construct()
    {
        $this->baseUrl        = config('pesapal.base_url');
        $this->consumerKey    = config('pesapal.consumer_key');
        $this->consumerSecret = config('pesapal.consumer_secret');
        $this->ipnId          = config('pesapal.ipn_id');
    }

    /**
     * Get OAuth token (cached for 4 minutes).
     */
    public function getToken(): string
    {
        return Cache::remember('pesapal_token', 240, function () {
            $response = Http::post("{$this->baseUrl}/api/Auth/RequestToken", [
                'consumer_key'    => $this->consumerKey,
                'consumer_secret' => $this->consumerSecret,
            ]);

            if (!$response->successful()) {
                Log::error('Pesapal token request failed', ['response' => $response->body()]);
                throw new \RuntimeException('Failed to obtain Pesapal auth token: ' . $response->body());
            }

            $data = $response->json();

            if (empty($data['token'])) {
                throw new \RuntimeException('Pesapal returned empty token');
            }

            return $data['token'];
        });
    }

    /**
     * Register IPN URL (run once via artisan command).
     */
    public function registerIPN(string $url): string
    {
        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])->post("{$this->baseUrl}/api/URLSetup/RegisterIPN", [
            'url'          => $url,
            'ipn_notification_type' => 'GET',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('IPN registration failed: ' . $response->body());
        }

        $data = $response->json();

        if (empty($data['ipn_id'])) {
            throw new \RuntimeException('No ipn_id in response: ' . $response->body());
        }

        return $data['ipn_id'];
    }

    /**
     * Submit an order to Pesapal.
     * Returns ['redirect_url' => string, 'order_tracking_id' => string]
     */
    public function submitOrder(array $data): array
    {
        $token = $this->getToken();

        $payload = [
            'id'                      => $data['merchant_reference'],
            'currency'                => $data['currency'] ?? 'UGX',
            'amount'                  => (float) $data['amount'],
            'description'             => $data['description'] ?? 'BebeMart Vote Payment',
            'callback_url'            => route('pesapal.callback'),
            'redirect_mode'           => 'PARENT_WINDOW',
            'notification_id'         => $this->ipnId,
            'branch'                  => 'BebeVotes',
            'billing_address' => [
                'email_address' => $data['email'] ?? '',
                'phone_number'  => $data['phone'] ?? '',
                'first_name'    => $data['first_name'] ?? '',
                'last_name'     => $data['last_name'] ?? '',
                'country_code'  => 'UG',
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])->post("{$this->baseUrl}/api/Transactions/SubmitOrderRequest", $payload);

        if (!$response->successful()) {
            Log::error('Pesapal submitOrder failed', ['payload' => $payload, 'response' => $response->body()]);
            throw new \RuntimeException('Failed to submit order to Pesapal: ' . $response->body());
        }

        $result = $response->json();

        if (empty($result['redirect_url'])) {
            throw new \RuntimeException('Pesapal did not return redirect_url: ' . $response->body());
        }

        return [
            'redirect_url'      => $result['redirect_url'],
            'order_tracking_id' => $result['order_tracking_id'] ?? null,
        ];
    }

    /**
     * Get transaction status by tracking ID.
     */
    public function getTransactionStatus(string $trackingId): array
    {
        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])->get("{$this->baseUrl}/api/Transactions/GetTransactionStatus", [
            'orderTrackingId' => $trackingId,
        ]);

        if (!$response->successful()) {
            Log::error('Pesapal getTransactionStatus failed', ['tracking_id' => $trackingId, 'response' => $response->body()]);
            throw new \RuntimeException('Failed to get transaction status: ' . $response->body());
        }

        return $response->json();
    }
}
