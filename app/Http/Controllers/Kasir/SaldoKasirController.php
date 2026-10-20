<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SaldoKasirController extends Controller
{
    /**
     * Menampilkan antarmuka Saldo Harian Kasir (Frontend Prototype Figma Frame P08).
     */
    public function index(): View
    {
        $ringkasan = [
            'tanggal' => 'Jumat, 3 Okt 2026',
            'nominal_saldo' => 'Rp 3.180.000',
            'total_transaksi' => 14,
            'rata_rata' => 'Rp 227.142',
            'modal_awal' => 'Rp 500.000',
        ];

        $riwayatTransaksi = [
            [
                'waktu' => '14:32',
                'no_transaksi' => 'TRX-001',
                'item' => 'Kertas HVS, Pulpen (2)',
                'total' => 'Rp 82.000',
                'metode' => 'Tunai',
            ],
            [
                'waktu' => '13:15',
                'no_transaksi' => 'TRX-002',
                'item' => 'Fotocopy A4 ×50',
                'total' => 'Rp 25.000',
                'metode' => 'QRIS',
            ],
            [
                'waktu' => '12:40',
                'no_transaksi' => 'TRX-003',
                'item' => 'Buku Sidu (3), Map (2)',
                'total' => 'Rp 22.500',
                'metode' => 'Tunai',
            ],
            [
                'waktu' => '11:22',
                'no_transaksi' => 'TRX-004',
                'item' => 'Spidol Snowman (5)',
                'total' => 'Rp 40.000',
                'metode' => 'Tunai',
            ],
            [
                'waktu' => '10:05',
                'no_transaksi' => 'TRX-005',
                'item' => 'Tinta Canon, Kertas F4',
                'total' => 'Rp 195.000',
                'metode' => 'QRIS',
            ],
        ];

        $kasir = [
            'nama' => 'Rina Putri',
            'inisial' => 'R',
            'role' => 'Kasir',
        ];

        return view('kasir.saldo.index', compact('ringkasan', 'riwayatTransaksi', 'kasir'));
    }
}

