<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan antarmuka Pengaturan Notifikasi Bot (Figma P17).
     */
    public function index(Request $request): View
    {
        $telegramConfig = [
            'status' => 'Terhubung (Prototipe)',
            'bot_token' => '•••••••••••••••••:AAH...',
            'chat_id' => '-1001928374',
        ];

        $daftarPengaturan = [
            (object) [
                'id' => 'rekap_harian',
                'label' => 'Rekap Laporan Penjualan Harian (otomatis setiap pukul 21.00 WIB)',
                'aktif' => true,
            ],
            (object) [
                'id' => 'stok_minimum',
                'label' => 'Peringatan Stok Minimum & Habis (otomatis saat transaksi selesai)',
                'aktif' => true,
            ],
            (object) [
                'id' => 'perubahan_item',
                'label' => 'Penambahan Item Baru & Perubahan Harga',
                'aktif' => true,
            ],
            (object) [
                'id' => 'backup_selesai',
                'label' => 'Pemberitahuan Backup Database Selesai',
                'aktif' => true,
            ],
            (object) [
                'id' => 'otp_2fa',
                'label' => 'Pengiriman Kode OTP 2FA untuk Login Staf',
                'aktif' => true,
            ],
        ];

        $riwayatNotifikasi = [
            (object) [
                'waktu' => '03 Okt 2026, 21:00',
                'tipe' => 'Rekap Harian',
                'pesan' => 'Laporan penjualan harian — Total: Rp 2.450.000 (34 nota)',
                'status' => 'Terkirim',
            ],
            (object) [
                'waktu' => '03 Okt 2026, 15:32',
                'tipe' => 'Stok Habis',
                'pesan' => 'Pensil 2B (BRG-008) stok habis — segera restock!',
                'status' => 'Terkirim',
            ],
            (object) [
                'waktu' => '03 Okt 2026, 10:15',
                'tipe' => 'Stok Menipis',
                'pesan' => 'Isi Staples No.10 (BRG-006) tersisa 2 box — batas minimum 3',
                'status' => 'Terkirim',
            ],
            (object) [
                'waktu' => '02 Okt 2026, 21:00',
                'tipe' => 'Rekap Harian',
                'pesan' => 'Laporan penjualan harian — Total: Rp 1.890.000 (28 nota)',
                'status' => 'Gagal',
            ],
        ];

        return view('admin.notifikasi.index', [
            'telegramConfig' => $telegramConfig,
            'daftarPengaturan' => $daftarPengaturan,
            'riwayatNotifikasi' => $riwayatNotifikasi,
        ]);
    }
}

