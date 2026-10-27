<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarangRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi yang berlaku untuk permintaan ini.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_item' => ['required', 'string', 'max:20'],
            'nama_item' => ['required', 'string', 'max:100'],
            'jenis' => ['nullable', 'string', 'max:50'],
            'merek' => ['nullable', 'string', 'max:50'],
            'rak' => ['nullable', 'string', 'max:20'],
            'satuan' => ['nullable', 'string', 'max:20'],
            'tipe_item' => ['required', 'string', 'in:Barang,Jasa,BARANG,JASA'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'batas_minimum' => ['nullable', 'integer', 'min:0'],
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
            'nama_item.required' => 'Nama barang wajib diisi.',
            'nama_item.max' => 'Nama barang maksimal 100 karakter.',
            'tipe_item.required' => 'Tipe item wajib dipilih.',
            'batas_minimum.min' => 'Batas minimum tidak boleh negatif.',
        ];
    }
}

