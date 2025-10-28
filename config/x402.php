<?php

return [
    /*
    |--------------------------------------------------------------------------
    | x402 Protocol Version
    |--------------------------------------------------------------------------
    |
    | The version of the x402 protocol to use.
    |
    */
    'protocol_version' => env('X402_PROTOCOL_VERSION', 1),

    /*
    |--------------------------------------------------------------------------
    | Facilitator Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the facilitator service that handles payment verification
    | and settlement. The default is Coinbase's hosted facilitator.
    |
    */
    'facilitator' => [
        'base_url' => env('X402_FACILITATOR_URL', 'https://facilitator.x402.org'),
        'timeout' => env('X402_FACILITATOR_TIMEOUT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Recipient Wallet
    |--------------------------------------------------------------------------
    |
    | The wallet address that will receive payments.
    |
    */
    'wallet_address' => env('X402_WALLET_ADDRESS'),

    /*
    |--------------------------------------------------------------------------
    | Default Payment Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for payment requirements.
    |
    */
    'defaults' => [
        'network' => env('X402_DEFAULT_NETWORK', 'base-sepolia'),
        'scheme' => env('X402_DEFAULT_SCHEME', 'exact'),
        'asset' => env('X402_DEFAULT_ASSET', '0x036CbD53842c5426634e7929541eC2318f3dCF7e'), // USDC on Base Sepolia
        'timeout_seconds' => env('X402_DEFAULT_TIMEOUT', 300),
        'mime_type' => env('X402_DEFAULT_MIME_TYPE', 'application/json'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Network Configurations
    |--------------------------------------------------------------------------
    |
    | Configure supported blockchain networks and their token addresses.
    |
    */
    'networks' => [
        'base-sepolia' => [
            'name' => 'Base Sepolia',
            'chain_id' => 84532,
            'usdc_address' => '0x036CbD53842c5426634e7929541eC2318f3dCF7e',
        ],
        'base-mainnet' => [
            'name' => 'Base Mainnet',
            'chain_id' => 8453,
            'usdc_address' => '0x833589fCD6eDb6E08f4c7C32D4f71b54bdA02913',
        ],
        'ethereum-mainnet' => [
            'name' => 'Ethereum Mainnet',
            'chain_id' => 1,
            'usdc_address' => '0xA0b86991c6218b36c1d19D4a2e9Eb0cE3606eB48',
        ],
        'ethereum-sepolia' => [
            'name' => 'Ethereum Sepolia',
            'chain_id' => 11155111,
            'usdc_address' => '0x1c7D4B196Cb0C7B01d743Fbc6116a902379C7238',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of payment requests and responses.
    |
    */
    'logging' => [
        'enabled' => env('X402_LOGGING_ENABLED', true),
        'channel' => env('X402_LOGGING_CHANNEL', 'stack'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Cache payment verifications to reduce facilitator calls.
    |
    */
    'cache' => [
        'enabled' => env('X402_CACHE_ENABLED', true),
        'ttl' => env('X402_CACHE_TTL', 300), // 5 minutes
        'prefix' => 'x402_payment',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Security settings for payment processing.
    |
    */
    'security' => [
        'verify_ssl' => env('X402_VERIFY_SSL', true),
        'max_payment_age' => env('X402_MAX_PAYMENT_AGE', 600), // 10 minutes
    ],
];
