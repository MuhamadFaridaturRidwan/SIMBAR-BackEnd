<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_masuk';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_barang',
        'tanggal_masuk',
        'jumlah',
        'referensi',
        'catatan',
        'oleh',
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'jumlah' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
