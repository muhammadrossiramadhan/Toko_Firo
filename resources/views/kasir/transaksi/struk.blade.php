<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembelian - {{ $transaksi->kode_transaksi }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;
            color: #111;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .struk-card {
            max-width: 380px;
            margin: 0 auto;
            background: #fff;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 22px; font-weight: bold; }
        .header p { margin: 4px 0; font-size: 14px; color: #444; }
        .divider { border-top: 1px dashed #777; margin: 12px 0; }
        .info-table, .item-table, .total-table { width: 100%; border-collapse: collapse; font-size: 15px; }
        .text-right { text-align: right; }
        .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #444; }
        .btn-print {
            display: block;
            width: 100%;
            min-height: 44px;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .struk-card { box-shadow: none; padding: 0; max-width: 100%; }
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="struk-card">
        <div class="header">
            <h1>{{ $pengaturan['nama_toko'] ?? 'TOKO FIRO' }}</h1>
            @if(!empty($pengaturan['header_struk']))
                <p>{{ $pengaturan['header_struk'] }}</p>
            @endif
            @if(!empty($pengaturan['alamat']))
                <p>{{ $pengaturan['alamat'] }}</p>
            @endif
            @if(!empty($pengaturan['telepon']))
                <p>Telepon: {{ $pengaturan['telepon'] }}</p>
            @endif
        </div>

        <div class="divider"></div>

        <table class="info-table">
            <tr><td>Nota:</td><td class="text-right"><strong>{{ $transaksi->kode_transaksi }}</strong></td></tr>
            <tr><td>Tanggal:</td><td class="text-right">{{ $transaksi->tanggal_transaksi ? date('d/m/Y', strtotime($transaksi->tanggal_transaksi)) : date('d/m/Y') }}</td></tr>
            <tr><td>Kasir:</td><td class="text-right">{{ $transaksi->kasir->nama ?? 'Kasir Toko Firo' }}</td></tr>
            <tr><td>Pelanggan:</td><td class="text-right">{{ $transaksi->nama_pelanggan ?: 'Umum' }}</td></tr>
        </table>

        <div class="divider"></div>

        <table class="item-table">
            @foreach($transaksi->transaksi as $detail)
                @php
                    $namaItem = $detail->item->nama_item ?? $detail->kode_item;
                    $qty = (int) ($detail->item_keluar ?: $detail->jumlah_item ?: 1);
                    $subtotal = number_format((float) $detail->subtotal, 0, ',', '.');
                    $hargaSatuan = $qty > 0 ? number_format((float) $detail->subtotal / $qty, 0, ',', '.') : $subtotal;
                @endphp
                <tr>
                    <td style="padding: 4px 0;" colspan="2"><strong>{{ $namaItem }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 0 0 6px 0; color: #555;">{{ $qty }} x Rp {{ $hargaSatuan }}</td>
                    <td style="padding: 0 0 6px 0; text-align: right;">Rp {{ $subtotal }}</td>
                </tr>
            @endforeach
        </table>

        <div class="divider"></div>

        <table class="total-table">
            <tr>
                <td style="font-size: 18px; font-weight: bold;">TOTAL BAYAR:</td>
                <td class="text-right" style="font-size: 18px; font-weight: bold;">Rp {{ number_format((float) $transaksi->total_bayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Metode Bayar:</td>
                <td class="text-right">{{ $transaksi->metode_bayar ?: 'TUNAI' }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <div class="footer">
            <p>{{ $pengaturan['footer_struk'] ?? 'Terima kasih telah berbelanja!' }}</p>
            <p>Barang yang sudah dibeli dapat ditukar maksimal 1x24 jam dengan struk resmi.</p>
        </div>

        <button type="button" class="btn-print" onclick="window.print()">Cetak Resi</button>
    </div>
</body>
</html>

