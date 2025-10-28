<?php

namespace X402\LaravelX402\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use X402\LaravelX402\Support\PaymentRequirements;

class PaymentRequiredException extends Exception
{
    public function __construct(
        public readonly PaymentRequirements $paymentRequirements,
        string $message = 'Payment Required',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'x402Version' => config('x402.protocol_version'),
            'accepts' => [$this->paymentRequirements->toArray()],
            'error' => $this->getMessage(),
        ], 402, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Get the HTTP status code for this exception.
     */
    public function getStatusCode(): int
    {
        return 402;
    }
}
