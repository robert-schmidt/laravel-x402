<?php

namespace X402\LaravelX402\Facades;

use Illuminate\Support\Facades\Facade;
use X402\LaravelX402\Client\FacilitatorClient;

/**
 * @method static array verify(\X402\LaravelX402\Support\PaymentPayload $payment, \X402\LaravelX402\Support\PaymentRequirements $requirements)
 * @method static array settle(\X402\LaravelX402\Support\PaymentPayload $payment, \X402\LaravelX402\Support\PaymentRequirements $requirements)
 * @method static array getSupportedConfigurations()
 *
 * @see \X402\LaravelX402\Client\FacilitatorClient
 */
class X402 extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return FacilitatorClient::class;
    }
}
