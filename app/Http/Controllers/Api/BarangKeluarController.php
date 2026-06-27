<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    /**
     * Display a listing of the resource.
     * @group Barang Keluar
     * @response 200 {
     *   "success": true,
     *   "message": "Barang keluar retrieved successfully",
     *   "data": [
     *     {
     *       "id_keluar": 1,
     *       "id_barang": 3,
     *       "tanggal_keluar": "2026-04-22T14:15:00.000000Z",
     *       "jumlah": 50,
     *       "referensi": "DO-2026-045",
     *       "catatan": "Pengiriman ke customer PT ABC",
     *       "oleh": "Admin User"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        try {
            $barangKeluar = BarangKeluar::with('barang')->get();
            return response()->json([
                'success' => true,
                'message' => 'Barang keluar retrieved successfully',
                'data' => $barangKeluar
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barang keluar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @group Barang Keluar
     * @bodyParam id_barang integer required ID barang. Example: 3
     * @bodyParam tanggal_keluar date required Tanggal keluar. Example: 2026-04-22
     * @bodyParam jumlah integer required Jumlah. Example: 50
     * @bodyParam referensi string Referensi. Example: DO-2026-045
     * @bodyParam catatan string Catatan. Example: Pengiriman ke customer PT ABC
     * @bodyParam oleh string Oleh. Example: Admin User
     * @response 201 {
     *   "success": true,
     *   "message": "Barang keluar created successfully",
     *   "data": {
     *     "id_keluar": 1,
     *     "id_barang": 3,
     *     "jumlah": 50
     *   }
     * }
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_barang' => 'required|integer|exists:barang,id_barang',
                'tanggal_keluar' => 'required|date',
                'jumlah' => 'required|integer|min:1',
                'referensi' => 'nullable|string|max:50',
                'catatan' => 'nullable|string',
                'oleh' => 'nullable|string|max:50',
            ]);

            DB::beginTransaction();
            
            // Check if enough stock
            $barang = Barang::find($validated['id_barang']);
            if ($barang && $barang->stok_saat_ini < $validated['jumlah']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock',
                    'available' => $barang->stok_saat_ini,
                    'requested' => $validated['jumlah']
                ], 400);
            }
            
            $barangKeluar = BarangKeluar::create($validated);
            
            // Update stock
            if ($barang) {
                $barang->stok_saat_ini -= $validated['jumlah'];
                $barang->save();
            }
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang keluar created successfully',
                'data' => $barangKeluar->load('barang')
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
                'message' => 'Error creating barang keluar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * @group Barang Keluar
     * @urlParam id required ID barang keluar. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Barang keluar retrieved successfully",
     *   "data": {
     *     "id_keluar": 1,
     *     "id_barang": 3,
     *     "jumlah": 50
     *   }
     * }
     */
    public function show($id)
    {
        try {
            $barangKeluar = BarangKeluar::with('barang')->find($id);
            if (!$barangKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang keluar not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Barang keluar retrieved successfully',
                'data' => $barangKeluar
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barang keluar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * @group Barang Keluar
     * @urlParam id required ID barang keluar. Example: 1
     * @bodyParam id_barang integer ID barang. Example: 3
     * @bodyParam tanggal_keluar date Tanggal keluar. Example: 2026-04-22
     * @bodyParam jumlah integer Jumlah. Example: 50
     * @bodyParam referensi string Referensi. Example: DO-2026-045
     * @bodyParam catatan string Catatan. Example: Pengiriman ke customer PT ABC
     * @bodyParam oleh string Oleh. Example: Admin User
     * @response 200 {
     *   "success": true,
     *   "message": "Barang keluar updated successfully",
     *   "data": {
     *     "id_keluar": 1,
     *     "id_barang": 3,
     *     "jumlah": 50
     *   }
     * }
     */
    public function update(Request $request, $id)
    {
        try {
            $barangKeluar = BarangKeluar::find($id);
            if (!$barangKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang keluar not found'
                ], 404);
            }

            $validated = $request->validate([
                'id_barang' => 'sometimes|integer|exists:barang,id_barang',
                'tanggal_keluar' => 'sometimes|date',
                'jumlah' => 'sometimes|integer|min:1',
                'referensi' => 'nullable|string|max:50',
                'catatan' => 'nullable|string',
                'oleh' => 'nullable|string|max:50',
            ]);

            DB::beginTransaction();
            
            // If updating quantity, adjust stock
            if (isset($validated['jumlah']) && $validated['jumlah'] != $barangKeluar->jumlah) {
                $barang = Barang::find($barangKeluar->id_barang);
                if ($barang) {
                    $barang->stok_saat_ini += $barangKeluar->jumlah;
                    $barang->stok_saat_ini -= $validated['jumlah'];
                    $barang->save();
                }
            }
            
            $barangKeluar->update($validated);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang keluar updated successfully',
                'data' => $barangKeluar->load('barang')
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
                'message' => 'Error updating barang keluar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @group Barang Keluar
     * @urlParam id required ID barang keluar. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Barang keluar deleted successfully"
     * }
     */
    public function destroy($id)
    {
        try {
            $barangKeluar = BarangKeluar::find($id);
            if (!$barangKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang keluar not found'
                ], 404);
            }

            DB::beginTransaction();
            
            // Adjust stock
            $barang = Barang::find($barangKeluar->id_barang);
            if ($barang) {
                $barang->stok_saat_ini += $barangKeluar->jumlah;
                $barang->save();
            }
            
            $barangKeluar->delete();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barang keluar deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting barang keluar',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
