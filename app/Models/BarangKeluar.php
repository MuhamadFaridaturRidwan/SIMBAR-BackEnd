<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'barang_keluar';
    protected $primaryKey = 'id_keluar';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_barang',
        'tanggal_keluar',
        'jumlah',
        'referensi',
        'catatan',
        'oleh',
    ];

    protected $casts = [
        'tanggal_keluar' => 'datetime',
        'jumlah' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
