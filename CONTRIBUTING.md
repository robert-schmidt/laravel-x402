# Contributing to Laravel x402

Thank you for considering contributing to Laravel x402! This document outlines the process for contributing to this project.

## Code of Conduct

- Be respectful and inclusive
- Welcome newcomers and help them get started
- Focus on constructive feedback
- Assume good intentions

## How to Contribute

### Reporting Bugs

1. Check if the issue already exists in [GitHub Issues](https://github.com/robert-schmidt/laravel-x402/issues)
2. If not, create a new issue with:
   - Clear title and description
   - Steps to reproduce
   - Expected vs actual behavior
   - PHP/Laravel version
   - Code samples if applicable

### Suggesting Features

1. Open a GitHub issue with the `enhancement` label
2. Describe the feature and its use case
3. Explain why it would be valuable
4. Provide examples if possible

### Pull Requests

1. **Fork** the repository
2. **Create a branch** from `main`:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make your changes**:
   - Follow PSR-12 coding standards
   - Add tests for new features
   - Update documentation
4. **Run tests**:
   ```bash
   composer test
   ```
5. **Commit** with clear messages:
   ```bash
   git commit -m "Add feature: payment timeout configuration"
   ```
6. **Push** to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```
7. **Create a Pull Request** on GitHub

## Development Setup

### Prerequisites

- PHP 8.2+
- Composer
- Docker & Docker Compose (for testing)

### Local Development

```bash
# Clone the repository
git clone https://github.com/robert-schmidt/laravel-x402.git
cd laravel-x402

# Install dependencies
composer install

# Run tests
composer test

# Run code style checks
composer lint
```

### With Docker

```bash
# From the project root
make up
make shell
cd /var/www/laravel-x402
composer test
```

## Coding Standards

### PHP Standards

- Follow **PSR-12** coding style
- Use **strict types**: `declare(strict_types=1);`
- Add **type hints** to all parameters and return types
- Write **DocBlocks** for classes and methods

### Code Style Example

```php
<?php

declare(strict_types=1);

namespace X402\LaravelX402\Example;

/**
 * Example class demonstrating code style.
 */
class ExampleClass
{
    /**
     * Create a new instance.
     */
    public function __construct(
        private readonly string $value,
        private readonly int $count = 0,
    ) {
    }

    /**
     * Process the data.
     */
    public function process(array $data): array
    {
        // Implementation
        return $data;
    }
}
```

## Testing Guidelines

### Writing Tests

- **100% coverage** for new features
- Use **descriptive test names**: `it_validates_payment_requirements`
- Follow **AAA pattern**: Arrange, Act, Assert
- Test **both success and failure** cases

### Test Example

```php
/** @test */
public function it_validates_payment_requirements()
{
    // Arrange
    $requirements = PaymentRequirements::fromPrice('$0.01', '/test');

    // Act
    $result = $requirements->toArray();

    // Assert
    $this->assertArrayHasKey('maxAmountRequired', $result);
    $this->assertEquals('10000', $result['maxAmountRequired']);
}
```

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
vendor/bin/phpunit tests/PaymentRequirementsTest.php

# Run with coverage
vendor/bin/phpunit --coverage-html coverage
```

## Documentation

### Code Documentation

- Add DocBlocks to all public methods
- Include parameter descriptions
- Document exceptions
- Provide usage examples

### README Updates

- Update README.md for new features
- Add usage examples
- Update configuration options
- Keep the changelog up to date

## Git Workflow

### Branch Naming

- `feature/` - New features
- `fix/` - Bug fixes
- `docs/` - Documentation changes
- `refactor/` - Code refactoring
- `test/` - Test additions/changes

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
feat: add support for custom payment schemes
fix: resolve payment verification timeout
docs: update installation instructions
test: add tests for payment payload parsing
refactor: simplify middleware logic
```

## Release Process

1. Update version in `composer.json`
2. Update `CHANGELOG.md`
3. Create a git tag: `git tag v1.0.0`
4. Push tag: `git push --tags`
5. Create GitHub release with notes

## Questions?

- Open a [GitHub Discussion](https://github.com/robert-schmidt/laravel-x402/discussions)
- Check existing [Issues](https://github.com/robert-schmidt/laravel-x402/issues)

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to Laravel x402! 🚀
