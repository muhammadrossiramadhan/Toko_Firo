<?php

namespace App\Http\Requests\Kasir;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class SimpanTransaksiRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Persiapkan data masukan sebelum validasi dijalankan.
     */
    protected function prepareForValidation(): void
    {
        // Decode JSON jika input items dikirim sebagai string
        if (is_string($this->input('items'))) {
            $decoded = json_decode($this->input('items'), true);
            if (is_array($decoded)) {
                $this->merge(['items' => $decoded]);
            }
        }

        // Normalisasi format item agar mendukung alias parameter umum
        if (is_array($this->input('items'))) {
            $normalizedItems = [];
            foreach ($this->input('items') as $item) {
                if (is_array($item)) {
                    $barangId = (string) ($item['barang_id'] ?? $item['kode_item'] ?? $item['kode'] ?? '');
                    $qty = $item['qty'] ?? $item['jumlah'] ?? $item['kuantitas'] ?? 1;

                    // Jika numeric id diberikan dan bukan kode_item langsung, coba padding BRG-XXX
                    if (is_numeric($barangId) && !DB::table('daftar_item')->where('kode_item', $barangId)->exists()) {
                        $padded = 'BRG-' . str_pad($barangId, 3, '0', STR_PAD_LEFT);
                        if (DB::table('daftar_item')->where('kode_item', $padded)->exists()) {
                            $barangId = $padded;
                        }
                    }

                    $normalizedItems[] = [
                        'barang_id' => $barangId,
                        'qty' => is_numeric($qty) ? (int) $qty : $qty,
                    ];
                }
            }
            $this->merge(['items' => $normalizedItems]);
        }
    }

    /**
     * Aturan validasi ketat untuk transaksi penjualan kasir.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'string', 'exists:daftar_item,kode_item'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'bayar' => ['required', 'numeric', 'min:0'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'metode_bayar' => ['nullable', 'string', 'in:TUNAI,QRIS,tunai,qris'],
            'nama_pelanggan' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Pesan validasi ramah pengguna berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Daftar item belanja wajib diisi.',
            'items.array' => 'Format daftar item belanja tidak valid.',
            'items.min' => 'Transaksi minimal harus memiliki 1 item barang belanjaan.',
            'items.*.barang_id.required' => 'Barang wajib dipilih.',
            'items.*.barang_id.exists' => 'Barang yang dipilih tidak ditemukan dalam katalog barang.',
            'items.*.qty.required' => 'Jumlah barang wajib diisi.',
            'items.*.qty.integer' => 'Jumlah barang harus berupa bilangan bulat positif.',
            'items.*.qty.min' => 'Jumlah barang minimal 1 unit.',
            'bayar.required' => 'Nominal uang pembayaran wajib diisi.',
            'bayar.numeric' => 'Nominal uang pembayaran harus berupa angka valid.',
            'bayar.min' => 'Nominal uang pembayaran tidak boleh bernilai negatif.',
            'diskon.numeric' => 'Nominal diskon harus berupa angka valid.',
            'diskon.min' => 'Nominal diskon tidak boleh bernilai negatif.',
            'metode_bayar.in' => 'Metode pembayaran harus berupa TUNAI atau QRIS.',
        ];
    }
}

