# SIMBAR API - Backend Documentation

## Overview
SIMBAR API adalah backend application yang dibangun menggunakan Laravel 12 dengan struktur RESTful API.

## Teknologi
- **Framework**: Laravel 12
- **Database**: SQLite (default, dapat diubah ke MySQL/PostgreSQL)
- **PHP**: 8.2+
- **Server**: PHP Development Server

## Struktur Project

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── ItemController.php
│   │   └── Middleware/
│   ├── Models/
│   │   └── Item.php
├── database/
│   ├── migrations/
│   │   ├── 2026_06_20_130038_create_items_table.php
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   └── 0001_01_01_000002_create_jobs_table.php
├── routes/
│   ├── api.php (API routes)
│   └── web.php
└── config/
    ├── app.php
    ├── database.php
    └── ...
```

## Setup & Installation

### Requirements
- PHP 8.2 atau lebih tinggi
- Composer
- SQLite3 (atau MySQL/PostgreSQL untuk production)

### Installation Steps
```bash
# 1. Clone/Navigate to project
cd c:\xampp\htdocs\layanan_web\SIMBAR-BackEnd

# 2. Install dependencies (sudah dilakukan)
composer install

# 3. Generate app key (sudah dilakukan)
php artisan key:generate

# 4. Run migrations (sudah dilakukan)
php artisan migrate

# 5. Start development server
php artisan serve
```

## API Endpoints

### Base URL
```
http://localhost:8000/api/v1
```

### Items Resource

#### 1. GET - List All Items
```
GET /api/v1/items
```
**Response:**
```json
{
    "success": true,
    "message": "Items retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Item Name",
            "description": "Item Description",
            "price": 100000,
            "quantity": 10,
            "status": "active",
            "created_at": "2026-06-20T13:00:00.000000Z",
            "updated_at": "2026-06-20T13:00:00.000000Z"
        }
    ]
}
```

#### 2. POST - Create New Item
```
POST /api/v1/items
```
**Request Body:**
```json
{
    "name": "Laptop",
    "description": "Gaming Laptop",
    "price": 15000000,
    "quantity": 5,
    "status": "active"
}
```
**Response:**
```json
{
    "success": true,
    "message": "Item created successfully",
    "data": {
        "id": 1,
        "name": "Laptop",
        "description": "Gaming Laptop",
        "price": 15000000,
        "quantity": 5,
        "status": "active",
        "created_at": "2026-06-20T13:00:00.000000Z",
        "updated_at": "2026-06-20T13:00:00.000000Z"
    }
}
```

#### 3. GET - Get Specific Item
```
GET /api/v1/items/{id}
```
**Response:**
```json
{
    "success": true,
    "message": "Item retrieved successfully",
    "data": {
        "id": 1,
        "name": "Laptop",
        "description": "Gaming Laptop",
        "price": 15000000,
        "quantity": 5,
        "status": "active",
        "created_at": "2026-06-20T13:00:00.000000Z",
        "updated_at": "2026-06-20T13:00:00.000000Z"
    }
}
```

#### 4. PUT/PATCH - Update Item
```
PUT /api/v1/items/{id}
PATCH /api/v1/items/{id}
```
**Request Body:**
```json
{
    "name": "Laptop Pro",
    "price": 18000000,
    "quantity": 3
}
```
**Response:**
```json
{
    "success": true,
    "message": "Item updated successfully",
    "data": {
        "id": 1,
        "name": "Laptop Pro",
        "description": "Gaming Laptop",
        "price": 18000000,
        "quantity": 3,
        "status": "active",
        "created_at": "2026-06-20T13:00:00.000000Z",
        "updated_at": "2026-06-20T13:00:00.000000Z"
    }
}
```

#### 5. DELETE - Delete Item
```
DELETE /api/v1/items/{id}
```
**Response:**
```json
{
    "success": true,
    "message": "Item deleted successfully"
}
```

## Validation Rules

### Item Resource
- **name** (required): string, max 255 characters
- **description** (optional): string
- **price** (required): numeric, minimum 0
- **quantity** (required): integer, minimum 0
- **status** (optional): string, values: 'active' or 'inactive'

## Environment Configuration

File `.env`:
```
APP_NAME="SIMBAR API"
APP_ENV=local
APP_KEY=base64:vphhtBhfks7JbYMNem8Og6FHw1RqW1gKrxQn0yMDrkc=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# Untuk MySQL, uncomment dan sesuaikan:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=simbar_api
# DB_USERNAME=root
# DB_PASSWORD=
```

## Database Configuration

### Mengubah dari SQLite ke MySQL
Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simbar_api
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration ulang:
```bash
php artisan migrate:refresh
```

## Development Server

Jalankan server development:
```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

Untuk mengakses API:
```
http://localhost:8000/api/v1/items
```

## Common Commands

```bash
# Generate model dengan migration
php artisan make:model ModelName -m

# Generate controller
php artisan make:controller ControllerName

# Generate API controller (dengan resource methods)
php artisan make:controller Api/ControllerName --api

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migrations
php artisan migrate:fresh

# Show all routes
php artisan route:list

# Cache clear
php artisan cache:clear

# Config cache
php artisan config:cache
```

## Error Handling

API responses include standardized error handling:

```json
{
    "success": false,
    "message": "Error description",
    "error": "Detailed error message"
}
```

## Next Steps

1. **Authentication**: Implementasi Sanctum/Passport untuk API authentication
2. **Validation**: Tambahkan Form Request Validation classes
3. **Resources**: Gunakan API Resources untuk response transformation
4. **Testing**: Tambahkan unit tests dan feature tests
5. **Documentation**: Setup Swagger/OpenAPI documentation
6. **Logging**: Configure logging untuk production

## Notes

- Database saat ini menggunakan SQLite, ideal untuk development
- Untuk production, disarankan menggunakan MySQL atau PostgreSQL
- Implementasi CORS jika frontend dan backend di domain berbeda
- Tambahkan rate limiting untuk production
- Implementasi proper error logging dan monitoring

---
**Created**: 2026-06-20
**Framework**: Laravel 12
**API Version**: v1
