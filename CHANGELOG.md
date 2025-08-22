# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Comprehensive test suite with Unit, Integration, and Feature tests
- ProductService with business logic layer
- ProductValidator with comprehensive validation rules
- Enhanced ProductRepository with CRUD operations
- Health check endpoint (`/api/health`)
- OpenAPI 3.0 specification with proper error responses
- GitHub Actions CI/CD pipeline with multiple PHP versions
- Code coverage reporting with Codecov integration
- Professional README.md with comprehensive documentation
- Pull request template for contributors
- Docker support with optimized configuration

### Changed
- Enhanced API error handling with proper HTTP status codes
- Improved input validation and sanitization
- Better database schema with created_at timestamp
- Refactored ApiPresenter with service layer integration
- Enhanced composer.json with better scripts and dependencies
- Improved phpunit.xml configuration with coverage settings

### Fixed
- SQL injection vulnerabilities in database queries
- Input validation issues
- Error response formatting
- Database connection handling

## [1.0.0] - 2024-01-15

### Added
- Initial release of Nette Shop Starter
- Basic REST API for products
- SQLite database integration
- Docker containerization
- Basic PHPUnit testing
- PHPStan static analysis
- GitHub Actions CI
- OpenAPI specification
- Basic documentation

### Features
- `GET /api/products` - List products
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get product by ID
- SQLite database with schema and seeds
- Docker Compose setup
- PSR-12 coding standards
- Basic error handling

## [0.2.0] - 2024-01-10

### Added
- PHPStan integration
- Enhanced error handling
- Better input validation

### Changed
- Improved code structure
- Enhanced documentation

## [0.1.0] - 2024-01-05

### Added
- Initial project setup
- Basic Nette 3 application structure
- SQLite database integration
- Basic product management
- Docker configuration

---

## Version History

- **1.0.0** - Professional release with comprehensive testing and CI/CD
- **0.2.0** - Enhanced code quality and validation
- **0.1.0** - Initial project foundation

## Migration Guide

### From 0.2.0 to 1.0.0

#### Breaking Changes
- API response format has been standardized
- Error responses now include proper HTTP status codes
- Product creation now requires validation

#### New Features
- Health check endpoint available at `/api/health`
- Enhanced validation with detailed error messages
- Pagination and search support for products
- Comprehensive test suite

#### Upgrading
1. Update your API client to handle new response formats
2. Implement proper error handling for validation failures
3. Update any hardcoded API endpoints if needed

### From 0.1.0 to 0.2.0

#### Breaking Changes
- None

#### New Features
- PHPStan static analysis
- Enhanced error handling

#### Upgrading
1. Run `composer update` to get new dependencies
2. Ensure your code passes PHPStan analysis

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details. 