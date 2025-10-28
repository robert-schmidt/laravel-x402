<?php

namespace X402\LaravelX402\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use X402\LaravelX402\Exceptions\PaymentVerificationException;
use X402\LaravelX402\Support\PaymentPayload;
use X402\LaravelX402\Support\PaymentRequirements;

class FacilitatorClient
{
    private Client $client;
    private string $baseUrl;
    private bool $loggingEnabled;
    private ?string $logChannel;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('x402.facilitator.base_url'), '/');
        $this->loggingEnabled = config('x402.logging.enabled', true);
        $this->logChannel = config('x402.logging.channel');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => config('x402.facilitator.timeout', 30),
            'verify' => config('x402.security.verify_ssl', true),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'laravel-x402/1.0',
            ],
        ]);
    }

    /**
     * Verify a payment with the facilitator.
     */
    public function verify(
        PaymentPayload $payment,
        PaymentRequirements $requirements
    ): array {
        $cacheKey = $this->getCacheKey('verify', $payment, $requirements);

        if (config('x402.cache.enabled') && Cache::has($cacheKey)) {
            $this->log('Payment verification cache hit', ['cache_key' => $cacheKey]);
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->client->post('/verify', [
                'json' => [
                    'x402Version' => config('x402.protocol_version'),
                    'paymentHeader' => $payment->toHeader(),
                    'paymentRequirements' => $requirements->toArray(),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!isset($result['isValid'])) {
                throw new PaymentVerificationException('Invalid response from facilitator');
            }

            if (!$result['isValid']) {
                $reason = $result['invalidReason'] ?? 'Unknown reason';
                throw new PaymentVerificationException(
                    "Payment verification failed: {$reason}",
                    $reason
                );
            }

            $this->log('Payment verified successfully', [
                'scheme' => $payment->scheme,
                'network' => $payment->network,
            ]);

            // Cache successful verification
            if (config('x402.cache.enabled')) {
                Cache::put($cacheKey, $result, config('x402.cache.ttl', 300));
            }

            return $result;
        } catch (GuzzleException $e) {
            $this->log('Payment verification failed', [
                'error' => $e->getMessage(),
            ], 'error');

            throw new PaymentVerificationException(
                'Failed to verify payment with facilitator: ' . $e->getMessage(),
                null,
                0,
                $e
            );
        }
    }

    /**
     * Settle a payment with the facilitator.
     */
    public function settle(
        PaymentPayload $payment,
        PaymentRequirements $requirements
    ): array {
        try {
            $response = $this->client->post('/settle', [
                'json' => [
                    'x402Version' => config('x402.protocol_version'),
                    'paymentHeader' => $payment->toHeader(),
                    'paymentRequirements' => $requirements->toArray(),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (!isset($result['success'])) {
                throw new PaymentVerificationException('Invalid response from facilitator');
            }

            if (!$result['success']) {
                $error = $result['error'] ?? 'Unknown error';
                throw new PaymentVerificationException("Payment settlement failed: {$error}", $error);
            }

            $this->log('Payment settled successfully', [
                'tx_hash' => $result['txHash'] ?? null,
                'network_id' => $result['networkId'] ?? null,
            ]);

            return $result;
        } catch (GuzzleException $e) {
            $this->log('Payment settlement failed', [
                'error' => $e->getMessage(),
            ], 'error');

            throw new PaymentVerificationException(
                'Failed to settle payment with facilitator: ' . $e->getMessage(),
                null,
                0,
                $e
            );
        }
    }

    /**
     * Get supported payment schemes and networks.
     */
    public function getSupportedConfigurations(): array
    {
        $cacheKey = config('x402.cache.prefix') . '_supported_configs';

        if (config('x402.cache.enabled') && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->client->get('/supported');
            $result = json_decode($response->getBody()->getContents(), true);

            // Cache for longer since this rarely changes
            if (config('x402.cache.enabled')) {
                Cache::put($cacheKey, $result, 3600); // 1 hour
            }

            return $result;
        } catch (GuzzleException $e) {
            $this->log('Failed to fetch supported configurations', [
                'error' => $e->getMessage(),
            ], 'error');

            // Return empty array as fallback
            return [];
        }
    }

    /**
     * Generate a cache key for payment verification.
     */
    private function getCacheKey(string $operation, PaymentPayload $payment, PaymentRequirements $requirements): string
    {
        $data = $operation . $payment->toHeader() . json_encode($requirements->toArray());
        return config('x402.cache.prefix') . '_' . md5($data);
    }

    /**
     * Log a message if logging is enabled.
     */
    private function log(string $message, array $context = [], string $level = 'info'): void
    {
        if (!$this->loggingEnabled) {
            return;
        }

        $logger = $this->logChannel ? Log::channel($this->logChannel) : Log::getFacadeRoot();
        $logger->log($level, "[x402] {$message}", $context);
    }
}
