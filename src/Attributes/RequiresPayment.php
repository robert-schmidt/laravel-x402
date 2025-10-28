<?php

namespace X402\LaravelX402\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class RequiresPayment
{
    public function __construct(
        public readonly string $price,
        public readonly ?string $network = null,
        public readonly ?string $scheme = null,
    ) {
    }

    /**
     * Get the middleware string for route registration.
     */
    public function getMiddleware(): string
    {
        $params = [$this->price];

        if ($this->network !== null) {
            $params[] = $this->network;
        }

        if ($this->scheme !== null) {
            $params[] = $this->scheme;
        }

        return 'x402:' . implode(',', $params);
    }
}
