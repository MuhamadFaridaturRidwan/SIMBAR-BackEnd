<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     * @group Barang
     * @response 200 {
     *   "success": true,
     *   "message": "Barang retrieved successfully",
     *   "data": [
     *     {
     *       "id_barang": 1,
     *       "kode_barang": "SAF-007",
     *       "nama_barang": "Safety Vest Reflektif",
     *       "supplier": "PT Safety Indo",
     *       "kategori": "Safety",
     *       "lokasi": "Gudang C - Storage",
     *       "stok_saat_ini": 25,
     *       "stok_min": 5,
     *       "harga_satuan": 65000
     *     }
     *   ]
     * }
     */
    public function index()
    {
        try {
            $barang = Barang::all();
            return response()->json([
                'success' => true,
                'message' => 'Barang retrieved successfully',
                'data' => $barang
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @group Barang
     * @bodyParam kode_barang string required Kode barang. Example: SAF-007
     * @bodyParam nama_barang string required Nama barang. Example: Safety Vest Reflektif
     * @bodyParam supplier string Supplier. Example: PT Safety Indo
     * @bodyParam kategori string required Kategori. Example: Safety
     * @bodyParam lokasi string Lokasi. Example: Gudang C - Storage
     * @bodyParam stok_saat_ini integer required Stok saat ini. Example: 25
     * @bodyParam stok_min integer required Stok minimum. Example: 5
     * @bodyParam harga_satuan integer required Harga satuan. Example: 65000
     * @response 201 {
     *   "success": true,
     *   "message": "Barang created successfully",
     *   "data": {
     *     "id_barang": 1,
     *     "kode_barang": "SAF-007",
     *     "nama_barang": "Safety Vest Reflektif"
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'kode_barang' => 'required|string|max:50|unique:barang,kode_barang',
                'nama_barang' => 'required|string|max:100',
                'supplier' => 'nullable|string|max:100',
                'kategori' => 'required|string|max:50',
                'lokasi' => 'nullable|string|max:100',
                'stok_saat_ini' => 'required|integer|min:0',
                'stok_min' => 'required|integer|min:0',
                'harga_satuan' => 'required|integer|min:0',
            ]);

            $barang = Barang::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Barang created successfully',
                'data' => $barang
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * @group Barang
     * @urlParam id required ID barang. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Barang retrieved successfully",
     *   "data": {
     *     "id_barang": 1,
     *     "kode_barang": "SAF-007",
     *     "nama_barang": "Safety Vest Reflektif"
     *   }
     * }
     */
    public function show($id)
    {
        try {
            $barang = Barang::find($id);
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Barang retrieved successfully',
                'data' => $barang
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @group Barang
     * @urlParam id required ID barang. Example: 1
     * @bodyParam kode_barang string Kode barang. Example: SAF-007
     * @bodyParam nama_barang string Nama barang. Example: Safety Vest Reflektif
     * @bodyParam supplier string Supplier. Example: PT Safety Indo
     * @bodyParam kategori string Kategori. Example: Safety
     * @bodyParam lokasi string Lokasi. Example: Gudang C - Storage
     * @bodyParam stok_saat_ini integer Stok saat ini. Example: 25
     * @bodyParam stok_min integer Stok minimum. Example: 5
     * @bodyParam harga_satuan integer Harga satuan. Example: 65000
     * @response 200 {
     *   "success": true,
     *   "message": "Barang updated successfully",
     *   "data": {
     *     "id_barang": 1,
     *     "kode_barang": "SAF-007",
     *     "nama_barang": "Safety Vest Reflektif"
     *   }
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            $barang = Barang::find($id);
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang not found'
                ], 404);
            }

            $validated = $request->validate([
                'kode_barang' => 'sometimes|string|max:50|unique:barang,kode_barang,' . $id . ',id_barang',
                'nama_barang' => 'sometimes|string|max:100',
                'supplier' => 'nullable|string|max:100',
                'kategori' => 'sometimes|string|max:50',
                'lokasi' => 'nullable|string|max:100',
                'stok_saat_ini' => 'sometimes|integer|min:0',
                'stok_min' => 'sometimes|integer|min:0',
                'harga_satuan' => 'sometimes|integer|min:0',
            ]);

            $barang->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Barang updated successfully',
                'data' => $barang
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @group Barang
     * @urlParam id required ID barang. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Barang deleted successfully"
     * }
     */
    public function destroy($id)
    {
        try {
            $barang = Barang::find($id);
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang not found'
                ], 404);
            }

            $barang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Barang deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
