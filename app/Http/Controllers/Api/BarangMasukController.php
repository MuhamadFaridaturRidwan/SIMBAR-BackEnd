<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     * @group Barang Masuk
     * @response 200 {
     *   "success": true,
     *   "message": "Barang masuk retrieved successfully",
     *   "data": [
     *     {
     *       "id_masuk": 1,
     *       "id_barang": 7,
     *       "tanggal_masuk": "2026-04-20T10:00:00.000000Z",
     *       "jumlah": 100,
     *       "referensi": "PO-2026-001",
     *       "catatan": "Pembelian rutin bulanan",
     *       "oleh": "Admin User"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        try {
            $barangMasuk = BarangMasuk::with('barang')->get();
            return response()->json([
                'success' => true,
                'message' => 'Barang masuk retrieved successfully',
                'data' => $barangMasuk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barang masuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @group Barang Masuk
     * @bodyParam id_barang integer required ID barang. Example: 7
     * @bodyParam tanggal_masuk date required Tanggal masuk. Example: 2026-04-20
     * @bodyParam jumlah integer required Jumlah. Example: 100
     * @bodyParam referensi string Referensi. Example: PO-2026-001
     * @bodyParam catatan string Catatan. Example: Pembelian rutin bulanan
     * @bodyParam oleh string Oleh. Example: Admin User
     * @response 201 {
     *   "success": true,
     *   "message": "Barang masuk created successfully",
     *   "data": {
     *     "id_masuk": 1,
     *     "id_barang": 7,
     *     "jumlah": 100
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_barang' => 'required|integer|exists:barang,id_barang',
                'tanggal_masuk' => 'required|date',
                'jumlah' => 'required|integer|min:1',
                'referensi' => 'nullable|string|max:50',
                'catatan' => 'nullable|string',
                'oleh' => 'nullable|string|max:50',
            ]);

            DB::beginTransaction();
            
            $barangMasuk = BarangMasuk::create($validated);
            
            // Update stock
            $barang = Barang::find($validated['id_barang']);
            if ($barang) {
                $barang->stok_saat_ini += $validated['jumlah'];
                $barang->save();
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang masuk created successfully',
                'data' => $barangMasuk->load('barang')
            ], 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating barang masuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * @group Barang Masuk
     * @urlParam id required ID barang masuk. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Barang masuk retrieved successfully",
     *   "data": {
     *     "id_masuk": 1,
     *     "id_barang": 7,
     *     "jumlah": 100
     *   }
     * }
     */
    public function show($id)
    {
        try {
            $barangMasuk = BarangMasuk::with('barang')->find($id);
            if (!$barangMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang masuk not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Barang masuk retrieved successfully',
                'data' => $barangMasuk
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barang masuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @group Barang Masuk
     * @urlParam id required ID barang masuk. Example: 1
     * @bodyParam id_barang integer ID barang. Example: 7
     * @bodyParam tanggal_masuk date Tanggal masuk. Example: 2026-04-20
     * @bodyParam jumlah integer Jumlah. Example: 100
     * @bodyParam referensi string Referensi. Example: PO-2026-001
     * @bodyParam catatan string Catatan. Example: Pembelian rutin bulanan
     * @bodyParam oleh string Oleh. Example: Admin User
     * @response 200 {
     *   "success": true,
     *   "message": "Barang masuk updated successfully",
     *   "data": {
     *     "id_masuk": 1,
     *     "id_barang": 7,
     *     "jumlah": 100
     *   }
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            $barangMasuk = BarangMasuk::find($id);
            if (!$barangMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang masuk not found'
                ], 404);
            }

            $validated = $request->validate([
                'id_barang' => 'sometimes|integer|exists:barang,id_barang',
                'tanggal_masuk' => 'sometimes|date',
                'jumlah' => 'sometimes|integer|min:1',
                'referensi' => 'nullable|string|max:50',
                'catatan' => 'nullable|string',
                'oleh' => 'nullable|string|max:50',
            ]);

            DB::beginTransaction();
            
            // If updating quantity, adjust stock
            if (isset($validated['jumlah']) && $validated['jumlah'] != $barangMasuk->jumlah) {
                $barang = Barang::find($barangMasuk->id_barang);
                if ($barang) {
                    $barang->stok_saat_ini -= $barangMasuk->jumlah;
                    $barang->stok_saat_ini += $validated['jumlah'];
                    $barang->save();
                }
            }
            
            $barangMasuk->update($validated);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang masuk updated successfully',
                'data' => $barangMasuk->load('barang')
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating barang masuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @group Barang Masuk
     * @urlParam id required ID barang masuk. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Barang masuk deleted successfully"
     * }
     */
    public function destroy($id)
    {
        try {
            $barangMasuk = BarangMasuk::find($id);
            if (!$barangMasuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang masuk not found'
                ], 404);
            }

            DB::beginTransaction();
            
            // Adjust stock
            $barang = Barang::find($barangMasuk->id_barang);
            if ($barang) {
                $barang->stok_saat_ini -= $barangMasuk->jumlah;
                $barang->save();
            }
            
            $barangMasuk->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang masuk deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting barang masuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
