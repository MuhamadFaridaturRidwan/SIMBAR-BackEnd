# SIMBAR API Backend

RESTful API backend untuk SIMBAR (Sistem Informasi Manajemen Barang/Inventaris) yang dibangun dengan Laravel 12.

## 📋 Overview

SIMBAR API adalah aplikasi backend yang menyediakan endpoints untuk manajemen inventory/barang. Dibangun dengan teknologi terkini dan mengikuti best practices RESTful API.

## 🚀 Teknologi

- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Database**: SQLite (development), MySQL/PostgreSQL (production-ready)
- **API Version**: v1
- **Architecture**: RESTful API

## 📦 Features

- ✅ RESTful API endpoints
- ✅ Comprehensive error handling
- ✅ Request validation
- ✅ Database migrations
- ✅ Structured response format
- ✅ Production-ready configuration
- ✅ Clean code architecture

## 🛠️ Installation & Setup

### Requirements
- PHP 8.2 atau lebih tinggi
- Composer
- SQLite3 (atau MySQL/PostgreSQL)

### Quick Start

1. **Navigate ke project**
```bash
cd c:\xampp\htdocs\layanan_web\SIMBAR-BackEnd
```

2. **Install dependencies** (sudah dilakukan)
```bash
composer install
```

3. **Generate application key** (sudah dilakukan)
```bash
php artisan key:generate
```

4. **Run migrations** (sudah dilakukan)
```bash
php artisan migrate
```

5. **Start development server**
```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000**

## 📚 Documentation

Documentasi lengkap tersedia di:
- [API_DOCUMENTATION.md](./API_DOCUMENTATION.md) - API Reference & Endpoints
- [DEVELOPMENT_GUIDE.md](./DEVELOPMENT_GUIDE.md) - Development Guide & Workflow

## 🔌 API Endpoints

Base URL: `http://localhost:8000/api/v1`

### Items Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/items` | Get all items |
| POST | `/items` | Create new item |
| GET | `/items/{id}` | Get specific item |
| PUT/PATCH | `/items/{id}` | Update item |
| DELETE | `/items/{id}` | Delete item |

### Example Request

```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop",
    "description": "Gaming Laptop",
    "price": 15000000,
    "quantity": 5,
    "status": "active"
  }'
```

### Response Format

Success Response:
```json
{
    "success": true,
    "message": "Item created successfully",
    "data": {...}
}
```

Error Response:
```json
{
    "success": false,
    "message": "Error message",
    "error": "Detailed error"
}
```

## 📂 Project Structure

```
SIMBAR-BackEnd/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   └── ItemController.php
│   │   └── Middleware/
│   ├── Models/
│   │   └── Item.php
├── database/
│   ├── migrations/
│   │   └── 2026_06_20_130038_create_items_table.php
├── routes/
│   ├── api.php
│   └── web.php
├── config/
├── storage/
├── vendor/
├── .env
├── .env.example
├── composer.json
└── artisan
```

## 🛣️ Routing

Semua API routes didefinisikan di `routes/api.php` dengan prefix `/api/v1`:

```php
Route::apiResource('items', ItemController::class);
```

Ini secara otomatis generate routes:
- `GET /api/v1/items` - index()
- `POST /api/v1/items` - store()
- `GET /api/v1/items/{id}` - show()
- `PUT/PATCH /api/v1/items/{id}` - update()
- `DELETE /api/v1/items/{id}` - destroy()

## 🗄️ Database

### Current Configuration
- **Connection**: SQLite (default, file: `database.sqlite`)
- **Ideal untuk**: Development & Testing

### Switch to MySQL (Production)

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simbar_api
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

Jalankan:
```bash
php artisan migrate:fresh
```

## 🔧 Useful Commands

```bash
# Start development server
php artisan serve

# Database commands
php artisan migrate              # Run migrations
php artisan migrate:fresh        # Reset & re-run migrations
php artisan migrate:rollback     # Undo last migration

# Model & Controller generation
php artisan make:model ModelName -m                    # Model + migration
php artisan make:controller Api/ControllerName --api   # API controller

# Route & Cache commands
php artisan route:list           # Show all routes
php artisan cache:clear          # Clear cache
php artisan config:cache         # Cache configuration

# Debugging
php artisan tinker               # Interactive shell
php artisan optimize             # Optimize application
```

## 🧪 Testing API

### Using cURL
```bash
# Get all items
curl http://localhost:8000/api/v1/items

# Create item
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{"name":"Item","price":100000,"quantity":5}'

# Get item
curl http://localhost:8000/api/v1/items/1

# Update item
curl -X PUT http://localhost:8000/api/v1/items/1 \
  -H "Content-Type: application/json" \
  -d '{"price":150000}'

# Delete item
curl -X DELETE http://localhost:8000/api/v1/items/1
```

### Using Postman
1. Import API collection
2. Set base URL: `http://localhost:8000/api/v1`
3. Test endpoints

## 📋 Item Schema

```javascript
{
    id: integer,
    name: string (required, max 255),
    description: string (optional),
    price: decimal (required, min 0),
    quantity: integer (required, min 0),
    status: enum['active', 'inactive'] (default: 'active'),
    created_at: timestamp,
    updated_at: timestamp
}
```

## ⚙️ Configuration

### Environment File (.env)
```env
APP_NAME="SIMBAR API"
APP_ENV=local
APP_KEY=base64:xxxxx
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
```

### Key Configuration Files
- `config/app.php` - Application settings
- `config/database.php` - Database configuration
- `config/cache.php` - Cache settings
- `.env` - Environment variables

## 🚀 Deployment

### Production Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Run `composer dump-autoload --optimize`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Setup HTTPS/SSL
- [ ] Configure proper logging
- [ ] Setup database backup
- [ ] Implement rate limiting
- [ ] Add authentication layer

## 🔐 Security

- Input validation implemented
- CSRF protection available
- SQL injection prevention (via Eloquent ORM)
- Environment variables for sensitive data
- Error handling without exposing stack traces

## 📝 Next Steps

1. Implement authentication (Sanctum/Passport)
2. Add more resources/models
3. Create API Resources for response transformation
4. Add comprehensive test coverage
5. Setup API documentation (Swagger/OpenAPI)
6. Implement logging and monitoring
7. Add rate limiting
8. Deploy to production server

## 📖 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel API Documentation](https://laravel.com/api)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [RESTful API Best Practices](https://restfulapi.net/)

## 📄 License

Licensed under the MIT license. See [LICENSE](LICENSE) file for details.

## 👨‍💻 Support

Untuk pertanyaan atau issues, silakan buka issue di repository ini.

---

**Created**: 2026-06-20  
**Version**: 1.0.0  
**Laravel Version**: 12  
**PHP Version**: 8.2+
