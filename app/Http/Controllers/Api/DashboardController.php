<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     * @group Dashboard
     * @response 200 {
     *   "success": true,
     *   "message": "Dashboard statistics retrieved successfully",
     *   "data": {
     *     "total_produk": 8,
     *     "stok_tersedia": 151,
     *     "stok_rendah": 2,
     *     "stok_habis": 1,
     *     "stok_per_kategori": {
     *       "Safety": 26,
     *       "Packaging": 5,
     *       "Equipment": 120,
     *       "Office Supplies": 2
     *     },
     *     "aktivitas_terakhir": [
     *       {
     *         "id": 3,
     *         "type": "barang_keluar",
     *         "tanggal": "2026-04-28 15:02:00",
     *         "jumlah": 5,
     *         "nama_barang": "Helm Fanzhi Terkuat",
     *         "oleh": "Admin User"
     *       }
     *     ],
     *     "ringkasan_stok_rendah": [
     *       {
     *         "id_barang": 4,
     *         "kode_barang": "LBL-005",
     *         "nama_barang": "Label Barcode Roll",
     *         "stok_saat_ini": 2,
     *         "stok_min": 5
     *       }
     *     ]
     *   }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function index(Request $request)
    {
        try {
            // Total produk
            $totalProduk = Barang::count();

            // Stok tersedia (total semua stok)
            $stokTersedia = Barang::sum('stok_saat_ini');

            // Stok rendah (stok < stok_min)
            $stokRendah = Barang::where('stok_saat_ini', '<', DB::raw('stok_min'))
                ->where('stok_saat_ini', '>', 0)
                ->count();

            // Stok habis (stok = 0)
            $stokHabis = Barang::where('stok_saat_ini', 0)->count();

            // Stok per kategori
            $stokPerKategori = Barang::select('kategori', DB::raw('SUM(stok_saat_ini) as total_stok'))
                ->groupBy('kategori')
                ->pluck('total_stok', 'kategori')
                ->toArray();

            // Aktivitas terakhir (gabungan barang_masuk dan barang_keluar)
            $barangMasukTerakhir = BarangMasuk::select(
                'id_masuk as id',
                DB::raw("'barang_masuk' as type"),
                'tanggal_masuk as tanggal',
                'jumlah',
                'oleh'
            )
                ->with(['barang:id_barang,nama_barang'])
                ->orderBy('tanggal_masuk', 'desc')
                ->limit(5)
                ->get();

            $barangKeluarTerakhir = BarangKeluar::select(
                'id_keluar as id',
                DB::raw("'barang_keluar' as type"),
                'tanggal_keluar as tanggal',
                'jumlah',
                'oleh'
            )
                ->with(['barang:id_barang,nama_barang'])
                ->orderBy('tanggal_keluar', 'desc')
                ->limit(5)
                ->get();

            // Gabungkan dan urutkan berdasarkan tanggal
            $aktivitasTerakhir = $barangMasukTerakhir->concat($barangKeluarTerakhir)
                ->sortByDesc('tanggal')
                ->take(10)
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                        'tanggal' => $item->tanggal,
                        'jumlah' => $item->jumlah,
                        'nama_barang' => $item->barang->nama_barang ?? 'Unknown',
                        'oleh' => $item->oleh,
                    ];
                })
                ->values();

            // Ringkasan stok rendah (stok < stok_min)
            $ringkasanStokRendah = Barang::where('stok_saat_ini', '<', DB::raw('stok_min'))
                ->select('id_barang', 'kode_barang', 'nama_barang', 'stok_saat_ini', 'stok_min')
                ->orderBy('stok_saat_ini', 'asc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id_barang' => $item->id_barang,
                        'kode_barang' => $item->kode_barang,
                        'nama_barang' => $item->nama_barang,
                        'stok_saat_ini' => $item->stok_saat_ini,
                        'stok_min' => $item->stok_min,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Dashboard statistics retrieved successfully',
                'data' => [
                    'total_produk' => $totalProduk,
                    'stok_tersedia' => $stokTersedia,
                    'stok_rendah' => $stokRendah,
                    'stok_habis' => $stokHabis,
                    'stok_per_kategori' => $stokPerKategori,
                    'aktivitas_terakhir' => $aktivitasTerakhir,
                    'ringkasan_stok_rendah' => $ringkasanStokRendah,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
