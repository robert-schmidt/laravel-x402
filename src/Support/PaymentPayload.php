<?php

namespace X402\LaravelX402\Support;

use JsonSerializable;

class PaymentPayload implements JsonSerializable
{
    public function __construct(
        public readonly int $x402Version,
        public readonly string $scheme,
        public readonly string $network,
        public readonly array $payload,
    ) {
    }

    /**
     * Parse payment payload from the X-PAYMENT header.
     */
    public static function fromHeader(string $header): self
    {
        $decoded = base64_decode($header, true);

        if ($decoded === false) {
            throw new \InvalidArgumentException('Invalid base64 encoding in X-PAYMENT header');
        }

        $data = json_decode($decoded, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON in X-PAYMENT header: ' . json_last_error_msg());
        }

        if (!isset($data['x402Version'], $data['scheme'], $data['network'], $data['payload'])) {
            throw new \InvalidArgumentException('Missing required fields in payment payload');
        }

        return new self(
            x402Version: $data['x402Version'],
            scheme: $data['scheme'],
            network: $data['network'],
            payload: $data['payload'],
        );
    }

    /**
     * Encode payment payload to base64 for X-PAYMENT header.
     */
    public function toHeader(): string
    {
        return base64_encode(json_encode($this->toArray()));
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        return [
            'x402Version' => $this->x402Version,
            'scheme' => $this->scheme,
            'network' => $this->network,
            'payload' => $this->payload,
        ];
    }
}
