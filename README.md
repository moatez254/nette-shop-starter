# Nette Shop Starter

Minimal REST API with **Nette 3 + SQLite** (products), **Docker**, **PHPUnit**, **PHPStan**, and **CI**.

## Endpoints
- `GET /api/products` — list
- `GET /api/products/{id}` — detail
- `POST /api/products` — `{ "name": "...", "price": 9.99, "sku": "OPT" }`

## Run (Docker)
```bash
docker compose build
mkdir -p var && touch var/database.sqlite
docker compose up -d
docker compose exec app sh -lc "sqlite3 var/database.sqlite < db/schema.sql && sqlite3 var/database.sqlite < db/seeds.sql"
open http://localhost:8080/api/products
