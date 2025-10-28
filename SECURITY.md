# Security Policy

## Supported Versions

We release patches for security vulnerabilities. Which versions are eligible for receiving such patches depends on the CVSS v3.0 Rating:

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

**Please do not report security vulnerabilities through public GitHub issues.**

If you discover a security vulnerability within Laravel x402, please send an email to security@x402.org. All security vulnerabilities will be promptly addressed.

### What to Include

Please include the following information in your report:

- Type of issue (e.g., buffer overflow, SQL injection, cross-site scripting, etc.)
- Full paths of source file(s) related to the manifestation of the issue
- The location of the affected source code (tag/branch/commit or direct URL)
- Any special configuration required to reproduce the issue
- Step-by-step instructions to reproduce the issue
- Proof-of-concept or exploit code (if possible)
- Impact of the issue, including how an attacker might exploit it

### Response Timeline

- **Acknowledgment**: We will acknowledge receipt of your vulnerability report within 48 hours
- **Assessment**: We will assess the vulnerability and determine its impact within 5 business days
- **Fix**: We will work on a fix and release it as soon as possible
- **Disclosure**: We will coordinate disclosure with you after the fix is released

## Security Considerations

When using Laravel x402 in production:

### 1. Wallet Security
- **Never commit** wallet private keys or sensitive credentials
- Use environment variables for wallet addresses
- Regularly rotate API keys and credentials
- Use separate wallets for development and production

### 2. Network Configuration
- Always use HTTPS in production
- Enable SSL/TLS verification (`X402_VERIFY_SSL=true`)
- Test on testnet (Base Sepolia) before deploying to mainnet
- Validate network configurations before processing payments

### 3. Payment Validation
- Always verify payments server-side (never trust client data)
- Implement rate limiting to prevent abuse
- Monitor payment logs for suspicious activity
- Set appropriate payment timeouts

### 4. API Security
- Protect facilitator API endpoints
- Use secure communication channels
- Implement request signing if available
- Monitor API usage and errors

### 5. Environment Variables
- Never expose `.env` files
- Use secure storage for production credentials
- Rotate secrets regularly
- Use different credentials per environment

### 6. Logging and Monitoring
- Enable payment logging in production
- Monitor for failed payment attempts
- Set up alerts for unusual activity
- Regularly review security logs

### 7. Dependency Management
- Keep Laravel and PHP updated
- Regularly update package dependencies
- Monitor security advisories
- Use `composer audit` to check for vulnerabilities

### 8. Payment Amounts
- Validate payment amounts match expected values
- Implement maximum payment limits
- Monitor for unusual payment patterns
- Use atomic units to prevent decimal errors

### 9. Error Handling
- Don't expose sensitive information in error messages
- Log errors securely
- Return generic error messages to clients
- Handle exceptions gracefully

### 10. Testing
- Test payment flows thoroughly before production
- Use testnet for integration testing
- Validate error handling
- Test edge cases and failure scenarios

## Security Updates

Subscribe to security updates:
- Watch this repository on GitHub
- Follow [@x402protocol](https://twitter.com/x402protocol) on Twitter
- Subscribe to the mailing list (coming soon)

## Bug Bounty

We currently do not have a bug bounty program, but we greatly appreciate security researchers who responsibly disclose vulnerabilities.

## Contact

For security concerns, contact:
- Email: security@x402.org
- Emergency: security@x402.org (monitored 24/7)

## Acknowledgments

We thank the following security researchers for responsibly disclosing vulnerabilities:

- (List will be maintained here)

---

**Remember**: Security is a shared responsibility. Always follow best practices when handling payments and sensitive data.
