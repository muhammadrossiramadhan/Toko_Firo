@extends('layouts.publik')
@section('title', 'Katalog Barang - Toko Firo')

@section('content')
    {{-- KATALOG HEADER --}}
    <section class="bg-surface py-8 md:pb-6 lg:pb-6 md:pt-8 lg:pt-8">
        <div class="max-w-[1280px] mx-auto px-5 md:px-10 lg:px-[80px]">
            <h1 class="font-heading font-bold text-[32px] leading-[40px] text-ink">Katalog Barang</h1>
            <p class="text-sm text-text-muted mt-2">Katalog untuk cek ketersediaan. Pembelian langsung di toko.</p>
            @if($lastUpdate)
                <div class="inline-flex items-center gap-1.5 mt-3 px-2.5 py-1 bg-green-50">
                    <span class="text-xs font-medium text-green-700">Diperbarui {{ \Carbon\Carbon::parse($lastUpdate)->timezone('Asia/Jakarta')->translatedFormat('d M Y, H.i') }}</span>
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
                    <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau kode barang..." class="touch-target w-full min-h-[2.75rem] h-11 px-4 bg-bg border border-border text-sm text-ink placeholder:text-text-muted focus:outline-none focus:border-green-700 focus:ring-2 focus:ring-green-50 transition">
                </form>
                {{-- Filter Pills --}}
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('katalog', request()->only('cari')) }}" class="touch-target min-h-[2.75rem] inline-flex items-center px-4 py-2 text-[13px] font-medium {{ !request('jenis') ? 'bg-green-700 text-white' : 'bg-surface border border-border text-ink hover:bg-bg transition' }}">Semua</a>
                    @foreach($kategori as $kat)
                        <a href="{{ route('katalog', array_merge(request()->only('cari'), ['jenis' => $kat])) }}" class="touch-target min-h-[2.75rem] inline-flex items-center px-4 py-2 text-[13px] font-medium {{ request('jenis') === $kat ? 'bg-green-700 text-white' : 'bg-surface border border-border text-ink hover:bg-bg transition' }}">{{ $kat }}</a>
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
                            $gambarUrl = $item->gambar_url ?: ($item->foto ?: '/items/default.svg');
                        @endphp
                        <div class="bg-surface border border-border overflow-hidden rounded-none flex flex-col justify-between {{ $isHabis ? 'opacity-50' : '' }}">
                            <div>
                                <div class="aspect-square bg-bg flex items-center justify-center relative overflow-hidden rounded-none">
                                    <img src="{{ $gambarUrl }}" alt="{{ $item->nama_item }}" class="w-full h-full object-cover rounded-none" onerror="this.onerror=null; this.src='/items/default.svg';">
                                    @if($isHabis)
                                        <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-0.5 bg-danger-50 text-[11px] font-semibold text-danger rounded-none">Stok Habis</span>
                                    @elseif($isMenipis)
                                        <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-0.5 bg-[#FFEE10] text-[11px] font-semibold text-ink rounded-none">Menipis</span>
                                    @else
                                        <span class="absolute top-3 left-3 inline-flex items-center px-2.5 py-0.5 bg-green-50 text-[11px] font-semibold text-green-700 rounded-none">Aman</span>
                                    @endif
                                </div>
                                <div class="p-2.5 lg:p-3.5 space-y-1">
                                    @if($item->jenis)
                                        <div class="text-[11px] font-medium text-text-muted">{{ $item->jenis }}</div>
                                    @endif
                                    <h3 class="text-[13px] lg:text-sm font-semibold text-ink line-clamp-2">{{ $item->nama_item }}</h3>
                                    @if($item->deskripsi_produk)
                                        <p class="text-xs text-text-muted line-clamp-2 mt-1">{{ $item->deskripsi_produk }}</p>
                                    @endif
                                    <div class="flex flex-wrap items-center gap-1 lg:gap-2 pt-1">
                                        <span class="font-heading font-bold text-[13px] lg:text-[15px] text-ink">Rp {{ $harga }}</span>
                                        <span class="hidden lg:block w-px h-3 bg-border"></span>
                                        @if($isHabis)
                                            <span class="text-xs font-medium text-danger">Stok habis</span>
                                        @else
                                            <span class="text-[11px] lg:text-xs font-medium {{ $isMenipis ? 'text-ink' : 'text-text-muted' }}">Sisa {{ $stok }} {{ $satuan }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="p-2.5 lg:p-3.5 pt-0 space-y-2">
                                <button
                                    type="button"
                                    data-kode="{{ $item->kode_item }}"
                                    data-nama="{{ $item->nama_item }}"
                                    data-jenis="{{ $item->jenis }}"
                                    data-merek="{{ $item->merek }}"
                                    data-satuan="{{ $item->satuan ?: 'pcs' }}"
                                    data-harga="{{ $harga }}"
                                    data-stok="{{ $stok }}"
                                    data-status="{{ $isHabis ? 'Habis' : ($isMenipis ? 'Menipis' : 'Aman') }}"
                                    data-gambar="{{ $gambarUrl }}"
                                    data-deskripsi="{{ $item->deskripsi_produk ?: $item->keterangan }}"
                                    data-shopee="{{ $item->link_shopee }}"
                                    onclick="bukaDetailKatalog(this)"
                                    class="touch-target w-full min-h-[44px] inline-flex items-center justify-center bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold px-3 py-2 rounded-none transition-colors"
                                >
                                    Detail Produk
                                </button>
                                @if($item->link_shopee)
                                    <a href="{{ $item->link_shopee }}" target="_blank" rel="noopener noreferrer" class="touch-target w-full min-h-[44px] inline-flex items-center justify-center bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold px-3 py-2 rounded-none transition-colors">
                                        Beli di Shopee
                                    </a>
                                @endif
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
                <div class="flex flex-wrap justify-center items-center gap-2">
                    {{-- Previous --}}
                    @if($items->onFirstPage())
                        <span class="touch-target px-4 py-2.5 min-h-[2.75rem] inline-flex items-center justify-center bg-surface border border-border text-[13px] font-medium text-text-disabled">Sebelumnya</span>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" class="touch-target px-4 py-2.5 min-h-[2.75rem] inline-flex items-center justify-center bg-surface border border-border text-[13px] font-medium text-ink hover:bg-bg transition">Sebelumnya</a>
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
                        <a href="{{ $items->url(1) }}" class="touch-target px-3.5 py-2.5 min-h-[2.75rem] min-w-[2.75rem] inline-flex items-center justify-center bg-surface border border-border text-[13px] font-medium text-ink hover:bg-bg transition">1</a>
                        @if($startPage > 2)
                            <span class="px-2 py-2.5 min-h-[2.75rem] inline-flex items-center justify-center text-[13px] text-text-muted">...</span>
                        @endif
                    @endif

                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $currentPage)
                            <span class="touch-target px-3.5 py-2.5 min-h-[2.75rem] min-w-[2.75rem] inline-flex items-center justify-center bg-green-700 text-white text-[13px] font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $items->url($page) }}" class="touch-target px-3.5 py-2.5 min-h-[2.75rem] min-w-[2.75rem] inline-flex items-center justify-center bg-surface border border-border text-[13px] font-medium text-ink hover:bg-bg transition">{{ $page }}</a>
                        @endif
                    @endfor

                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span class="px-2 py-2.5 min-h-[2.75rem] inline-flex items-center justify-center text-[13px] text-text-muted">...</span>
                        @endif
                        <a href="{{ $items->url($lastPage) }}" class="touch-target px-3.5 py-2.5 min-h-[2.75rem] min-w-[2.75rem] inline-flex items-center justify-center bg-surface border border-border text-[13px] font-medium text-ink hover:bg-bg transition">{{ $lastPage }}</a>
                    @endif

                    {{-- Next --}}
                    @if($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" class="touch-target px-4 py-2.5 min-h-[2.75rem] inline-flex items-center justify-center bg-surface border border-border text-[13px] font-medium text-ink hover:bg-bg transition">Selanjutnya</a>
                    @else
                        <span class="touch-target px-4 py-2.5 min-h-[2.75rem] inline-flex items-center justify-center bg-surface border border-border text-[13px] font-medium text-text-disabled">Selanjutnya</span>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- MODAL DETAIL BARANG (K12 Standard: 0 SVG, 0 Emoji, rounded-none) --}}
    <div id="modal-detail-barang" class="fixed inset-0 z-50 bg-black/60 hidden flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-none shadow-2xl w-full max-w-lg overflow-hidden border border-gray-300">
            <div class="p-4 bg-emerald-800 text-white font-bold flex justify-between items-center rounded-none">
                <span class="text-lg">Detail Produk</span>
                <button type="button" onclick="tutupDetailKatalog()" class="touch-target min-h-[2.75rem] min-w-[2.75rem] text-white hover:text-gray-200 font-bold px-3 text-lg flex items-center justify-center">X</button>
            </div>
            <div class="p-6 space-y-4">
                <div id="detail-katalog-gambar-box" class="aspect-video bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden rounded-none">
                    <img id="detail-katalog-img" src="" alt="Foto Produk" class="w-full h-full object-contain rounded-none">
                </div>
                <div>
                    <span id="detail-katalog-kode" class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-0.5 rounded-none font-bold"></span>
                    <h3 id="detail-katalog-nama" class="text-xl font-bold text-gray-900 mt-1"></h3>
                </div>
                <div class="grid grid-cols-2 gap-3 text-sm border-t border-b border-gray-200 py-3">
                    <div>
                        <span class="text-gray-500 block">Kategori / Jenis:</span>
                        <span id="detail-katalog-jenis" class="font-semibold text-gray-800"></span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Merek:</span>
                        <span id="detail-katalog-merek" class="font-semibold text-gray-800"></span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Harga:</span>
                        <span id="detail-katalog-harga" class="font-bold text-emerald-700 text-base"></span>
                    </div>
                    <div>
                        <span class="text-gray-500 block">Ketersediaan:</span>
                        <span id="detail-katalog-stok" class="font-semibold"></span>
                    </div>
                </div>
                <div>
                    <span class="text-gray-500 text-sm block">Deskripsi:</span>
                    <p id="detail-katalog-deskripsi" class="text-sm text-gray-700 mt-1 whitespace-pre-line"></p>
                </div>
                <div id="detail-katalog-shopee-container" class="pt-2 hidden">
                    <a id="detail-katalog-shopee-btn" href="#" target="_blank" rel="noopener noreferrer" class="touch-target w-full min-h-[2.75rem] inline-flex items-center justify-center bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold px-4 py-2 rounded-none transition-colors">
                        Beli di Shopee
                    </a>
                </div>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button type="button" onclick="tutupDetailKatalog()" class="touch-target min-h-[2.75rem] px-6 py-2.5 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-none text-sm transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function bukaDetailKatalog(el) {
            const item = el.dataset || el;
            document.getElementById('detail-katalog-kode').textContent = item.kode || '-';
            document.getElementById('detail-katalog-nama').textContent = item.nama || '-';
            document.getElementById('detail-katalog-jenis').textContent = item.jenis || '-';
            document.getElementById('detail-katalog-merek').textContent = item.merek || '-';
            document.getElementById('detail-katalog-harga').textContent = 'Rp ' + (item.harga || '0');

            const stok = Number(item.stok) || 0;
            const satuan = item.satuan || 'pcs';
            const stokEl = document.getElementById('detail-katalog-stok');
            if (stok <= 0) {
                stokEl.textContent = 'Stok Habis';
                stokEl.className = 'font-semibold text-red-600';
            } else if (stok < 10) {
                stokEl.textContent = 'Menipis (Sisa ' + stok + ' ' + satuan + ')';
                stokEl.className = 'font-semibold text-amber-600';
            } else {
                stokEl.textContent = 'Tersedia (' + stok + ' ' + satuan + ')';
                stokEl.className = 'font-semibold text-emerald-600';
            }

            document.getElementById('detail-katalog-deskripsi').textContent = item.deskripsi || 'Tidak ada deskripsi.';

            const imgEl = document.getElementById('detail-katalog-img');
            imgEl.src = item.gambar || '/images/logo.png';

            const shopeeBox = document.getElementById('detail-katalog-shopee-container');
            const shopeeBtn = document.getElementById('detail-katalog-shopee-btn');
            const linkShopee = item.shopee || item.link_shopee;
            if (linkShopee) {
                shopeeBtn.href = linkShopee;
                shopeeBox.classList.remove('hidden');
            } else {
                shopeeBox.classList.add('hidden');
            }

            const modal = document.getElementById('modal-detail-barang');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function tutupDetailKatalog() {
            const modal = document.getElementById('modal-detail-barang');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
@endsection
