# SIMBAR API - Development Guide

## Quick Start

### 1. Mulai Development Server
```bash
cd c:\xampp\htdocs\layanan_web\SIMBAR-BackEnd
php artisan serve
```
Server akan berjalan di: `http://localhost:8000`

### 2. Test API dengan cURL atau Postman

#### Create Item
```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Smartphone",
    "description": "Latest Model",
    "price": 5000000,
    "quantity": 20,
    "status": "active"
  }'
```

#### Get All Items
```bash
curl http://localhost:8000/api/v1/items
```

#### Get Single Item
```bash
curl http://localhost:8000/api/v1/items/1
```

#### Update Item
```bash
curl -X PUT http://localhost:8000/api/v1/items/1 \
  -H "Content-Type: application/json" \
  -d '{
    "price": 5500000,
    "quantity": 15
  }'
```

#### Delete Item
```bash
curl -X DELETE http://localhost:8000/api/v1/items/1
```

## Project Structure

### Models (`app/Models/`)
- **Item.php** - Model untuk tabel items dengan mass assignment

### Controllers (`app/Http/Controllers/Api/`)
- **ItemController.php** - Controller dengan 5 resource methods:
  - `index()` - List semua items
  - `store()` - Create item baru
  - `show()` - Get item spesifik
  - `update()` - Update item
  - `destroy()` - Delete item

### Database (`database/`)
- **migrations/** - Schema definitions untuk database
  - `2026_06_20_130038_create_items_table.php` - Tabel items

### Routes (`routes/`)
- **api.php** - API routes dengan prefix `/api/v1`
- **web.php** - Web routes (jika diperlukan)

## Development Workflow

### 1. Membuat Resource Baru

Misalkan ingin membuat resource "Category":

```bash
# Create model dengan migration
php artisan make:model Category -m

# Create controller dengan resource methods
php artisan make:controller Api/CategoryController --api --model=Category
```

Update migration file `database/migrations/xxxx_create_categories_table.php`:
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->timestamps();
});
```

Update model `app/Models/Category.php`:
```php
protected $fillable = ['name', 'description'];
```

Update controller `app/Http/Controllers/Api/CategoryController.php` dengan business logic.

Add route di `routes/api.php`:
```php
Route::apiResource('categories', CategoryController::class);
```

Run migration:
```bash
php artisan migrate
```

### 2. Database Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset

# Fresh migration (refresh semua data)
php artisan migrate:fresh

# Show migration status
php artisan migrate:status
```

### 3. Artisan Commands Berguna

```bash
# List all routes
php artisan route:list

# Show detailed info tentang specific route
php artisan route:list --path=api

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Optimize application
php artisan optimize

# Generate API documentation (jika menggunakan Swagger)
php artisan scribe:generate
```

## Configuration Files

### Key Configurations

**`config/app.php`**
- `APP_NAME` - Nama aplikasi
- `APP_ENV` - Environment (local/production)
- `APP_DEBUG` - Enable/disable debug mode
- `APP_URL` - Base URL aplikasi

**`config/database.php`**
- Database connection configuration
- Default menggunakan SQLite

**`.env`**
- Environment variables
- Sensitive configuration (API keys, credentials)

## Error Handling

Controller sudah include try-catch untuk error handling:

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

## Testing API

### Menggunakan Postman
1. Import collection ke Postman
2. Set base URL: `http://localhost:8000/api/v1`
3. Test setiap endpoint

### Menggunakan cURL (Command Line)
Lihat bagian "Quick Start" di atas

### Menggunakan PHP artisan tinker (Interactive Shell)
```bash
php artisan tinker

# Create item
App\Models\Item::create(['name' => 'Test', 'price' => 100000, 'quantity' => 5]);

# Get items
App\Models\Item::all();

# Get specific item
App\Models\Item::find(1);

# Update item
App\Models\Item::find(1)->update(['price' => 150000]);

# Delete item
App\Models\Item::find(1)->delete();
```

## Production Checklist

- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Set `APP_ENV=production`
- [ ] Optimize autoloader: `composer dump-autoload --optimize`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Implement CORS (jika diperlukan)
- [ ] Setup proper error logging
- [ ] Implement rate limiting
- [ ] Add authentication/authorization
- [ ] Setup HTTPS
- [ ] Database backup strategy
- [ ] Monitoring dan alerting

## Troubleshooting

### Issue: "php artisan: command not found"
Solution: Pastikan sudah di direktori project dan PHP ada di PATH

### Issue: Database error saat migrate
Solution: 
- Cek `.env` database configuration
- Cek file permissions pada `database/` folder
- Run: `php artisan cache:clear`

### Issue: 404 Not Found untuk API endpoint
Solution:
- Run: `php artisan route:cache` dan `php artisan route:clear`
- Cek routing di `routes/api.php`

### Issue: CORS error
Solution: Install dan configure `laravel-cors` package:
```bash
composer require fruitcake/laravel-cors
```

## Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel API Documentation](https://laravel.com/api)
- [Eloquent ORM Guide](https://laravel.com/docs/eloquent)
- [RESTful API Best Practices](https://restfulapi.net/)

---
Last Updated: 2026-06-20
