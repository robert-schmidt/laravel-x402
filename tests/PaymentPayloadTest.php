<?php

namespace X402\LaravelX402\Tests;

use X402\LaravelX402\Support\PaymentPayload;

class PaymentPayloadTest extends TestCase
{
    /** @test */
    public function it_can_parse_payment_from_header()
    {
        $data = [
            'x402Version' => 1,
            'scheme' => 'exact',
            'network' => 'base-sepolia',
            'payload' => [
                'from' => '0xSender',
                'to' => '0xRecipient',
                'value' => '10000',
            ],
        ];

        $header = base64_encode(json_encode($data));

        $payment = PaymentPayload::fromHeader($header);

        $this->assertEquals(1, $payment->x402Version);
        $this->assertEquals('exact', $payment->scheme);
        $this->assertEquals('base-sepolia', $payment->network);
        $this->assertIsArray($payment->payload);
    }

    /** @test */
    public function it_can_encode_payment_to_header()
    {
        $payment = new PaymentPayload(
            x402Version: 1,
            scheme: 'exact',
            network: 'base-sepolia',
            payload: ['test' => 'data']
        );

        $header = $payment->toHeader();

        $this->assertIsString($header);
        $this->assertNotEmpty($header);

        // Verify it can be decoded back
        $decoded = json_decode(base64_decode($header), true);
        $this->assertEquals(1, $decoded['x402Version']);
        $this->assertEquals('exact', $decoded['scheme']);
    }

    /** @test */
    public function it_throws_exception_for_invalid_base64()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base64 encoding');

        PaymentPayload::fromHeader('not-valid-base64!!!');
    }

    /** @test */
    public function it_throws_exception_for_invalid_json()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid JSON');

        PaymentPayload::fromHeader(base64_encode('not json'));
    }

    /** @test */
    public function it_throws_exception_for_missing_required_fields()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required fields');

        $data = ['x402Version' => 1]; // Missing other required fields
        $header = base64_encode(json_encode($data));

        PaymentPayload::fromHeader($header);
    }

    /** @test */
    public function it_can_be_serialized_to_array()
    {
        $payment = new PaymentPayload(
            x402Version: 1,
            scheme: 'exact',
            network: 'base-sepolia',
            payload: ['key' => 'value']
        );

        $array = $payment->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('x402Version', $array);
        $this->assertArrayHasKey('scheme', $array);
        $this->assertArrayHasKey('network', $array);
        $this->assertArrayHasKey('payload', $array);
    }

    /** @test */
    public function it_round_trips_correctly()
    {
        $original = new PaymentPayload(
            x402Version: 1,
            scheme: 'exact',
            network: 'base-sepolia',
            payload: ['from' => '0xA', 'to' => '0xB', 'value' => '10000']
        );

        $header = $original->toHeader();
        $parsed = PaymentPayload::fromHeader($header);

        $this->assertEquals($original->x402Version, $parsed->x402Version);
        $this->assertEquals($original->scheme, $parsed->scheme);
        $this->assertEquals($original->network, $parsed->network);
        $this->assertEquals($original->payload, $parsed->payload);
    }
}
