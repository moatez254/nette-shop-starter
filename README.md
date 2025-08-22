# Nette Shop Starter 🚀

[![CI/CD Pipeline](https://github.com/moatez254/nette-shop-starte/actions/workflows/ci-php.yml/badge.svg)](https://github.com/moatez254/nette-shop-starte/actions/workflows/ci-php.yml)
[![Code Coverage](https://img.shields.io/codecov/c/github/moatez254/nette-shop-starte?token=COVERAGE_TOKEN)](https://codecov.io/gh/moatez254/nette-shop-starte)
[![PHP Version](https://img.shields.io/badge/php-8.2%2B-blue.svg)](https://php.net)
[![Nette Version](https://img.shields.io/badge/nette-3.1%2B-green.svg)](https://nette.org)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![API Docs](https://img.shields.io/badge/OpenAPI-docs-blue)](https://moatez254.github.io/nette-shop-starte/)
[![Dependabot](https://img.shields.io/badge/Dependabot-enabled-025E8C)](./.github/dependabot.yml)

A **professional-grade REST API starter** built with **Nette 3** framework, featuring comprehensive testing, CI/CD pipeline, OpenAPI documentation, and Docker support.

## ✨ Features

- 🚀 **Nette 3 Framework** - Modern PHP framework with dependency injection
- 🗄️ **SQLite Database** - Lightweight, file-based database with migrations
- 🧪 **Comprehensive Testing** - PHPUnit with code coverage, PHPStan static analysis
- 🔄 **CI/CD Pipeline** - GitHub Actions with multiple PHP versions, security checks
- 📚 **OpenAPI 3.0** - Complete API documentation with Swagger UI
- 🐳 **Docker Support** - Single command deployment with docker-compose
- 📊 **Code Quality** - PSR-12 coding standards, PHPStan level max
- 🔒 **Security** - Input validation, error handling, security headers
- 📈 **Monitoring** - Health check endpoints, logging, error tracking

## 🚀 Quick Start

### Option 1: Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/moatez254/nette-shop-starte.git
cd nette-shop-starte

# Start everything with one command
docker-compose up -d

# API is now running at http://localhost:8000
# Swagger UI at http://localhost:8000/swagger
```

### Option 2: Local Development

```bash
# Clone and setup
git clone https://github.com/moatez254/nette-shop-starte.git
cd nette-shop-starte

# Install dependencies
composer install

# Environment setup
cp .env.example .env
# Edit .env with your database settings

# Create database
mkdir -p var
sqlite3 var/database.sqlite < db/schema.sql
sqlite3 var/database.sqlite < db/seeds.sql

# Run the application
php -S 0.0.0.0:8000 -t www
```

## 📚 API Endpoints

### Health Check
- `GET /api/health` - API health status

### Products
- `GET /api/products` - List products (with pagination & search)
- `POST /api/products` - Create new product
- `GET /api/products/{id}` - Get product details

### Query Parameters
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 20, max: 100)
- `q` - Search query for name or SKU

### Example Requests

```bash
# List products
curl http://localhost:8000/api/products

# Create product
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{"name": "Desk Lamp", "price": 49.99, "sku": "LAMP-001"}'

# Search products
curl "http://localhost:8000/api/products?q=lamp&page=1&limit=10"
```

## 🧪 Testing

```bash
# Run all tests
composer test

# Run tests with coverage
composer test:coverage

# Static analysis
composer stan

# Code style check
composer lint

# Fix code style issues
composer lint:fix

# Run all checks
composer check
```

## 🔧 Development Tools

### Composer Scripts
- `composer test` - Run PHPUnit tests
- `composer test:coverage` - Generate coverage reports
- `composer stan` - Run PHPStan static analysis
- `composer lint` - Check PSR-12 coding standards
- `composer lint:fix` - Auto-fix coding standards
- `composer check` - Run all quality checks
- `composer ci` - Run CI pipeline locally

### Code Quality
- **PHPUnit 10** - Unit testing framework
- **PHPStan** - Static analysis tool (level max)
- **PHP CodeSniffer** - PSR-12 coding standards
- **Code Coverage** - HTML, Clover, and text reports

## 🐳 Docker

### Services
- **PHP 8.3** - Application runtime
- **Nginx** - Web server with optimized configuration
- **SQLite** - Database (file-based)

### Commands
```bash
# Start services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down

# Rebuild and restart
docker-compose up -d --build

# Access container
docker-compose exec app bash
```

## 📊 CI/CD Pipeline

The project includes a comprehensive GitHub Actions workflow:

- **Multi-PHP Testing** - PHP 8.2 and 8.3 on Ubuntu
- **Code Quality** - PSR-12, PHPStan, security audit
- **Test Coverage** - Codecov integration with detailed reports
- **Docker Testing** - Container build and smoke tests
- **Security Checks** - Dependency vulnerability scanning
- **Artifact Storage** - Coverage reports and audit results

## 📖 API Documentation

### 📚 Live Documentation
- **Interactive API Docs**: https://moatez254.github.io/nette-shop-starter/ (auto-deployed)
- **OpenAPI Specification**: https://moatez254.github.io/nette-shop-starter/openapi.yaml

### 🔧 Setup GitHub Pages
To enable automatic API documentation deployment, follow the guide: [`GITHUB_PAGES_SETUP.md`](./GITHUB_PAGES_SETUP.md)

### OpenAPI Specification
- **File**: `openapi.yaml`
- **View Online**: Import into [Swagger Editor](https://editor.swagger.io/)
- **Raw URL**: `https://raw.githubusercontent.com/moatez254/nette-shop-starte/main/openapi.yaml`

### Swagger UI
Access interactive API documentation at `/swagger` when running the application.

## 🏗️ Project Structure

```
nette-shop-starte/
├── app/                    # Application code
│   ├── Model/             # Data models and repositories
│   ├── Presenters/        # API endpoints and controllers
│   ├── config/            # Configuration files
│   └── Router/            # Custom routing
├── tests/                 # Test suite
│   ├── Unit/              # Unit tests
│   ├── Integration/       # Integration tests
│   └── Feature/           # Feature tests
├── www/                   # Web root
├── var/                   # Runtime files (logs, temp, database)
├── db/                    # Database schema and seeds
├── .github/               # GitHub configuration
│   └── workflows/         # CI/CD workflows
├── docker-compose.yml     # Docker services
├── Dockerfile             # Application container
├── openapi.yaml           # API specification
├── phpunit.xml            # Test configuration
└── composer.json          # Dependencies and scripts
```

## 🔒 Security Features

- Input validation and sanitization
- SQL injection prevention
- XSS protection
- Proper HTTP status codes
- Error message sanitization
- Security headers

## 📈 Monitoring & Health

- Health check endpoint (`/api/health`)
- Structured logging
- Error tracking with Tracy
- Performance monitoring
- Database connection status

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Update OpenAPI documentation
- Ensure PHPStan passes (level max)
- Maintain code coverage above 80%

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [Nette Framework](https://nette.org) - Modern PHP framework
- [PHPUnit](https://phpunit.de) - Testing framework
- [PHPStan](https://phpstan.org) - Static analysis tool
- [GitHub Actions](https://github.com/features/actions) - CI/CD platform

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/moatez254/nette-shop-starte/issues)
- **Discussions**: [GitHub Discussions](https://github.com/moatez254/nette-shop-starte/discussions)
- **Documentation**: [Wiki](https://github.com/moatez254/nette-shop-starte/wiki)

---

**Made with ❤️ by the Nette Community**

[![Nette](https://nette.org/images/nette-logo.svg)](https://nette.org)
