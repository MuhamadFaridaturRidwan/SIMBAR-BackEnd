# SIMBAR API - Testing Guide & Examples

## 🧪 How to Test API

### Prerequisites
- API server running: `php artisan serve` (http://localhost:8000)
- cURL atau Postman installed
- JSON format understanding

---

## 📝 cURL Examples

### 1️⃣ Create Item (POST)

```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Laptop Dell XPS 13",
    "description": "Premium laptop for professionals",
    "price": 18500000,
    "quantity": 5,
    "status": "active"
  }'
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Item created successfully",
    "data": {
        "id": 1,
        "name": "Laptop Dell XPS 13",
        "description": "Premium laptop for professionals",
        "price": 18500000,
        "quantity": 5,
        "status": "active",
        "created_at": "2026-06-20T13:00:00.000000Z",
        "updated_at": "2026-06-20T13:00:00.000000Z"
    }
}
```

---

### 2️⃣ Get All Items (GET)

```bash
curl http://localhost:8000/api/v1/items
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Items retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "Laptop Dell XPS 13",
            "description": "Premium laptop for professionals",
            "price": 18500000,
            "quantity": 5,
            "status": "active",
            "created_at": "2026-06-20T13:00:00.000000Z",
            "updated_at": "2026-06-20T13:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "iPhone 15 Pro",
            "description": "Latest Apple smartphone",
            "price": 19999000,
            "quantity": 10,
            "status": "active",
            "created_at": "2026-06-20T13:01:00.000000Z",
            "updated_at": "2026-06-20T13:01:00.000000Z"
        }
    ]
}
```

---

### 3️⃣ Get Specific Item (GET)

```bash
curl http://localhost:8000/api/v1/items/1
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Item retrieved successfully",
    "data": {
        "id": 1,
        "name": "Laptop Dell XPS 13",
        "description": "Premium laptop for professionals",
        "price": 18500000,
        "quantity": 5,
        "status": "active",
        "created_at": "2026-06-20T13:00:00.000000Z",
        "updated_at": "2026-06-20T13:00:00.000000Z"
    }
}
```

---

### 4️⃣ Update Item (PUT)

```bash
curl -X PUT http://localhost:8000/api/v1/items/1 \
  -H "Content-Type: application/json" \
  -d '{
    "price": 17500000,
    "quantity": 3,
    "status": "active"
  }'
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Item updated successfully",
    "data": {
        "id": 1,
        "name": "Laptop Dell XPS 13",
        "description": "Premium laptop for professionals",
        "price": 17500000,
        "quantity": 3,
        "status": "active",
        "created_at": "2026-06-20T13:00:00.000000Z",
        "updated_at": "2026-06-20T13:02:00.000000Z"
    }
}
```

---

### 5️⃣ Partial Update Item (PATCH)

```bash
curl -X PATCH http://localhost:8000/api/v1/items/1 \
  -H "Content-Type: application/json" \
  -d '{
    "quantity": 8
  }'
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Item updated successfully",
    "data": {
        "id": 1,
        "name": "Laptop Dell XPS 13",
        "description": "Premium laptop for professionals",
        "price": 17500000,
        "quantity": 8,
        "status": "active",
        "created_at": "2026-06-20T13:00:00.000000Z",
        "updated_at": "2026-06-20T13:03:00.000000Z"
    }
}
```

---

### 6️⃣ Delete Item (DELETE)

```bash
curl -X DELETE http://localhost:8000/api/v1/items/1
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Item deleted successfully"
}
```

---

## 🎯 Postman Testing

### Setup Postman

1. **Create New Collection** - Name: "SIMBAR API"

2. **Set Environment Variable**
   - Variable: `base_url`
   - Value: `http://localhost:8000`

3. **Create Requests**

#### Request 1: Create Item
```
Method: POST
URL: {{base_url}}/api/v1/items
Headers: Content-Type: application/json
Body (raw):
{
    "name": "Samsung Monitor 27",
    "description": "4K Gaming Monitor",
    "price": 3500000,
    "quantity": 15,
    "status": "active"
}
```

#### Request 2: Get All Items
```
Method: GET
URL: {{base_url}}/api/v1/items
```

#### Request 3: Get Item by ID
```
Method: GET
URL: {{base_url}}/api/v1/items/1
```

#### Request 4: Update Item
```
Method: PUT
URL: {{base_url}}/api/v1/items/1
Headers: Content-Type: application/json
Body (raw):
{
    "price": 3200000,
    "quantity": 12
}
```

#### Request 5: Delete Item
```
Method: DELETE
URL: {{base_url}}/api/v1/items/1
```

---

## ✅ Validation Rules Testing

### Test Case 1: Missing Required Field

```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "description": "Missing name and price",
    "quantity": 5
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": [
            "The name field is required."
        ],
        "price": [
            "The price field is required."
        ]
    }
}
```

---

### Test Case 2: Invalid Data Type

```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Invalid Item",
    "price": "not-a-number",
    "quantity": "five"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "price": [
            "The price must be a number."
        ],
        "quantity": [
            "The quantity must be an integer."
        ]
    }
}
```

---

### Test Case 3: Out of Range Value

```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Negative Price Item",
    "price": -5000,
    "quantity": -2,
    "status": "invalid_status"
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "price": [
            "The price must be at least 0."
        ],
        "quantity": [
            "The quantity must be at least 0."
        ],
        "status": [
            "The status must be one of the following values: active, inactive."
        ]
    }
}
```

---

### Test Case 4: Name Too Long

```bash
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{
    "name": "This is a very long product name that exceeds the maximum character limit of 255 characters which is set in the validation rules of the Item model and should be rejected by the API validation system",
    "price": 100000,
    "quantity": 5
  }'
```

**Expected Response (422 Unprocessable Entity):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": [
            "The name must not be greater than 255 characters."
        ]
    }
}
```

---

## 🔍 Test Scenarios

### Scenario 1: Create and Retrieve Items

```bash
# Step 1: Create Item 1
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{"name":"Item 1","price":100000,"quantity":10,"status":"active"}'

