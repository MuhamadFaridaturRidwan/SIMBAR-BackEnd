<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * @group Items
     * @response 200 {
     *   "success": true,
     *   "message": "Items retrieved successfully",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Laptop",
     *       "description": "Gaming Laptop",
     *       "price": 15000000,
     *       "quantity": 5,
     *       "status": "active",
     *       "created_at": "2026-06-20T13:00:00.000000Z",
     *       "updated_at": "2026-06-20T13:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        try {
            $items = Item::all();
            return response()->json([
                'success' => true,
                'message' => 'Items retrieved successfully',
                'data' => $items
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @group Items
     * @bodyParam name string required Item name. Example: Laptop
     * @bodyParam description string Item description. Example: Gaming Laptop
     * @bodyParam price number required Item price. Example: 15000000
     * @bodyParam quantity integer required Item quantity. Example: 5
     * @bodyParam status string Item status (active/inactive). Example: active
     * @response 201 {
     *   "success": true,
     *   "message": "Item created successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop",
     *     "description": "Gaming Laptop",
     *     "price": 15000000,
     *     "quantity": 5,
     *     "status": "active",
     *     "created_at": "2026-06-20T13:00:00.000000Z",
     *     "updated_at": "2026-06-20T13:00:00.000000Z"
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'status' => 'nullable|string|in:active,inactive',
            ]);

            $item = Item::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item created successfully',
                'data' => $item
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
                'message' => 'Error creating item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * @group Items
     * @urlParam id required The ID of the item. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Item retrieved successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop",
     *     "description": "Gaming Laptop",
     *     "price": 15000000,
     *     "quantity": 5,
     *     "status": "active",
     *     "created_at": "2026-06-20T13:00:00.000000Z",
     *     "updated_at": "2026-06-20T13:00:00.000000Z"
     *   }
     * }
     */
    public function show(Item $item)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Item retrieved successfully',
                'data' => $item
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @group Items
     * @urlParam id required The ID of the item. Example: 1
     * @bodyParam name string Item name. Example: Laptop Pro
     * @bodyParam description string Item description. Example: Gaming Laptop Pro
     * @bodyParam price number Item price. Example: 18000000
     * @bodyParam quantity integer Item quantity. Example: 3
     * @bodyParam status string Item status (active/inactive). Example: active
     * @response 200 {
     *   "success": true,
     *   "message": "Item updated successfully",
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Pro",
     *     "description": "Gaming Laptop Pro",
     *     "price": 18000000,
     *     "quantity": 3,
     *     "status": "active",
     *     "created_at": "2026-06-20T13:00:00.000000Z",
     *     "updated_at": "2026-06-20T13:00:00.000000Z"
     *   }
     * }
     */
    public function update(Request $request, Item $item)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'price' => 'sometimes|numeric|min:0',
                'quantity' => 'sometimes|integer|min:0',
                'status' => 'nullable|string|in:active,inactive',
            ]);

            $item->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'data' => $item
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
                'message' => 'Error updating item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @group Items
     * @urlParam id required The ID of the item. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Item deleted successfully"
     * }
     */
    public function destroy(Item $item)
    {
        try {
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
