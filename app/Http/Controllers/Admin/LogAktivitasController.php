<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LogAktivitasService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogAktivitasController extends Controller
{
    public function __construct(
        protected LogAktivitasService $logService
    ) {}

    /**
     * Menampilkan antarmuka Log Aktivitas (Figma P16).
     */
    public function index(Request $request): View|StreamedResponse
    {
        // Tangani ekspor CSV jika parameter ekspor / export disertakan pada URL
        if ($request->query('export') === 'csv' || $request->query('ekspor') === 'csv') {
            return $this->ekspor($request);
        }

        $filter = [
            'tipe' => $request->query('tipe'),
            'tanggal_mulai' => $request->query('tanggal_mulai') ?? $request->query('start_date'),
            'tanggal_akhir' => $request->query('tanggal_akhir') ?? $request->query('end_date'),
            'kata_kunci' => $request->query('kata_kunci') ?? $request->query('keyword') ?? $request->query('q'),
        ];

        $daftarLog = $this->logService->getLogs($filter, 10);
        $tipeAksiList = $this->logService->getTipeAksiList();

        return view('admin.log.index', [
            'daftarLog' => $daftarLog,
            'tipeAksiList' => $tipeAksiList,
            'filter' => $filter,
        ]);
    }

    /**
     * Mengekspor seluruh log aktivitas sesuai filter ke format CSV murni.
     */
    public function ekspor(Request $request): StreamedResponse
    {
        $filter = [
            'tipe' => $request->query('tipe'),
            'tanggal_mulai' => $request->query('tanggal_mulai') ?? $request->query('start_date'),
            'tanggal_akhir' => $request->query('tanggal_akhir') ?? $request->query('end_date'),
            'kata_kunci' => $request->query('kata_kunci') ?? $request->query('keyword') ?? $request->query('q'),
        ];

        $filename = 'log-aktivitas-' . date('Y-m-d') . '.csv';
        $csvContent = $this->logService->eksporCsv($filter);

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
