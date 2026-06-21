# SIMBAR API - How to Add More Resources

Panduan lengkap untuk menambahkan resource baru ke SIMBAR API.

## 📋 Prerequisites

Pastikan sudah familiar dengan:
- Laravel Eloquent ORM
- RESTful API concepts
- Migration & Model creation
- Controller & routing

---

## 🎯 Step-by-Step: Adding New Resource (Example: Category)

### Step 1: Generate Model dengan Migration

```bash
cd c:\xampp\htdocs\layanan_web\SIMBAR-BackEnd
php artisan make:model Category -m
```

Output:
```
Model created: App\Models\Category
Migration created: database/migrations/2026_06_20_xxxxxx_create_categories_table.php
```

### Step 2: Edit Migration File

File: `database/migrations/2026_06_20_xxxxxx_create_categories_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

### Step 3: Update Model

File: `app/Models/Category.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define relationships
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
```

### Step 4: Generate API Controller

```bash
php artisan make:controller Api/CategoryController --api --model=Category
```

### Step 5: Implement Controller Methods

File: `app/Http/Controllers/Api/CategoryController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        try {
            $categories = Category::all();
            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:active,inactive',
            ]);

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a specific category.
     */
    public function show(Category $category)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Category retrieved successfully',
                'data' => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a category.
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255|unique:categories,name,' . $category->id,
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:active,inactive',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

### Step 6: Add Routes

File: `routes/api.php`

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\CategoryController;

// ... existing routes ...

Route::prefix('v1')->group(function () {
    Route::apiResource('items', ItemController::class);
    Route::apiResource('categories', CategoryController::class);  // Add this line
});
```

### Step 7: Run Migration

```bash
php artisan migrate
```

Output:
```
Running migrations.
2026_06_20_xxxxxx_create_categories_table ..... 25.00ms DONE
```

### Step 8: Test the New Resource

```bash
# Create category
curl -X POST http://localhost:8000/api/v1/categories \
  -H "Content-Type: application/json" \
  -d '{"name":"Electronics","description":"Electronic products"}'

# Get all categories
curl http://localhost:8000/api/v1/categories

# Get specific category
curl http://localhost:8000/api/v1/categories/1
```

---

## 🔗 Adding Relationships

### Step 1: Update Item Model to Belong to Category

File: `app/Models/Item.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'category_id',  // Add this
        'name',
        'description',
        'price',
        'quantity',
        'status',
    ];

    // Add relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

### Step 2: Create Migration to Add Foreign Key

```bash
php artisan make:migration add_category_id_to_items_table --table=items
```

File: `database/migrations/2026_06_20_xxxxxx_add_category_id_to_items_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeignIdFor('categories');
        });
    }
};
```

### Step 3: Run Migration

```bash
php artisan migrate
```

### Step 4: Update ItemController

Add `category_id` to validation in `store()` and `update()` methods:

```php
$validated = $request->validate([
    'category_id' => 'nullable|exists:categories,id',  // Add this
    'name' => 'required|string|max:255',
    // ... rest of validation
]);
```

---

## 🛡️ Adding Form Request Validation

### Create Request Class

```bash
php artisan make:request StoreItemRequest
php artisan make:request UpdateItemRequest
```

### Update Request Class

File: `app/Http/Requests/StoreItemRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'price.required' => 'Product price is required',
            'price.numeric' => 'Price must be a valid number',
            'quantity.required' => 'Product quantity is required',
        ];
    }
}
```

### Update Controller to Use Request Class

```php
public function store(StoreItemRequest $request)
{
    try {
        $item = Item::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Item created successfully',
            'data' => $item
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error creating item',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

---

## 📊 Adding API Resources

### Create Resource Class

```bash
php artisan make:resource ItemResource
php artisan make:resource ItemCollection
```

### Update Resource Class

File: `app/Http/Resources/ItemResource.php`

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'status' => $this->status,
            'category' => new CategoryResource($this->category),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
```

### Use Resource in Controller

