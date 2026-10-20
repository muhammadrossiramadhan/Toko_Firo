@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex items-center gap-2">
        {{-- Tombol Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <button
                type="button"
                disabled
                class="touch-target flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-base font-medium text-gray-400 cursor-not-allowed"
            >
                Sebelumnya
            </button>
        @else
            <a
                href="{{ $paginator->previousPageUrl() }}"
                class="touch-target flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                Sebelumnya
            </a>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            {{-- Tiga titik separator --}}
            @if (is_string($element))
                <span class="touch-target flex items-center justify-center px-3 py-2 text-base text-gray-400 font-bold">
                    {{ $element }}
                </span>
            @endif

            {{-- Link Halaman --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="touch-target flex items-center justify-center px-4 py-2 bg-emerald-600 border border-emerald-600 rounded-lg text-base font-bold text-white shadow-sm">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $url }}"
                            class="touch-target flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Tombol Selanjutnya --}}
        @if ($paginator->hasMorePages())
            <a
                href="{{ $paginator->nextPageUrl() }}"
                class="touch-target flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-50 transition-colors"
            >
                Selanjutnya
            </a>
        @else
            <button
                type="button"
                disabled
                class="touch-target flex items-center justify-center px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-base font-medium text-gray-400 cursor-not-allowed"
            >
                Selanjutnya
            </button>
        @endif
    </nav>
@endif

