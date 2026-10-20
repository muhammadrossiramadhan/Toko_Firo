<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DaftarItem extends Model
{
    use HasFactory;

    protected $table = 'daftar_item';

    protected $primaryKey = 'kode_item';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'kode_item',
        'nama_item',
        'jenis',
        'merek',
        'rak',
        'tipe_item',
        'satuan',
        'harga_pokok',
        'harga_jual',
        'stok',
        'keterangan',
        'foto',
    ];

    protected function casts(): array
    {
        return [
            'harga_pokok' => 'decimal:2',
            'harga_jual' => 'decimal:2',
        ];
    }

    public function transaksi(): HasMany
    {
        return $this->hasMany(DaftarTransaksi::class, 'kode_item', 'kode_item');
    }
}