# Step 2: Create Item 2
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{"name":"Item 2","price":200000,"quantity":20,"status":"active"}'

# Step 3: Get All Items
curl http://localhost:8000/api/v1/items

# Step 4: Get Specific Item
curl http://localhost:8000/api/v1/items/1
```

---

### Scenario 2: Update and Delete

```bash
# Step 1: Update Item 1
curl -X PUT http://localhost:8000/api/v1/items/1 \
  -H "Content-Type: application/json" \
  -d '{"price":150000,"quantity":8}'

# Step 2: Get Updated Item
curl http://localhost:8000/api/v1/items/1

# Step 3: Delete Item 1
curl -X DELETE http://localhost:8000/api/v1/items/1

# Step 4: Try to Get Deleted Item (Should fail)
curl http://localhost:8000/api/v1/items/1
```

---

### Scenario 3: Full CRUD Workflow

```bash
# CREATE
echo "=== CREATE ===" 
curl -X POST http://localhost:8000/api/v1/items \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Item","description":"Test Description","price":250000,"quantity":5,"status":"active"}'

# READ ALL
echo -e "\n=== READ ALL ==="
curl http://localhost:8000/api/v1/items

# READ ONE
echo -e "\n=== READ ONE ==="
curl http://localhost:8000/api/v1/items/1

# UPDATE
echo -e "\n=== UPDATE ==="
curl -X PUT http://localhost:8000/api/v1/items/1 \
  -H "Content-Type: application/json" \
  -d '{"price":300000}'

# DELETE
echo -e "\n=== DELETE ==="
curl -X DELETE http://localhost:8000/api/v1/items/1

echo -e "\n=== VERIFY DELETED ==="
curl http://localhost:8000/api/v1/items
```

---

## 🐛 Debugging Tips

### 1. Check Server Status
```bash
# Is server running?
curl http://localhost:8000

# Should return 200 if running
```

### 2. Check Specific Route
```bash
# List all routes
cd c:\xampp\htdocs\layanan_web\SIMBAR-BackEnd
php artisan route:list

# Filter by api
php artisan route:list | grep api
```

### 3. Check Database
```bash
# Access tinker shell
php artisan tinker

# Get all items
App\Models\Item::all()

# Create item
App\Models\Item::create(['name' => 'Test', 'price' => 100000, 'quantity' => 5])

# Count items
App\Models\Item::count()

# Delete all items
App\Models\Item::truncate()
```

### 4. Check Logs
```bash
# View recent logs
tail -f storage/logs/laravel.log

# Or in Windows PowerShell
Get-Content storage/logs/laravel.log -Wait
```

---

## 📊 Performance Testing

### Simple Load Test

```bash
# Using ApacheBench (if installed)
ab -n 100 -c 10 http://localhost:8000/api/v1/items

# Using cURL in loop
for i in {1..10}; do curl http://localhost:8000/api/v1/items; done
```

---

## 🔐 Security Testing

### Test CORS (if needed later)
```bash
curl -H "Origin: http://frontend.local" \
     -H "Access-Control-Request-Method: POST" \
     -H "Access-Control-Request-Headers: Content-Type" \
     -X OPTIONS http://localhost:8000/api/v1/items
```

---

## 📝 Response Time Expectations

| Operation | Expected Time |
|-----------|---------------|
| GET /items | < 100ms |
| POST /items | < 200ms |
| GET /items/{id} | < 50ms |
| PUT /items/{id} | < 150ms |
| DELETE /items/{id} | < 100ms |

---

## ✨ Tips for Effective Testing

1. **Save responses** - Copy response JSON for reference
2. **Test edge cases** - Empty strings, null values, large numbers
3. **Test validation** - Missing fields, wrong types, out of range
4. **Test sequences** - Create → Read → Update → Delete
5. **Test error scenarios** - Non-existent IDs, invalid data
6. **Monitor logs** - Check Laravel logs for errors
7. **Use tools** - Postman, Insomnia, or VS Code extensions

---

## 🎯 Testing Checklist

- [ ] Create item with all fields
- [ ] Create item with minimal fields
- [ ] Get all items (empty & non-empty list)
- [ ] Get specific item (existing & non-existing)
- [ ] Update item (all fields & partial)
- [ ] Delete item
- [ ] Validate required fields
- [ ] Validate data types
- [ ] Validate min/max values
- [ ] Validate enum values
- [ ] Check error messages are clear
- [ ] Check response format is consistent
- [ ] Check timestamps are correct
- [ ] Test concurrent requests
- [ ] Test with large payloads

---

**Happy Testing! 🚀**

For more information, see:
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- [DEVELOPMENT_GUIDE.md](DEVELOPMENT_GUIDE.md)
