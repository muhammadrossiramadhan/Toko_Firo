<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PembelianService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembelianController extends Controller
{
    public function __construct(
        protected PembelianService $pembelianService
    ) {}

    /**
     * Menampilkan halaman Pembelian Barang (Figma P21).
     */
    public function index(Request $request): View
    {
        $barangRestock = $this->pembelianService->getBarangPerluRestock();
        $riwayatPembelian = $this->pembelianService->getRiwayatPembelian();

        return view('admin.pembelian.index', [
            'barangRestock' => $barangRestock,
            'riwayatPembelian' => $riwayatPembelian,
        ]);
    }

    /**
     * Menyimpan transaksi pembelian restock barang baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_supplier' => ['required', 'string', 'max:100'],
            'kode_item' => ['required', 'string', 'exists:daftar_item,kode_item'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'kode_transaksi' => ['nullable', 'string', 'max:20', 'unique:daftar_pembelian,kode_transaksi'],
        ], [
            'nama_supplier.required' => 'Nama supplier wajib diisi.',
            'kode_item.required' => 'Barang wajib dipilih.',
            'kode_item.exists' => 'Barang tidak ditemukan dalam sistem.',
            'jumlah.required' => 'Jumlah pembelian wajib diisi.',
            'jumlah.min' => 'Jumlah pembelian minimal 1.',
        ]);

        $kodeTransaksi = $validated['kode_transaksi'] ?? ('PB-' . date('Ymd') . '-' . sprintf('%03d', rand(1, 999)));
        $idPengguna = auth()->id() ?? auth('web')->id();

        $this->pembelianService->catatPembelian(
            $kodeTransaksi,
            $validated['nama_supplier'],
            $idPengguna,
            [
                [
                    'kode_item' => $validated['kode_item'],
                    'jumlah' => (int) $validated['jumlah'],
                ],
            ]
        );

        return redirect()->route('admin.pembelian')
            ->with('sukses', 'Pembelian restock barang berhasil dicatat.');
    }
}