```php
public function index()
{
    try {
        $items = Item::all();
        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully',
            'data' => ItemResource::collection($items)
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving items',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

---

## 🔍 Adding Filtering & Search

Update controller to support query parameters:

```php
public function index(Request $request)
{
    try {
        $query = Item::query();

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Items retrieved successfully',
            'data' => $items->items(),
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error retrieving items',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

### Usage Examples

```bash
# Search
curl "http://localhost:8000/api/v1/items?search=laptop"

# Filter by category
curl "http://localhost:8000/api/v1/items?category_id=1"

# Filter by status
curl "http://localhost:8000/api/v1/items?status=active"

# Sort
curl "http://localhost:8000/api/v1/items?sort_by=price&sort_order=asc"

# Pagination
curl "http://localhost:8000/api/v1/items?page=2&per_page=10"

# Combine
curl "http://localhost:8000/api/v1/items?search=laptop&category_id=1&sort_by=price&sort_order=desc&page=1&per_page=20"
```

---

## 📋 Checklist untuk Menambah Resource Baru

- [ ] Buat Model dengan migration: `php artisan make:model ResourceName -m`
- [ ] Edit migration file dengan schema
- [ ] Update Model dengan `$fillable` dan relationships
- [ ] Generate API Controller: `php artisan make:controller Api/ResourceController --api`
- [ ] Implement controller methods (index, store, show, update, destroy)
- [ ] Add routes di `routes/api.php`
- [ ] Run migration: `php artisan migrate`
- [ ] Test dengan cURL/Postman
- [ ] (Optional) Create Form Request classes
- [ ] (Optional) Create API Resources
- [ ] (Optional) Add filtering/search support
- [ ] Update documentation

---

## 🧪 Quick Testing Script

```bash
#!/bin/bash

BASE_URL="http://localhost:8000/api/v1"

# Test new resource CRUD

echo "=== CREATE ==="
curl -X POST $BASE_URL/categories \
  -H "Content-Type: application/json" \
  -d '{"name":"Electronics","description":"Electronic products"}'

echo -e "\n=== READ ALL ==="
curl $BASE_URL/categories

echo -e "\n=== READ ONE ==="
curl $BASE_URL/categories/1

echo -e "\n=== UPDATE ==="
curl -X PUT $BASE_URL/categories/1 \
  -H "Content-Type: application/json" \
  -d '{"description":"Updated description"}'

echo -e "\n=== DELETE ==="
curl -X DELETE $BASE_URL/categories/1

echo -e "\nDone!"
```

---

## 🎯 Common Resource Examples

### User Resource
```bash
php artisan make:model User -m
# Fields: id, name, email, password, phone, address, status, created_at, updated_at
```

### Order Resource
```bash
php artisan make:model Order -m
# Fields: id, user_id, order_date, total_amount, status, created_at, updated_at
# Relationships: belongsTo User, hasMany OrderItem
```

### OrderItem Resource
```bash
php artisan make:model OrderItem -m
# Fields: id, order_id, item_id, quantity, price, created_at, updated_at
# Relationships: belongsTo Order, belongsTo Item
```

### Supplier Resource
```bash
php artisan make:model Supplier -m
# Fields: id, name, contact_name, phone, email, address, city, status, created_at, updated_at
# Relationships: hasMany Item
```

---

## 📚 Useful Commands Reference

```bash
# Generate
php artisan make:model ResourceName -m          # Model + Migration
php artisan make:migration migration_name       # Migration only
php artisan make:controller Api/ControllerName --api  # API Controller
php artisan make:request StoreResourceRequest   # Form Request
php artisan make:resource ResourceResource      # API Resource
php artisan make:seeder ResourceSeeder          # Seeder

# Database
php artisan migrate                # Run migrations
php artisan migrate:rollback      # Undo last migration
php artisan migrate:fresh         # Reset all migrations
php artisan migrate:status        # Show migration status

# Cache & Optimize
php artisan cache:clear           # Clear cache
php artisan route:clear           # Clear route cache
php artisan config:cache          # Cache config
php artisan route:cache           # Cache routes

# Debugging
php artisan tinker                # Interactive shell
php artisan route:list            # List all routes
php artisan serve                 # Start dev server
```

---

**Happy Coding! 🚀**

For more information, refer to:
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)
- [Official Laravel Documentation](https://laravel.com/docs)
