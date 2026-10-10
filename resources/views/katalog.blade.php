@extends('layouts.publik')
@section('title', 'Katalog Barang - Toko Firo')

@section('content')
    {{-- KATALOG HEADER --}}
    <section class="bg-surface py-8 md:pb-6 lg:pb-6 md:pt-8 lg:pt-8">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <h1 class="font-heading font-bold text-[32px] leading-[40px] text-ink">Katalog Barang</h1>
            <p class="text-sm text-text-muted mt-2">Katalog untuk cek ketersediaan. Pembelian langsung di toko.</p>
            @if($lastUpdate)
                <div class="inline-flex items-center gap-1.5 mt-3 px-2.5 py-1 bg-green-50 rounded-full">
                    <span class="text-xs font-medium text-green-700">✓ Diperbarui {{ \Carbon\Carbon::parse($lastUpdate)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H.i') }}</span>
                </div>
            @endif
        </div>
    </section>

    {{-- TOOLBAR --}}
    <section class="bg-surface border-y border-border">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px] py-4">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Search --}}
                <form action="{{ route('katalog') }}" method="GET" class="relative w-full sm:w-[320px]">
                    @if(request('jenis'))
                        <input type="hidden" name="jenis" value="{{ request('jenis') }}">
                    @endif
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari barang..." class="w-full h-[39px] pl-10 pr-4 bg-bg border border-border rounded-lg text-sm text-ink placeholder:text-text-muted focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition">
                </form>
                {{-- Filter Pills --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('katalog', request()->only('cari')) }}" class="px-3.5 py-1.5 text-[13px] font-medium rounded-full {{ !request('jenis') ? 'bg-green-700 text-white' : 'bg-surface border border-border text-ink hover:bg-bg transition' }}">Semua</a>
                    @foreach($kategori as $kat)
                        <a href="{{ route('katalog', array_merge(request()->only('cari'), ['jenis' => $kat])) }}" class="px-3.5 py-1.5 text-[13px] font-medium rounded-full {{ request('jenis') === $kat ? 'bg-green-700 text-white' : 'bg-surface border border-border text-ink hover:bg-bg transition' }}">{{ $kat }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- CONTENT: PRODUCT GRID --}}
    <section class="bg-bg">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px] py-8">
            @if($items->isEmpty())
                <div class="text-center py-16">
                    <p class="text-text-muted">Belum ada barang di katalog.</p>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4 lg:gap-5">
                    @foreach($items as $item)
                        @php
                            $stok = $item->stok;
                            $isHabis = $stok === 0;
                            $isMenipis = $stok > 0 && $stok < 10;
                            $satuan = $item->satuan ?: 'pcs';
                            $harga = number_format($item->harga_jual, 0, ',', '.');
                        @endphp
                        <div class="bg-surface border border-border rounded-xl overflow-hidden {{ $isHabis ? 'opacity-50' : '' }}">
                            <div class="aspect-square bg-bg flex items-center justify-center relative overflow-hidden">
                                <img src="{{ $item->foto ?: '/items/default.svg' }}" alt="{{ $item->nama_item }}" class="w-full h-full object-cover">
                                @if($isHabis)
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-0.5 bg-danger-50 rounded-full text-[11px] font-semibold text-danger">✕ Habis</span>
                                @elseif($isMenipis)
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-0.5 bg-[#FFEE10] rounded-full text-[11px] font-semibold text-ink">⚠ Menipis</span>
                                @else
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-0.5 bg-green-50 rounded-full text-[11px] font-semibold text-green-700">✓ Aman</span>
                                @endif
                            </div>
                            <div class="p-2.5 lg:p-3.5 space-y-0.5 lg:space-y-1">
                                @if($item->jenis)
                                    <div class="text-[11px] font-medium text-text-muted">{{ $item->jenis }}</div>
                                @endif
                                <h3 class="text-[13px] lg:text-sm font-semibold text-ink line-clamp-2">{{ $item->nama_item }}</h3>
                                <div class="flex flex-wrap items-center gap-1 lg:gap-2 pt-1">
                                    <span class="font-heading font-bold text-[13px] lg:text-[15px] text-ink">Rp {{ $harga }}{{ in_array($satuan, ['lbr', 'lembar']) ? '/' . $satuan : '' }}</span>
                                    <span class="hidden lg:block w-px h-3 bg-border"></span>
                                    @if($isHabis)
                                        <span class="text-xs font-medium text-danger">Stok habis</span>
                                    @else
                                        <span class="text-[11px] lg:text-xs font-medium {{ $isMenipis ? 'text-ink' : 'text-text-muted' }}">Sisa {{ $stok }} {{ $satuan }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- PAGINATION --}}
    @if($items->hasPages())
        <section class="bg-bg pb-8">
            <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
                <div class="flex justify-center items-center gap-2">
                    {{-- Previous --}}
                    @if($items->onFirstPage())
                        <span class="px-3.5 py-2 bg-surface border border-border rounded-lg text-[13px] font-medium text-text-disabled">← Sebelumnya</span>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" class="px-3.5 py-2 bg-surface border border-border rounded-lg text-[13px] font-medium text-ink hover:bg-bg transition">← Sebelumnya</a>
                    @endif

                    {{-- Page Numbers (windowed if > 5 pages) --}}
                    @php
                        $lastPage = $items->lastPage();
                        $currentPage = $items->currentPage();
                        if ($lastPage <= 5) {
                            $startPage = 1;
                            $endPage = $lastPage;
                        } else {
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                            if ($startPage === 1) $endPage = min($lastPage, 5);
                            if ($endPage === $lastPage) $startPage = max(1, $lastPage - 4);
                        }
                    @endphp

                    @if($startPage > 1)
                        <a href="{{ $items->url(1) }}" class="px-3.5 py-2 bg-surface border border-border rounded-lg text-[13px] font-medium text-ink hover:bg-bg transition">1</a>
                        @if($startPage > 2)
                            <span class="px-2 py-2 text-[13px] text-text-muted">...</span>
                        @endif
                    @endif

                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <span class="px-3.5 py-2 bg-green-700 text-white rounded-lg text-[13px] font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $items->url($page) }}" class="px-3.5 py-2 bg-surface border border-border rounded-lg text-[13px] font-medium text-ink hover:bg-bg transition">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span class="px-2 py-2 text-[13px] text-text-muted">...</span>
                        @endif
                        <a href="{{ $items->url($lastPage) }}" class="px-3.5 py-2 bg-surface border border-border rounded-lg text-[13px] font-medium text-ink hover:bg-bg transition">{{ $lastPage }}</a>
                    @endif

                    {{-- Next --}}
                    @if($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" class="px-3.5 py-2 bg-surface border border-border rounded-lg text-[13px] font-medium text-ink hover:bg-bg transition">Selanjutnya →</a>
                    @else
                        <span class="px-3.5 py-2 bg-surface border border-border rounded-lg text-[13px] font-medium text-text-disabled">Selanjutnya →</span>
                    @endif
                </div>
            </div>
        </section>
    @endif
@endsection
