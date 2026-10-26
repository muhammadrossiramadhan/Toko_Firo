<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Harian - Toko Firo</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .header .brand-title {
            font-size: 20pt;
            font-weight: bold;
            color: #065f46;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .header .brand-sub {
            font-size: 9.5pt;
            color: #64748b;
            margin-top: 3px;
        }
        .header .meta-doc {
            text-align: right;
            font-size: 9.5pt;
            color: #475569;
        }
        .title-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-section h2 {
            font-size: 14pt;
            text-transform: uppercase;
            color: #0f172a;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
        }
        .title-section .subtitle {
            font-size: 10pt;
            color: #64748b;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            vertical-align: top;
            width: 33.33%;
        }
        .summary-label {
            font-size: 8.5pt;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .summary-value {
            font-size: 13pt;
            font-weight: bold;
            color: #065f46;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 10px;
            border-left: 3px solid #059669;
            padding-left: 8px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 9.5pt;
        }
        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .data-table td {
            padding: 8px 10px;
            border: 1px solid #e2e8f0;
            color: #334155;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-mono {
            font-family: 'Courier New', Courier, monospace;
        }
        .footer-note {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px dashed #cbd5e1;
            font-size: 8.5pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="vertical-align: middle;">
                    <div class="brand-title">TOKO FIRO</div>
                    <div class="brand-sub">Penyedia ATK, Fotocopy, dan Kebutuhan Kantor</div>
                    <div class="brand-sub">Jl. Yos Sudarso, Kraksaan, Kab. Probolinggo | WA: 0852-3478-9169</div>
                </td>
                <td class="meta-doc" style="vertical-align: middle;">
                    <div><strong>LAPORAN RESMI</strong></div>
                    <div>Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
                    <div>Oleh: Sistem Telegram Bot</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title-section">
        <h2>Laporan Rekap Penjualan Harian</h2>
        <div class="subtitle">Periode: {{ $data['tanggal'] ?? date('d F Y') }}</div>
    </div>

    <table class="summary-table">
        <tr>
            <td class="summary-card">
                <div class="summary-label">Total Penjualan</div>
                <div class="summary-value">Rp {{ number_format($data['total_penjualan'] ?? 0, 0, ',', '.') }}</div>
            </td>
            <td style="width: 10px;"></td>
            <td class="summary-card">
                <div class="summary-label">Jumlah Transaksi</div>
                <div class="summary-value">{{ $data['jumlah_transaksi'] ?? 0 }} Nota</div>
            </td>
            <td style="width: 10px;"></td>
            <td class="summary-card">
                <div class="summary-label">Barang Terjual</div>
                <div class="summary-value">{{ $data['barang_terjual'] ?? 0 }} Pcs</div>
            </td>
        </tr>
        <tr><td style="height: 10px;" colspan="5"></td></tr>
        <tr>
            <td class="summary-card">
                <div class="summary-label">Saldo Akhir Kasir</div>
                <div class="summary-value">Rp {{ number_format($data['saldo_kasir'] ?? 0, 0, ',', '.') }}</div>
            </td>
            <td style="width: 10px;"></td>
            <td class="summary-card" colspan="3">
                <div class="summary-label">Produk Terlaris Hari Ini</div>
                <div class="summary-value" style="font-size: 11pt; color: #1e293b;">
                    {{ $data['produk_terlaris'] ?? '-' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Transaksi Terakhir Hari Ini</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th style="width: 50px;">Waktu</th>
                <th style="width: 120px;">Kode Transaksi</th>
                <th style="width: 80px;">Kasir</th>
                <th>Rincian Item</th>
                <th style="width: 60px;" class="text-center">Metode</th>
                <th style="width: 90px;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['transaksi'] ?? [] as $index => $trx)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $trx->waktu ?? '-' }}</td>
                    <td class="font-mono"><strong>{{ $trx->kode ?? '-' }}</strong></td>
                    <td>{{ $trx->kasir ?? '-' }}</td>
                    <td>{{ $trx->item ?? '-' }}</td>
                    <td class="text-center">{{ $trx->metode ?? 'Tunai' }}</td>
                    <td class="text-right font-mono"><strong>Rp {{ number_format($trx->subtotal ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #94a3b8;">
                        Tidak ada transaksi tercatat untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Laporan ini digenerate secara otomatis oleh sistem point-of-sale Toko Firo dan sah tanpa tanda tangan basah.
    </div>
</body>
</html>

