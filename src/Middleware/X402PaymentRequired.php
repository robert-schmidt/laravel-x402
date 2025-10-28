<?php

namespace X402\LaravelX402\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use X402\LaravelX402\Client\FacilitatorClient;
use X402\LaravelX402\Exceptions\PaymentRequiredException;
use X402\LaravelX402\Exceptions\PaymentVerificationException;
use X402\LaravelX402\Support\PaymentPayload;
use X402\LaravelX402\Support\PaymentRequirements;

class X402PaymentRequired
{
    public function __construct(
        private FacilitatorClient $facilitator
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $price  Price in format "$0.01"
     * @param  string|null  $network  Blockchain network (optional, uses default)
     * @param  string|null  $scheme  Payment scheme (optional, uses default)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $price, ?string $network = null, ?string $scheme = null): Response
    {
        $paymentHeader = $request->header('X-Payment');

        // If no payment header, throw 402 Payment Required
        if (!$paymentHeader) {
            $this->log('No payment header found, returning 402', [
                'url' => $request->fullUrl(),
                'price' => $price,
            ]);

            throw $this->createPaymentRequiredException($request, $price, $network, $scheme);
        }

        // Parse payment payload
        try {
            $payment = PaymentPayload::fromHeader($paymentHeader);
        } catch (\InvalidArgumentException $e) {
            $this->log('Invalid payment header', [
                'error' => $e->getMessage(),
            ], 'error');

            return response()->json([
                'error' => 'Invalid payment header: ' . $e->getMessage(),
            ], 400);
        }

        // Create payment requirements
        $requirements = PaymentRequirements::fromPrice(
            price: $price,
            resource: $request->fullUrl(),
            network: $network,
            scheme: $scheme,
        );

        // Verify payment
        try {
            $verificationResult = $this->facilitator->verify($payment, $requirements);

            $this->log('Payment verification successful', [
                'url' => $request->fullUrl(),
                'price' => $price,
                'scheme' => $payment->scheme,
                'network' => $payment->network,
            ]);
        } catch (PaymentVerificationException $e) {
            $this->log('Payment verification failed', [
                'error' => $e->getMessage(),
                'reason' => $e->getReason(),
            ], 'error');

            return response()->json([
                'error' => 'Payment verification failed',
                'reason' => $e->getReason(),
            ], 402);
        }

        // Settle payment
        try {
            $settlementResult = $this->facilitator->settle($payment, $requirements);

            $this->log('Payment settlement successful', [
                'tx_hash' => $settlementResult['txHash'] ?? null,
                'network_id' => $settlementResult['networkId'] ?? null,
            ]);

            // Add payment response header
            $response = $next($request);

            $paymentResponse = base64_encode(json_encode([
                'success' => true,
                'txHash' => $settlementResult['txHash'] ?? null,
                'networkId' => $settlementResult['networkId'] ?? null,
            ]));

            return $response->header('X-Payment-Response', $paymentResponse);
        } catch (PaymentVerificationException $e) {
            $this->log('Payment settlement failed', [
                'error' => $e->getMessage(),
                'reason' => $e->getReason(),
            ], 'error');

            return response()->json([
                'error' => 'Payment settlement failed',
                'reason' => $e->getReason(),
            ], 500);
        }
    }

    /**
     * Create a PaymentRequiredException for the current request.
     */
    private function createPaymentRequiredException(
        Request $request,
        string $price,
        ?string $network,
        ?string $scheme
    ): PaymentRequiredException {
        $requirements = PaymentRequirements::fromPrice(
            price: $price,
            resource: $request->fullUrl(),
            network: $network,
            scheme: $scheme,
        );

        return new PaymentRequiredException($requirements);
    }

    /**
     * Log a message if logging is enabled.
     */
    private function log(string $message, array $context = [], string $level = 'info'): void
    {
        if (!config('x402.logging.enabled')) {
            return;
        }

        $channel = config('x402.logging.channel');
        $logger = $channel ? Log::channel($channel) : Log::getFacadeRoot();
        $logger->log($level, "[x402] {$message}", $context);
    }
}
