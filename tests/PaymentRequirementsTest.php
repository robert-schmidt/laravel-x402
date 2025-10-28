<?php

namespace X402\LaravelX402\Tests;

use X402\LaravelX402\Support\PaymentRequirements;

class PaymentRequirementsTest extends TestCase
{
    /** @test */
    public function it_can_create_payment_requirements_from_price()
    {
        $requirements = PaymentRequirements::fromPrice(
            price: '$0.01',
            resource: '/api/test',
        );

        $this->assertEquals('exact', $requirements->scheme);
        $this->assertEquals('base-sepolia', $requirements->network);
        $this->assertEquals('10000', $requirements->maxAmountRequired); // $0.01 = 10000 atomic units
        $this->assertEquals('/api/test', $requirements->resource);
        $this->assertEquals('0x1234567890123456789012345678901234567890', $requirements->payTo);
    }

    /** @test */
    public function it_converts_price_to_atomic_units_correctly()
    {
        $requirements1 = PaymentRequirements::fromPrice('$0.10', '/test');
        $this->assertEquals('100000', $requirements1->maxAmountRequired);

        $requirements2 = PaymentRequirements::fromPrice('$1.00', '/test');
        $this->assertEquals('1000000', $requirements2->maxAmountRequired);

        $requirements3 = PaymentRequirements::fromPrice('$0.001', '/test');
        $this->assertEquals('1000', $requirements3->maxAmountRequired);
    }

    /** @test */
    public function it_can_be_serialized_to_json()
    {
        $requirements = PaymentRequirements::fromPrice('$0.01', '/api/test');
        $json = json_encode($requirements);

        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertArrayHasKey('scheme', $decoded);
        $this->assertArrayHasKey('network', $decoded);
        $this->assertArrayHasKey('maxAmountRequired', $decoded);
        $this->assertArrayHasKey('resource', $decoded);
    }

    /** @test */
    public function it_throws_exception_for_missing_wallet_address()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Wallet address not configured');

        config(['x402.wallet_address' => null]);

        PaymentRequirements::fromPrice('$0.01', '/test');
    }

    /** @test */
    public function it_throws_exception_for_unknown_network()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown network');

        PaymentRequirements::fromPrice(
            price: '$0.01',
            resource: '/test',
            network: 'invalid-network'
        );
    }
}
