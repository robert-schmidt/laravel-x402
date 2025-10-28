<?php

namespace X402\LaravelX402\Support;

use JsonSerializable;

class PaymentRequirements implements JsonSerializable
{
    public function __construct(
        public readonly string $scheme,
        public readonly string $network,
        public readonly string $maxAmountRequired,
        public readonly string $resource,
        public readonly string $payTo,
        public readonly string $asset,
        public readonly int $maxTimeoutSeconds,
        public readonly string $mimeType = 'application/json',
        public readonly ?string $description = null,
    ) {
    }

    /**
     * Create payment requirements from a price string (e.g., "$0.01").
     */
    public static function fromPrice(
        string $price,
        string $resource,
        ?string $network = null,
        ?string $walletAddress = null,
        ?string $scheme = null,
    ): self {
        $network = $network ?? config('x402.defaults.network');
        $walletAddress = $walletAddress ?? config('x402.wallet_address');
        $scheme = $scheme ?? config('x402.defaults.scheme');

        if (empty($walletAddress)) {
            throw new \InvalidArgumentException('Wallet address not configured. Set X402_WALLET_ADDRESS in your .env file.');
        }

        // Convert price string to atomic units (assuming 6 decimals for USDC)
        $amount = self::parsePrice($price);

        $networkConfig = config("x402.networks.{$network}");
        if (!$networkConfig) {
            throw new \InvalidArgumentException("Unknown network: {$network}");
        }

        return new self(
            scheme: $scheme,
            network: $network,
            maxAmountRequired: (string) $amount,
            resource: $resource,
            payTo: $walletAddress,
            asset: $networkConfig['usdc_address'],
            maxTimeoutSeconds: config('x402.defaults.timeout_seconds'),
            mimeType: config('x402.defaults.mime_type'),
            description: "Payment required: {$price}",
        );
    }

    /**
     * Parse price string to atomic units.
     * E.g., "$0.01" -> 10000 (assuming 6 decimals)
     */
    private static function parsePrice(string $price): int
    {
        // Remove dollar sign and convert to float
        $price = ltrim($price, '$');
        $amount = (float) $price;

        // Convert to atomic units (6 decimals for USDC)
        return (int) ($amount * 1_000_000);
    }

    public function jsonSerialize(): array
    {
        return [
            'scheme' => $this->scheme,
            'network' => $this->network,
            'maxAmountRequired' => $this->maxAmountRequired,
            'resource' => $this->resource,
            'payTo' => $this->payTo,
            'asset' => $this->asset,
            'maxTimeoutSeconds' => $this->maxTimeoutSeconds,
            'mimeType' => $this->mimeType,
            'description' => $this->description,
        ];
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }
}
