<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'supplier',
        'kategori',
        'lokasi',
        'stok_saat_ini',
        'stok_min',
        'harga_satuan',
    ];

    protected $casts = [
        'stok_saat_ini' => 'integer',
        'stok_min' => 'integer',
        'harga_satuan' => 'integer',
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_barang', 'id_barang');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'id_barang', 'id_barang');
    }
}
