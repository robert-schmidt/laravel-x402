# Changelog

All notable changes to `laravel-x402` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release
- x402 payment protocol middleware for Laravel
- Route protection with `x402` middleware
- Modern PHP 8.2+ attributes support (`#[RequiresPayment]`)
- Facilitator client for payment verification and settlement
- Support for multiple blockchain networks (Base, Ethereum, Sepolia)
- Comprehensive configuration system
- Built-in caching for payment verification
- Detailed logging support
- Payment requirements builder
- Payment payload parser
- Exception handling for payment errors
- Laravel facade for programmatic access
- Full test suite with PHPUnit
- Complete documentation and usage examples

### Security
- SSL verification for facilitator API calls
- Payment age validation
- Secure header parsing
- Input validation for all payment data

## [1.0.0] - 2025-01-XX

### Added
- First stable release
- Production-ready x402 payment integration
- Multi-network support (Base Sepolia, Base Mainnet, Ethereum)
- Comprehensive error handling
- Caching layer for performance
- Logging integration
- Full test coverage

### Changed
- N/A (initial release)

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

---

## Version Number Guidelines

- **MAJOR** version for incompatible API changes
- **MINOR** version for backwards-compatible functionality
- **PATCH** version for backwards-compatible bug fixes

## Release Process

1. Update this CHANGELOG
2. Update version in `composer.json`
3. Create git tag: `git tag v1.0.0`
4. Push tag: `git push --tags`
5. Create GitHub release

---

[Unreleased]: https://github.com/robert-schmidt/laravel-x402/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/robert-schmidt/laravel-x402/releases/tag/v1.0.0
