<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DaftarPenjualan extends Model
{
    use HasFactory;

    protected $table = 'daftar_penjualan';

    protected $primaryKey = 'kode_transaksi';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'kode_transaksi',
        'nama_pelanggan',
        'tanggal_transaksi',
        'total_bayar',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'date',
            'total_bayar' => 'decimal:2',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(DaftarTransaksi::class, 'kode_penjualan', 'kode_transaksi');
    }
}
