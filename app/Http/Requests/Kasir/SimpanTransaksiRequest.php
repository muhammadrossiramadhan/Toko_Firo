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
        // Ambil data items dari session keranjang jika tidak dikirimkan eksplisit pada request
        if (empty($this->input('items')) && session()->has('keranjang')) {
            $sessionCart = session('keranjang', []);
            $itemsFromSession = [];
            foreach ($sessionCart as $cartItem) {
                if (is_array($cartItem)) {
                    $itemsFromSession[] = [
                        'barang_id' => $cartItem['kode'] ?? $cartItem['kode_item'] ?? '',
                        'qty' => $cartItem['qty'] ?? 1,
                    ];
                }
            }
            if (!empty($itemsFromSession)) {
                $this->merge(['items' => $itemsFromSession]);
            }
        }

        // Decode JSON jika input items dikirim sebagai string
        if (is_string($this->input('items'))) {
            $decoded = json_decode($this->input('items'), true);
            if (is_array($decoded)) {
                $this->merge(['items' => $decoded]);
            }
        }

        // Normalisasi format item agar mendukung alias parameter umum (kode_item & barang_id)
        if (is_array($this->input('items'))) {
            $normalizedItems = [];
            foreach ($this->input('items') as $item) {
                if (is_array($item)) {
                    $kodeItem = (string) ($item['kode_item'] ?? $item['barang_id'] ?? $item['kode'] ?? '');
                    $qty = $item['qty'] ?? $item['jumlah'] ?? $item['kuantitas'] ?? 1;

                    // Jika numeric id diberikan dan bukan kode_item langsung, coba padding BRG-XXX
                    if (is_numeric($kodeItem) && !DB::table('daftar_item')->where('kode_item', $kodeItem)->exists()) {
                        $padded = 'BRG-' . str_pad($kodeItem, 3, '0', STR_PAD_LEFT);
                        if (DB::table('daftar_item')->where('kode_item', $padded)->exists()) {
                            $kodeItem = $padded;
                        }
                    }

                    $normalizedItems[] = [
                        'kode_item' => $kodeItem,
                        'barang_id' => $kodeItem,
                        'qty' => is_numeric($qty) ? (int) $qty : $qty,
                    ];
                }
            }
            $this->merge(['items' => $normalizedItems]);
        }

        // Sinkronisasi dan pembersihan nominal_bayar & bayar
        $rawBayar = $this->input('nominal_bayar', $this->input('bayar'));
        if (is_string($rawBayar)) {
            $cleanBayar = preg_replace('/[^0-9]/', '', $rawBayar);
            $rawBayar = $cleanBayar !== '' ? (float) $cleanBayar : null;
        }

        if ($rawBayar !== null) {
            $this->merge([
                'nominal_bayar' => (float) $rawBayar,
                'bayar' => (float) $rawBayar,
            ]);
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
            'items.*.kode_item' => ['required', 'string', 'exists:daftar_item,kode_item'],
            'items.*.barang_id' => ['nullable', 'string', 'exists:daftar_item,kode_item'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'nominal_bayar' => ['nullable', 'numeric', 'min:0'],
            'bayar' => ['nullable', 'numeric', 'min:0'],
            'diskon' => ['nullable', 'numeric', 'min:0'],
            'metode_bayar' => ['nullable', 'string', 'in:TUNAI,QRIS,TRANSFER,tunai,qris,transfer'],
            'nama_pelanggan' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Konfigurasi validator tambahan untuk validasi kecukupan pembayaran TUNAI.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $metode = strtoupper((string) ($this->input('metode_bayar') ?? 'TUNAI'));
            if ($metode === 'TUNAI') {
                $nominalBayar = (float) ($this->input('nominal_bayar') ?? $this->input('bayar') ?? 0);
                $items = $this->input('items', []);

                if (is_array($items) && !empty($items)) {
                    $totalBelanja = 0.0;
                    foreach ($items as $item) {
                        $kode = (string) ($item['kode_item'] ?? $item['barang_id'] ?? '');
                        $qty = (int) ($item['qty'] ?? 1);
                        if (!empty($kode) && $qty > 0) {
                            $harga = (float) DB::table('daftar_item')->where('kode_item', $kode)->value('harga_jual');
                            $totalBelanja += ($harga * $qty);
                        }
                    }

                    $diskon = max(0.0, (float) ($this->input('diskon') ?? 0));
                    $totalAkhir = max(0.0, $totalBelanja - $diskon);

                    if ($nominalBayar < $totalAkhir) {
                        $kekurangan = $totalAkhir - $nominalBayar;
                        $pesan = "Uang pembayaran kurang sebesar Rp " . number_format($kekurangan, 0, ',', '.') . ".";
                        $validator->errors()->add('nominal_bayar', $pesan);
                        $validator->errors()->add('bayar', $pesan);
                    }
                }
            }
        });
    }

    /**
     * Format respon JSON jika validasi form request gagal.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $response = response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first() ?: 'Data masukan transaksi tidak valid.',
            'errors' => $validator->errors(),
        ], 422);

        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
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

