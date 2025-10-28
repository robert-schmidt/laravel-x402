<?php

namespace X402\LaravelX402\Exceptions;

use Exception;

class PaymentVerificationException extends Exception
{
    public function __construct(
        string $message = 'Payment verification failed',
        public readonly ?string $reason = null,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the verification failure reason.
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }
}
