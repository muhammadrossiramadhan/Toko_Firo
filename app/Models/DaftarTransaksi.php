<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DaftarTransaksi extends Model
{
    use HasFactory;

    protected $table = 'daftar_transaksi';

    protected $primaryKey = 'id_detail';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'kode_item',
        'jumlah_item',
        'item_keluar',
        'item_masuk',
        'retur_penjualan',
        'retur_pembelian',
        'subtotal',
        'kode_penjualan',
        'kode_pembelian',
        'tanggal_log',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tanggal_log' => 'datetime',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(DaftarItem::class, 'kode_item', 'kode_item');
    }

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(DaftarPembelian::class, 'kode_pembelian', 'kode_transaksi');
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(DaftarPenjualan::class, 'kode_penjualan', 'kode_transaksi');
    }
}
