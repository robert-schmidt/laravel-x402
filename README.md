# Laravel x402

[![Latest Version](https://img.shields.io/github/v/release/robert-schmidt/laravel-x402)](https://github.com/robert-schmidt/laravel-x402/releases)
[![License](https://img.shields.io/github/license/robert-schmidt/laravel-x402)](LICENSE)

**Laravel x402** is a production-ready package that brings the [x402 payment protocol](https://www.x402.org) to Laravel applications. Enable instant blockchain-based payments over HTTP with minimal code.

---

## 📖 What is x402?

**x402 is an open payment protocol** that enables instant cryptocurrency payments over HTTP using the standardized `402 Payment Required` status code.

### Key Concepts

**1. HTTP-Native Payments**
- Uses standard HTTP 402 status code (Payment Required)
- Works with existing web infrastructure
- No special blockchain nodes or complex integrations needed

**2. Instant Settlements**
- Payments settle in ~2 seconds on-chain
- Based on ERC-3009 TransferWithAuthorization (gasless for users)
- Zero protocol fees for both merchants and customers

**3. EVM Blockchain Support**
- **Currently supports ANY EVM-compatible blockchain** (Ethereum, Base, Polygon, Arbitrum, Optimism, etc.)
- This package comes **pre-configured** with 4 popular networks (see below)
- You can easily add any other EVM network by adding its configuration
- **Note:** Solana and other non-EVM chains are not yet supported (see [Solana Support](#-solana-support) below)

**4. Stablecoin-First**
- Designed for USDC payments (avoids price volatility)
- Can work with any ERC-3009 compatible token

---

## 🏗️ Architecture: How It Works

### The Payment Flow

```
┌─────────┐                    ┌─────────┐                    ┌─────────────┐
│  Client │                    │  Your   │                    │ Facilitator │
│         │                    │ Laravel │                    │   (x402)    │
└────┬────┘                    │   App   │                    └──────┬──────┘
     │                         └────┬────┘                           │
     │ 1. GET /api/premium          │                                │
     ├─────────────────────────────>│                                │
     │                              │                                │
     │ 2. 402 Payment Required      │                                │
     │    + Payment Instructions    │                                │
     │<─────────────────────────────┤                                │
     │                              │                                │
     │ 3. Sign Payment (ERC-3009)   │                                │
     │    with user's wallet        │                                │
     │                              │                                │
     │ 4. GET /api/premium          │                                │
     │    + X-PAYMENT header        │                                │
     ├─────────────────────────────>│                                │
     │                              │ 5. Verify Payment              │
     │                              ├───────────────────────────────>│
     │                              │                                │
     │                              │ 6. Valid ✓                     │
     │                              │<───────────────────────────────┤
     │                              │                                │
     │                              │ 7. Settle on Blockchain        │
     │                              ├───────────────────────────────>│
     │                              │                                │
     │                              │ 8. Transaction Hash            │
     │                              │<───────────────────────────────┤
     │                              │                                │
     │ 9. 200 OK + Content          │                                │
     │    + X-PAYMENT-RESPONSE      │                                │
     │<─────────────────────────────┤                                │
     │                              │                                │
```

### What is the Facilitator?

The **Facilitator** is a service that handles blockchain interactions so you don't have to:

**What it does:**
- ✅ Verifies payment signatures are valid
- ✅ Submits transactions to the blockchain
- ✅ Handles gas fees
- ✅ Manages nonces and replay protection
- ✅ Returns transaction hashes

**What you DON'T need:**
- ❌ No blockchain node to run
- ❌ No private key management for transactions
- ❌ No gas fee handling
- ❌ No complex Web3 code

**Default Facilitator:**
- Coinbase provides a free hosted facilitator at `https://facilitator.x402.org`
- Zero fees for USDC payments on Base network
- Production-ready and maintained by Coinbase

**Can I use a different facilitator?**
- Yes! The protocol is open
- You can run your own facilitator
- Or use any third-party facilitator
- Just change `X402_FACILITATOR_URL` in your `.env`

---

## ✨ Features

- 🚀 **Zero-friction payments** - Instant stablecoin payments in ~2 seconds
- 🔒 **Secure** - Built on ERC-3009 TransferWithAuthorization standard
- 🌐 **Multi-chain EVM support** - Works with ANY EVM blockchain (Solana coming soon)
- 💰 **No protocol fees** - Zero fees for merchants and customers
- 🎯 **Laravel-native** - Middleware, facades, and PHP 8 attributes
- ⚡ **Smart caching** - Reduces redundant facilitator API calls
- 📝 **Comprehensive logging** - Track all payment activities
- 🧪 **Fully tested** - Complete PHPUnit test coverage
- 🔧 **Extensible** - Add any blockchain network easily

---

## 📋 Requirements

- **PHP** 8.2 or higher
- **Laravel** 11.0 or 12.x
- **A wallet address** to receive payments (any EVM wallet)
- **Internet access** to the x402 facilitator

---

## 📦 Installation

### Step 1: Install Package

```bash
composer require robert-schmidt/laravel-x402
```

### or using git:

```bash
git clone https://github.com/robert-schmidt/laravel-x402.git .
```

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag=x402-config
```

This creates `config/x402.php` in your Laravel application.

### Step 3: Configure Your Wallet

Add your receiving wallet address to `.env`:

```env
X402_WALLET_ADDRESS=0xYourWalletAddressHere
```

**That's it!** You're ready to start accepting payments.

---

## ⚙️ Configuration

### Environment Variables

```env
# REQUIRED: Your wallet address (where payments go)
X402_WALLET_ADDRESS=0x1234567890123456789012345678901234567890

# OPTIONAL: Which blockchain to use (default: base-sepolia for testing)
X402_DEFAULT_NETWORK=base-sepolia

# OPTIONAL: Which facilitator to use (default: Coinbase's)
X402_FACILITATOR_URL=https://facilitator.x402.org

# OPTIONAL: Logging (default: true)
X402_LOGGING_ENABLED=true

# OPTIONAL: Caching (default: true, 5 minutes)
X402_CACHE_ENABLED=true
X402_CACHE_TTL=300
```

### Pre-Configured Networks

This package comes with **4 popular networks pre-configured**:

| Network | Type | Chain ID | Use Case |
|---------|------|----------|----------|
| **base-sepolia** | Testnet | 84532 | Development & Testing |
| **base-mainnet** | Mainnet | 8453 | Production (Coinbase L2) |
| **ethereum-sepolia** | Testnet | 11155111 | Ethereum Testing |
| **ethereum-mainnet** | Mainnet | 1 | Ethereum Production |

**These are just defaults!** You can add any EVM-compatible blockchain.

### Adding More Blockchain Networks

Want to use Polygon? Arbitrum? Optimism? Your own L2? **Easy!**

Open `config/x402.php` and add to the `networks` array:

```php
'networks' => [
    // ... existing networks ...

    // Add Polygon Mainnet
    'polygon-mainnet' => [
        'name' => 'Polygon Mainnet',
        'chain_id' => 137,
        'usdc_address' => '0x3c499c542cEF5E3811e1192ce70d8cC03d5c3359',
    ],

    // Add Arbitrum One
    'arbitrum-one' => [
        'name' => 'Arbitrum One',
        'chain_id' => 42161,
        'usdc_address' => '0xaf88d065e77c8cC2239327C5EDb3A432268e5831',
    ],

    // Add Optimism
    'optimism-mainnet' => [
        'name' => 'Optimism',
        'chain_id' => 10,
        'usdc_address' => '0x0b2C639c533813f4Aa9D7837CAf62653d097Ff85',
    ],

    // Add YOUR custom network
    'my-custom-chain' => [
        'name' => 'My Custom Chain',
        'chain_id' => 12345,
        'usdc_address' => '0x...', // Your USDC contract address
    ],
],
```

**That's all you need!** The package handles the rest.

### Wallet Setup by Blockchain

**You only need ONE wallet address** across all EVM chains (same address works everywhere).

**How to get a wallet address:**

1. **Use an existing wallet:**
   - MetaMask: `accounts[0]` address
   - Coinbase Wallet: Your wallet address
   - Hardware wallet (Ledger/Trezor): Your address
   - Any EVM wallet works!

2. **Create a new wallet for your app:**
   ```bash
   # Using cast (from Foundry)
   cast wallet new

   # Or use any wallet generator
   ```

3. **Important:** You only need the **public address** (not private keys) for receiving payments
   - ✅ Public address goes in `.env` → `X402_WALLET_ADDRESS`
   - ❌ Private keys stay secure (never in your app)

**The facilitator handles the transactions**, so you never expose private keys in your Laravel app!

---

## 🚀 Usage

### 1. Protect a Single Route (Simplest)

```php
use Illuminate\Support\Facades\Route;

Route::get('/premium-content', function () {
    return response()->json([
        'message' => 'You paid to see this!',
        'data' => 'Premium content here'
    ]);
})->middleware('x402:$0.01');
```

**That's it!** This route now requires a $0.01 USDC payment.

### 2. Using PHP 8 Attributes (Modern)

```php
use X402\LaravelX402\Attributes\RequiresPayment;

class ApiController extends Controller
{
    #[RequiresPayment('$0.05')]
    public function getData()
    {
        return response()->json(['data' => 'Protected data']);
    }

    #[RequiresPayment('$1.00', network: 'base-mainnet')]
    public function premiumEndpoint()
    {
        return response()->json(['premium' => true]);
    }
}
```

### 3. Protect Multiple Routes (Groups)

```php
Route::middleware(['x402:$0.01'])->group(function () {
    Route::get('/api/users', [UserController::class, 'index']);
    Route::get('/api/posts', [PostController::class, 'index']);
    Route::get('/api/comments', [CommentController::class, 'index']);
});
```

### 4. Different Prices for Different Routes

```php
Route::get('/api/basic', function () {
    return ['data' => 'Basic tier'];
})->middleware('x402:$0.01');

Route::get('/api/premium', function () {
    return ['data' => 'Premium tier'];
})->middleware('x402:$0.10');

Route::get('/api/enterprise', function () {
    return ['data' => 'Enterprise tier'];
})->middleware('x402:$5.00');
```

### 5. Using Different Networks

```php
// Use Base Mainnet
Route::get('/base-api', function () {
    return ['data' => 'On Base'];
})->middleware('x402:$0.01,base-mainnet');

// Use Polygon
Route::get('/polygon-api', function () {
    return ['data' => 'On Polygon'];
})->middleware('x402:$0.01,polygon-mainnet');

// Use your custom network
Route::get('/custom-api', function () {
    return ['data' => 'On custom chain'];
})->middleware('x402:$0.01,my-custom-chain');
```

### 6. Programmatic Usage (Advanced)

For custom payment logic:

```php
use X402\LaravelX402\Facades\X402;
use X402\LaravelX402\Support\PaymentPayload;
use X402\LaravelX402\Support\PaymentRequirements;

public function customPayment(Request $request)
{
    // Parse payment from header
    $payment = PaymentPayload::fromHeader($request->header('X-Payment'));

    // Define requirements
    $requirements = PaymentRequirements::fromPrice(
        price: '$0.10',
        resource: $request->fullUrl()
    );

    // Verify payment
    try {
        X402::verify($payment, $requirements);
        X402::settle($payment, $requirements);

        return response()->json(['success' => true]);
    } catch (\X402\LaravelX402\Exceptions\PaymentVerificationException $e) {
        return response()->json([
            'error' => 'Payment failed',
            'reason' => $e->getReason()
        ], 402);
    }
}
```

### 7. Check Supported Networks

```php
use X402\LaravelX402\Facades\X402;

// Get all supported (scheme, network) pairs from facilitator
$supported = X402::getSupportedConfigurations();

return response()->json($supported);
```

---

## 🔧 Advanced Configuration

### Caching

Reduce API calls to the facilitator:

```php
// config/x402.php
'cache' => [
    'enabled' => true,
    'ttl' => 300, // 5 minutes
    'prefix' => 'x402_payment',
],
```

### Logging

Track all payment activities:

```php
// config/x402.php
'logging' => [
    'enabled' => true,
    'channel' => 'stack', // Or any Laravel log channel
],
```

Logs include:
- Payment requests received
- Verification results
- Settlement transactions
- Errors and failures

### Timeouts

Configure how long payments are valid:

```php
// config/x402.php
'defaults' => [
    'timeout_seconds' => 300, // 5 minutes
],
```

### Security

```php
// config/x402.php
'security' => [
    'verify_ssl' => true, // Always true in production
    'max_payment_age' => 600, // 10 minutes
],
```

---

## 🔐 Security Considerations

### What You MUST Do

1. **Use HTTPS in production** - Required for secure payment headers
2. **Keep wallet address in `.env`** - Never hardcode
3. **Test on testnet first** - Use `base-sepolia` before `base-mainnet`
4. **Monitor payment logs** - Track suspicious activity
5. **Enable SSL verification** - Default in production

### What You DON'T Need to Worry About

1. ❌ **Private key management** - The facilitator handles transactions
2. ❌ **Gas fees** - The facilitator pays gas
3. ❌ **Nonce management** - Built into ERC-3009
4. ❌ **Replay attacks** - Protocol prevents them
5. ❌ **Running blockchain nodes** - Not required

### Best Practices

```env
# ✅ Good - Environment variable
X402_WALLET_ADDRESS=0x1234...

# ❌ Bad - Hardcoded in code
```

```php
// ✅ Good - Test on testnet first
X402_DEFAULT_NETWORK=base-sepolia

// Then switch to mainnet
X402_DEFAULT_NETWORK=base-mainnet
```

---

## 🧪 Testing

### Run Package Tests

```bash
cd vendor/robert-schmidt/laravel-x402
composer test
```

### Test Your Implementation

1. **Start with testnet:**
   ```env
   X402_DEFAULT_NETWORK=base-sepolia
   ```

2. **Create a test endpoint:**
   ```php
   Route::get('/test-payment', function () {
       return ['message' => 'Paid!'];
   })->middleware('x402:$0.01');
   ```

3. **Make a request without payment:**
   ```bash
   curl http://your-app.test/api/test-payment
   ```

   You'll get a `402 Payment Required` response with payment instructions.

4. **Use an x402 client to pay and access:**
   - See official x402 docs for client implementations
   - Or use the TypeScript client: `@x402/fetch`

---

## 💡 Use Cases

### API Monetization
```php
Route::prefix('api/v1')->middleware('x402:$0.001')->group(function () {
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/translate', [TranslateController::class, 'translate']);
});
```

### Content Paywalls
```php
Route::get('/articles/{id}', function ($id) {
    return Article::find($id);
})->middleware('x402:$0.05');
```

### AI Agent Services
```php
Route::post('/ai/generate', function (Request $request) {
    return AIService::generate($request->prompt);
})->middleware('x402:$0.10');
```

### Tiered Pricing
```php
Route::get('/api/free', fn() => ['tier' => 'free']);

Route::get('/api/basic', fn() => ['tier' => 'basic'])
    ->middleware('x402:$0.01');

Route::get('/api/premium', fn() => ['tier' => 'premium'])
    ->middleware('x402:$1.00');
```

---

## 📚 How Payments Work (Deep Dive)

### For Developers

1. **Client makes request** → Your Laravel app
2. **Middleware checks** → Is there an `X-PAYMENT` header?
3. **If no payment** → Return `402 Payment Required` with payment instructions
4. **If payment exists** → Parse and verify with facilitator
5. **Verification passes** → Settle payment on blockchain
6. **Return content** → With `X-PAYMENT-RESPONSE` header

### Payment Instructions Format

When returning `402`, the response includes:

```json
{
  "x402Version": 1,
  "accepts": [
    {
      "scheme": "exact",
      "network": "base-sepolia",
      "maxAmountRequired": "10000",
      "resource": "http://your-app.test/api/premium",
      "payTo": "0xYourWalletAddress",
      "asset": "0x036CbD53842c5426634e7929541eC2318f3dCF7e",
      "maxTimeoutSeconds": 300,
      "mimeType": "application/json"
    }
  ]
}
```

### Client Implementation

Clients must:
1. Receive 402 response
2. Sign payment using ERC-3009 (TransferWithAuthorization)
3. Retry request with `X-PAYMENT: <base64-encoded-signature>`
4. Receive content with `X-PAYMENT-RESPONSE` header containing transaction hash

**Client libraries available:**
- TypeScript: `@x402/fetch`
- See [CLIENT_EXAMPLE.md](../CLIENT_EXAMPLE.md) for implementation guides

---

## 🤝 Contributing

Contributions welcome! See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

---

## 📄 License

This package is open-source software licensed under the [MIT license](LICENSE).

---

## 🔗 Resources

- **x402 Protocol**: [x402.org](https://www.x402.org)
- **Coinbase Docs**: [docs.cdp.coinbase.com/x402](https://docs.cdp.coinbase.com/x402/welcome)
- **GitHub**: [robert-schmidt/laravel-x402](https://github.com/robert-schmidt/laravel-x402)
- **Issues**: [GitHub Issues](https://github.com/robert-schmidt/laravel-x402/issues)
- **Discussions**: [GitHub Discussions](https://github.com/robert-schmidt/laravel-x402/discussions)

---

## ❓ FAQ

**Q: Do I need to run a blockchain node?**
A: No! The facilitator handles all blockchain interactions.

**Q: Do I need different wallets for each blockchain?**
A: No! The same EVM wallet address works on all EVM chains (Ethereum, Base, Polygon, etc.).

**Q: How much does it cost?**
A: Zero protocol fees. Facilitator (Coinbase) is free for USDC on Base. Gas fees are paid by the facilitator.

**Q: Can I use this with other tokens besides USDC?**
A: Yes, but the token must support ERC-3009. USDC is recommended for stability.

**Q: Is this production-ready?**
A: Yes! Used by production applications. Always test on testnet first.

**Q: Can I use my own facilitator?**
A: Yes! Change `X402_FACILITATOR_URL` to point to your facilitator.

**Q: What if the facilitator goes down?**
A: The protocol is open - you can switch facilitators or run your own.

**Q: Does this work with Solana?**
A: Not yet. Currently only EVM-compatible blockchains are supported. See the [Solana Support](#-solana-support) section for details.

---

## 🔮 Solana Support

### Current Status: ⚠️ Not Supported

While the **x402 protocol itself is blockchain agnostic**, this Laravel package currently **only supports EVM-compatible blockchains** (Ethereum, Base, Polygon, Arbitrum, Optimism, etc.).

### Why Not Solana Yet?

The current implementation uses **ERC-3009 TransferWithAuthorization**, which is an Ethereum standard. Solana has a completely different architecture:

| Feature | EVM Chains | Solana |
|---------|-----------|--------|
| **Token Standard** | ERC-20 | SPL Tokens |
| **Address Format** | Hex (0x...) | Base58 |
| **Transaction Signing** | ERC-3009 | Solana native signatures |
| **USDC Address** | Contract address (0x8335...) | Token mint address |

### What Would Be Needed

To add Solana support, we would need:

1. **Solana Transaction Signing**: Implement Solana's native transaction format
2. **SPL Token Support**: Handle SPL USDC instead of ERC-20 USDC
3. **Address Validation**: Support base58-encoded Solana addresses
4. **Facilitator Support**: The x402 facilitator would need to support Solana verification and settlement
5. **Different Middleware Logic**: Separate code path for Solana vs EVM chains

### Example: Solana USDC Addresses

```php
// What Solana configuration might look like (not yet implemented):
'networks' => [
    'solana-mainnet' => [
        'name' => 'Solana Mainnet',
        'chain_type' => 'solana',
        'usdc_mint' => 'EPjFWdd5AufqSSqeM2qN1xzybapC8G4wEGGkZwyTDt1v',
        'rpc_url' => 'https://api.mainnet-beta.solana.com',
    ],
    'solana-devnet' => [
        'name' => 'Solana Devnet',
        'chain_type' => 'solana',
        'usdc_mint' => '4zMMC9srt5Ri5X14GAgXhaHii3GnPAEERYPJgZJDncDU',
        'rpc_url' => 'https://api.devnet.solana.com',
    ],
],
```

### Future Roadmap

Solana support is being considered for future releases. The x402 protocol is designed to be blockchain agnostic, and adding Solana would make this package even more versatile.

**Want Solana support? Help us prioritize!**
- 👍 Vote on the [GitHub Issue](https://github.com/robert-schmidt/laravel-x402/issues)
- 💬 Join the discussion in [GitHub Discussions](https://github.com/robert-schmidt/laravel-x402/discussions)
- 🤝 Consider contributing! See [CONTRIBUTING.md](CONTRIBUTING.md)
- ☕ [Buy me a coffee](https://coff.ee/robbschmidt) to support development

### Workaround for Now

If you need cryptocurrency payments on Solana today, you can:
1. **Use EVM chains**: Base network is extremely fast (~2s) and cheap
2. **Custom implementation**: Implement Solana logic separately from this package
3. **Bridge solutions**: Use cross-chain bridges between Solana and EVM chains
4. **Wait for updates**: Star the repo to get notified when Solana support is added

---

## 💖 Support This Project

If this package helps you, consider buying me a coffee!

[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20A%20Coffee-Support-yellow?style=for-the-badge&logo=buy-me-a-coffee)](https://coff.ee/robbschmidt)

---

## 👨‍💻 Author

**Robert Schmidt**
- Website: [robertschmidt.dev](https://robertschmidt.dev)
- Support: [Buy me a coffee](https://coff.ee/robbschmidt)
- GitHub: [robert-schmidt](https://github.com/robert-schmidt)

---

Built with ❤️ for the Laravel and Web3 communities

---

## 🚀 Quick Start Recap

```bash
# 1. Install
composer require robert-schmidt/laravel-x402

# 2. Publish config
php artisan vendor:publish --tag=x402-config

# 3. Set wallet in .env
X402_WALLET_ADDRESS=0xYourAddress

# 4. Protect a route
Route::get('/api/premium', fn() => ['data' => 'Paid!'])
    ->middleware('x402:$0.01');

# Done! 🎉
```

Your API now accepts cryptocurrency payments! 💰
