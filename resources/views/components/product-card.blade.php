@props(['item'])

<div class="border border-border rounded-lg overflow-hidden bg-surface">
    <!-- Image area -->
    <div class="h-40 bg-[#F0F1F0] flex items-center justify-center relative">
        <span class="text-4xl text-text-disabled select-none">📦</span>

        @if(($item->tipe_item ?? '') === 'JASA')
            <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-800">✓ Tersedia</span>
        @elseif($item->stok > 10)
            <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-800">✓ Aman</span>
        @elseif($item->stok > 0 && $item->stok <= 10)
            <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-xs font-medium bg-yellow-50 text-[#92700C]">⚠ Menipis</span>
        @else
            <span class="absolute top-3 left-3 px-2 py-0.5 rounded text-xs font-medium bg-danger-50 text-danger">✕ Habis</span>
        @endif
    </div>

    <!-- Info area -->
    <div class="p-3.5">
        <div class="text-xs font-medium text-text-muted">{{ $item->jenis ?? $item->tipe_item ?? 'Barang' }}</div>
        <h3 class="text-sm font-medium text-ink mt-1 line-clamp-1" title="{{ $item->nama_item }}">{{ $item->nama_item }}</h3>
        <div class="flex items-center gap-2 mt-2">
            <span class="text-sm font-medium text-green-700">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</span>
            @if(($item->tipe_item ?? '') !== 'JASA')
                <span class="w-px h-3 bg-border"></span>
                <span class="text-xs text-text-muted">{{ $item->stok == 0 ? 'Stok habis' : 'Sisa ' . $item->stok . ' pcs' }}</span>
            @else
                <span class="text-xs text-text-muted">Tersedia</span>
            @endif
        </div>
    </div>
</div>
