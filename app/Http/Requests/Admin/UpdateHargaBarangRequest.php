<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHargaBarangRequest extends FormRequest
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
            'harga_pokok' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'harga_jual' => ['required', 'numeric', 'min:0', 'max:999999999'],
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
            'harga_pokok.required' => 'Harga pokok wajib diisi.',
            'harga_pokok.numeric' => 'Harga pokok harus berupa angka.',
            'harga_pokok.min' => 'Harga pokok tidak boleh negatif.',
            'harga_pokok.max' => 'Harga pokok maksimal 999.999.999.',
            'harga_jual.required' => 'Harga jual wajib diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'harga_jual.min' => 'Harga jual tidak boleh negatif.',
            'harga_jual.max' => 'Harga jual maksimal 999.999.999.',
        ];
    }
}

