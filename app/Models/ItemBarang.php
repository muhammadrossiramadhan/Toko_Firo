<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBarang extends Model
{
    use HasFactory;

    /**
     * Batas maksimum stok awal saat penambahan item baru (K9).
     */
    public const MAKS_STOK_AWAL = 1000;
    public const MAX_STOK_AWAL = 1000;

    /**
     * Tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'daftar_item';

    /**
     * Primary key tabel.
     *
     * @var string
     */
    protected $primaryKey = 'kode_item';

    /**
     * Menandakan bahwa ID bukan auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Menonaktifkan timestamps bawaan Laravel.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var list<string>
     */
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
        'batas_minimum',
        'dibuat_pada',
        'diubah_pada',
    ];

    /**
     * Casting tipe data kolom.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stok' => 'integer',
            'batas_minimum' => 'integer',
            'harga_pokok' => 'float',
            'harga_jual' => 'float',
            'dibuat_pada' => 'datetime',
            'diubah_pada' => 'datetime',
        ];
    }

    /**
     * Accessor untuk status_stok.
     * "Habis" jika stok = 0, "Menipis" jika stok <= batas_minimum, selain itu "Aman".
     */
    public function getStatusStokAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'Habis';
        }

        if ($this->stok <= ($this->batas_minimum ?? 5)) {
            return 'Menipis';
        }

        return 'Aman';
    }
}

