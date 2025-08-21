# Nette Shop Starter

[![CI](https://github.com/moatez254/nette-shop-starte/actions/workflows/ci-php.yml/badge.svg)](https://github.com/moatez254/nette-shop-starte/actions/workflows/ci-php.yml)
[![Dependabot](https://img.shields.io/badge/Dependabot-enabled-025E8C)](./.github/dependabot.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![API Docs](https://img.shields.io/badge/OpenAPI-docs-blue)](https://moatez254.github.io/nette-shop-starte/)


Minimal REST API built with **Nette 3** and **SQLite** (products). Includes **Docker**, **PHPUnit**, **PHPStan**, and **GitHub Actions CI**.

---

## Features
- Nette 3 app (presenters + DI) with clean structure
- SQLite database with schema + seeds
- REST endpoints for products
- CI: PHPUnit, PHPStan (max), PHPCS (PSR-12)
- OpenAPI spec for quick exploration

---

## Endpoints
- `GET /api/products` — list
- `GET /api/products/{id}` — detail
- `POST /api/products` — body: `{ "name": "Lamp", "price": 49, "sku": "LAMP-003" }`

**OpenAPI:** import `openapi.yaml` into https://editor.swagger.io or open raw:  
`https://raw.githubusercontent.com/moatez254/nette-shop-starte/main/openapi.yaml`

---

## Quick start (Local PHP server)

```bash
cp .env.example .env
composer install

# SQLite files
mkdir -p var
sqlite3 var/database.sqlite < db/schema.sql
sqlite3 var/database.sqlite < db/seeds.sql

# run
php -S 0.0.0.0:8000 -t www
# open http://localhost:8000/api/products
