<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Lembar Rekomendasi Restock Barang - Toko Firo</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            font-size: 18pt;
            font-weight: bold;
            color: #065f46;
            margin: 0;
        }
        .brand-sub {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }
        .meta-doc {
            text-align: right;
            font-size: 9pt;
            color: #475569;
        }
        .title-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-section h2 {
            font-size: 13pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-section p {
            font-size: 9pt;
            color: #64748b;
            margin: 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 8.5pt;
            text-transform: uppercase;
            font-weight: bold;
            padding: 8px 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        table.data-table td {
            padding: 7px 6px;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-habis {
            display: inline-block;
            padding: 2px 6px;
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: bold;
            font-size: 8pt;
            border-radius: 4px;
        }
        .badge-menipis {
            display: inline-block;
            padding: 2px 6px;
            background-color: #fef3c7;
            color: #92400e;
            font-weight: bold;
            font-size: 8pt;
            border-radius: 4px;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px dashed #cbd5e1;
            padding-top: 10px;
            font-size: 8pt;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    <div class="brand-title">TOKO FIRO</div>
                    <div class="brand-sub">Alat Tulis Kantor & Fotocopy &bull; Sistem Inventaris Otomatis</div>
                </td>
                <td class="meta-doc" style="width: 40%;">
                    <div><strong>Lembar Rekomendasi Restock</strong></div>
                    <div>Tanggal: {{ $tanggal ?? date('d/m/Y') }}</div>
                    <div>Status: Prioritas Pembelian</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="title-section">
        <h2>Daftar Barang Butuh Restock Segera</h2>
        <p>Barang-barang di bawah ini telah mencapai atau berada di bawah ambang batas stok minimum.</p>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Kode Item</th>
                <th style="width: 35%;">Nama Barang</th>
                <th style="width: 15%;">Jenis</th>
                <th style="width: 10%;" class="text-center">Stok</th>
                <th style="width: 10%;" class="text-center">Batas Min</th>
                <th style="width: 10%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $idx => $item)
                @php
                    $stok = (int) ($item->stok ?? 0);
                    $batas = (int) ($item->batas_minimum ?? 0);
                    $isHabis = $stok <= 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace;">{{ $item->kode_item }}</td>
                    <td><strong>{{ $item->nama_item }}</strong></td>
                    <td>{{ $item->jenis ?? '-' }}</td>
                    <td class="text-center">{{ $stok }} {{ $item->satuan ?? 'pcs' }}</td>
                    <td class="text-center">{{ $batas }} {{ $item->satuan ?? 'pcs' }}</td>
                    <td class="text-center">
                        @if ($isHabis)
                            <span class="badge-habis">HABIS</span>
                        @else
                            <span class="badge-menipis">MENIPIS</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 15px; color: #64748b;">
                        Semua stok barang dalam kondisi aman. Tidak ada barang yang memerlukan restock saat ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Bot Toko Firo pada {{ date('d/m/Y H:i') }} WIB &bull; Dokumen Resmi Internal Toko
    </div>
</body>
</html>

