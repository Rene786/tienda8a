<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PayPalService
{
    public function hasSandboxCredentials(): bool
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');

        return config('services.paypal.environment') === 'sandbox'
            && is_string($clientId)
            && trim($clientId) !== ''
            && is_string($clientSecret)
            && trim($clientSecret) !== '';
    }

    /**
     * @return array{order_id: string, approval_url: string}
     */
    public function createOrder(Order $order): array
    {
        $response = Http::acceptJson()
            ->withToken($this->accessToken())
            ->timeout(15)
            ->post($this->apiUrl('/v2/checkout/orders'), [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $order->order_number,
                        'custom_id' => $order->order_number,
                        'amount' => [
                            'currency_code' => config('services.paypal.currency_code'),
                            'value' => number_format((float) $order->total, 2, '.', ''),
                        ],
                    ],
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'user_action' => 'PAY_NOW',
                    'shipping_preference' => 'NO_SHIPPING',
                    'return_url' => route('paypal.return'),
                    'cancel_url' => route('paypal.cancel', ['order' => $order->order_number]),
                ],
            ])
            ->throw()
            ->json();

        $orderId = $response['id'] ?? null;
        $approvalUrl = collect($response['links'] ?? [])
            ->firstWhere('rel', 'approve')['href'] ?? null;

        if (! is_string($orderId) || $orderId === '' || ! $this->isSandboxApprovalUrl($approvalUrl)) {
            throw new RuntimeException('PayPal no devolvió un enlace de aprobación de Sandbox válido.');
        }

        return [
            'order_id' => $orderId,
            'approval_url' => $approvalUrl,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function captureOrder(string $paypalOrderId): array
    {
        return Http::acceptJson()
            ->withToken($this->accessToken())
            ->timeout(15)
            ->withHeader('PayPal-Request-Id', $paypalOrderId.'-capture')
            ->send('POST', $this->apiUrl('/v2/checkout/orders/'.rawurlencode($paypalOrderId).'/capture'), [
                'json' => new \stdClass,
            ])
            ->throw()
            ->json();
    }

    private function accessToken(): string
    {
        if (! $this->hasSandboxCredentials()) {
            throw new RuntimeException('Configura el Client ID y el Secret de Sandbox de PayPal.');
        }

        $response = Http::acceptJson()
            ->asForm()
            ->withBasicAuth(
                (string) config('services.paypal.client_id'),
                (string) config('services.paypal.client_secret'),
            )
            ->timeout(15)
            ->post($this->apiUrl('/v1/oauth2/token'), [
                'grant_type' => 'client_credentials',
            ])
            ->throw()
            ->json();

        $accessToken = $response['access_token'] ?? null;

        if (! is_string($accessToken) || $accessToken === '') {
            throw new RuntimeException('PayPal no devolvió un token de acceso válido.');
        }

        return $accessToken;
    }

    private function apiUrl(string $path): string
    {
        return 'https://api-m.sandbox.paypal.com'.$path;
    }

    private function isSandboxApprovalUrl(mixed $url): bool
    {
        if (! is_string($url)) {
            return false;
        }

        $parsedUrl = parse_url($url);

        return ($parsedUrl['scheme'] ?? null) === 'https'
            && in_array($parsedUrl['host'] ?? null, ['www.sandbox.paypal.com', 'sandbox.paypal.com'], true);
    }
}
