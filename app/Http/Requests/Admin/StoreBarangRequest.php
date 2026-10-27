<?php

namespace App\Http\Requests\Admin;

use App\Models\ItemBarang;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarangRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Siapkan data sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        if (is_string($this->kode_item)) {
            $this->merge([
                'kode_item' => trim($this->kode_item),
            ]);
        }
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_item' => ['required', 'string', 'max:20', 'unique:daftar_item,kode_item'],
            'nama_item' => ['required', 'string', 'max:100'],
            'jenis' => ['nullable', 'string', 'max:50'],
            'merek' => ['nullable', 'string', 'max:50'],
            'rak' => ['nullable', 'string', 'max:20'],
            'satuan' => ['nullable', 'string', 'max:20'],
            'tipe_item' => ['required', 'string', 'in:Barang,Jasa,BARANG,JASA'],
            'stok_awal' => ['nullable', 'integer', 'min:0', 'max:' . ItemBarang::MAKS_STOK_AWAL],
            'harga_pokok' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'harga_jual' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'batas_minimum' => ['nullable', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Pesan kesalahan khusus untuk aturan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_item.required' => 'Kode barang wajib diisi.',
            'kode_item.max' => 'Kode barang maksimal 20 karakter.',
            'kode_item.unique' => 'Kode barang sudah dipakai.',
            'nama_item.required' => 'Nama barang wajib diisi.',
            'nama_item.max' => 'Nama barang maksimal 100 karakter.',
            'tipe_item.required' => 'Tipe item wajib dipilih.',
            'stok_awal.integer' => 'Stok awal harus berupa bilangan bulat.',
            'stok_awal.min' => 'Stok awal tidak boleh negatif.',
            'stok_awal.max' => 'Stok awal maksimal ' . ItemBarang::MAKS_STOK_AWAL . '.',
            'harga_pokok.required' => 'Harga pokok wajib diisi.',
            'harga_pokok.numeric' => 'Harga pokok harus berupa angka.',
            'harga_pokok.min' => 'Harga pokok tidak boleh negatif.',
            'harga_pokok.max' => 'Harga pokok maksimal 999.999.999.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'harga_jual.min' => 'Harga jual tidak boleh negatif.',
            'harga_jual.max' => 'Harga jual maksimal 999.999.999.',
            'batas_minimum.min' => 'Batas minimum tidak boleh negatif.',
        ];
    }
}

