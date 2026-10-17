{{-- Datalist untuk Autocomplete Jenis dan Satuan --}}
<datalist id="datalist-jenis">
    @foreach ($daftarJenis as $itemJenis)
        <option value="{{ $itemJenis }}"></option>
    @endforeach
</datalist>

<datalist id="datalist-satuan">
    @foreach ($daftarSatuan as $itemSatuan)
        <option value="{{ $itemSatuan }}"></option>
    @endforeach
</datalist>

{{-- MODAL 1: Tambah Barang Baru --}}
<div id="modal-tambah" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">Tambah Barang Baru</h2>
            <button type="button" onclick="tutupModal('modal-tambah')" class="touch-target px-3 py-1 text-gray-500 hover:text-gray-800 text-base font-semibold">Tutup</button>
        </div>

        <form method="POST" action="{{ route('admin.barang.store') }}" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- 1. Kode Item --}}
                <div>
                    <label for="tambah-kode" class="block text-base font-medium text-gray-700 mb-1">Kode Item *</label>
                    <input type="text" name="kode_item" id="tambah-kode" value="{{ old('kode_item') }}" required maxlength="20" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    <span class="text-xs text-gray-500 mt-1 block">Unik, maks 20 karakter</span>
                    @error('kode_item')
                        <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 2. Nama Item --}}
                <div>
                    <label for="tambah-nama" class="block text-base font-medium text-gray-700 mb-1">Nama Item *</label>
                    <input type="text" name="nama_item" id="tambah-nama" value="{{ old('nama_item') }}" required maxlength="100" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    @error('nama_item')
                        <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 3. Jenis --}}
                <div>
                    <label for="tambah-jenis" class="block text-base font-medium text-gray-700 mb-1">Jenis</label>
                    <input type="text" name="jenis" id="tambah-jenis" list="datalist-jenis" value="{{ old('jenis') }}" maxlength="50" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- 4. Merek --}}
                <div>
                    <label for="tambah-merek" class="block text-base font-medium text-gray-700 mb-1">Merek</label>
                    <input type="text" name="merek" id="tambah-merek" value="{{ old('merek') }}" maxlength="50" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- 5. Rak --}}
                <div>
                    <label for="tambah-rak" class="block text-base font-medium text-gray-700 mb-1">Rak</label>
                    <input type="text" name="rak" id="tambah-rak" value="{{ old('rak') }}" maxlength="20" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- 6. Satuan --}}
                <div>
                    <label for="tambah-satuan" class="block text-base font-medium text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="tambah-satuan" list="datalist-satuan" value="{{ old('satuan') }}" maxlength="20" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- 7. Tipe Item --}}
                <div>
                    <label for="tambah-tipe" class="block text-base font-medium text-gray-700 mb-1">Tipe Item *</label>
                    <select name="tipe_item" id="tambah-tipe" required onchange="cekTipeItemTambah()" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                        <option value="Barang" {{ old('tipe_item', 'Barang') === 'Barang' ? 'selected' : '' }}>Barang</option>
                        <option value="Jasa" {{ old('tipe_item') === 'Jasa' ? 'selected' : '' }}>Jasa</option>
                    </select>
                </div>

                {{-- 8. Stok Awal --}}
                <div>
                    <label for="tambah-stok" class="block text-base font-medium text-gray-700 mb-1">Stok Awal</label>
                    <input type="number" name="stok_awal" id="tambah-stok" value="{{ old('stok_awal', 0) }}" min="0" max="1000" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    @error('stok_awal')
                        <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 9. Harga Pokok --}}
                <div>
                    <label for="tambah-harga-pokok" class="block text-base font-medium text-gray-700 mb-1">Harga Pokok (Rp) *</label>
                    <input type="number" name="harga_pokok" id="tambah-harga-pokok" value="{{ old('harga_pokok', 0) }}" min="0" max="999999999" step="1" required class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    @error('harga_pokok')
                        <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                {{-- 10. Harga Jual --}}
                <div>
                    <label for="tambah-harga-jual" class="block text-base font-medium text-gray-700 mb-1">Harga Jual (Rp) *</label>
                    <input type="number" name="harga_jual" id="tambah-harga-jual" value="{{ old('harga_jual', 0) }}" min="0" max="999999999" step="1" required class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                    @error('harga_jual')
                        <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- 11. Batas Minimum --}}
            <div>
                <label for="tambah-batas" class="block text-base font-medium text-gray-700 mb-1">Batas Minimum</label>
                <input type="number" name="batas_minimum" id="tambah-batas" value="{{ old('batas_minimum', 5) }}" min="0" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                <span class="text-xs text-gray-500 mt-1 block">Dipakai untuk notifikasi stok menipis</span>
                @error('batas_minimum')
                    <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            {{-- 12. Keterangan --}}
            <div>
                <label for="tambah-keterangan" class="block text-base font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="tambah-keterangan" rows="3" maxlength="1000" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="tutupModal('modal-tambah')" class="touch-target px-5 py-2.5 border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
                <button type="submit" class="touch-target px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-base shadow-sm transition-colors">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: Ubah Data Barang --}}
<div id="modal-ubah" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">Ubah Data Barang</h2>
            <button type="button" onclick="tutupModal('modal-ubah')" class="touch-target px-3 py-1 text-gray-500 hover:text-gray-800 text-base font-semibold">Tutup</button>
        </div>

        <form method="POST" action="{{ route('admin.barang.update') }}" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="kode_item" id="ubah-kode-hidden">

            <div class="p-3 bg-gray-100 rounded-lg mb-2">
                <span class="text-sm text-gray-600 block">Kode Barang:</span>
                <span id="ubah-kode-display" class="text-lg font-bold text-gray-900"></span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama Item --}}
                <div class="md:col-span-2">
                    <label for="ubah-nama" class="block text-base font-medium text-gray-700 mb-1">Nama Item *</label>
                    <input type="text" name="nama_item" id="ubah-nama" required maxlength="100" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- Jenis --}}
                <div>
                    <label for="ubah-jenis" class="block text-base font-medium text-gray-700 mb-1">Jenis</label>
                    <input type="text" name="jenis" id="ubah-jenis" list="datalist-jenis" maxlength="50" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- Merek --}}
                <div>
                    <label for="ubah-merek" class="block text-base font-medium text-gray-700 mb-1">Merek</label>
                    <input type="text" name="merek" id="ubah-merek" maxlength="50" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- Rak --}}
                <div>
                    <label for="ubah-rak" class="block text-base font-medium text-gray-700 mb-1">Rak</label>
                    <input type="text" name="rak" id="ubah-rak" maxlength="20" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- Satuan --}}
                <div>
                    <label for="ubah-satuan" class="block text-base font-medium text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="ubah-satuan" list="datalist-satuan" maxlength="20" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>

                {{-- Tipe Item --}}
                <div>
                    <label for="ubah-tipe" class="block text-base font-medium text-gray-700 mb-1">Tipe Item *</label>
                    <select name="tipe_item" id="ubah-tipe" required class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                        <option value="Barang">Barang</option>
                        <option value="Jasa">Jasa</option>
                    </select>
                </div>

                {{-- Batas Minimum --}}
                <div>
                    <label for="ubah-batas" class="block text-base font-medium text-gray-700 mb-1">Batas Minimum</label>
                    <input type="number" name="batas_minimum" id="ubah-batas" min="0" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label for="ubah-keterangan" class="block text-base font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" id="ubah-keterangan" rows="3" maxlength="1000" class="w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="tutupModal('modal-ubah')" class="touch-target px-5 py-2.5 border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
                <button type="submit" class="touch-target px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-base shadow-sm transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 3: Ubah Harga Barang --}}
<div id="modal-harga" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">Ubah Harga Barang</h2>
            <button type="button" onclick="tutupModal('modal-harga')" class="touch-target px-3 py-1 text-gray-500 hover:text-gray-800 text-base font-semibold">Tutup</button>
        </div>

        <form method="POST" action="{{ route('admin.barang.harga') }}" class="p-6 space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="kode_item" id="harga-kode-hidden">

            <div class="p-3 bg-gray-100 rounded-lg mb-2">
                <span class="text-sm text-gray-600 block">Barang:</span>
                <span id="harga-info-barang" class="text-base font-bold text-gray-900"></span>
            </div>

            <div>
                <label for="harga-pokok-input" class="block text-base font-medium text-gray-700 mb-1">Harga Pokok (Rp) *</label>
                <input type="number" name="harga_pokok" id="harga-pokok-input" min="0" max="999999999" step="1" required oninput="cekPeringatanHarga()" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
            </div>

            <div>
                <label for="harga-jual-input" class="block text-base font-medium text-gray-700 mb-1">Harga Jual (Rp) *</label>
                <input type="number" name="harga_jual" id="harga-jual-input" min="0" max="999999999" step="1" required oninput="cekPeringatanHarga()" class="touch-target w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-base text-gray-800 shadow-sm focus:ring-2 focus:ring-emerald-600 focus:outline-none">
            </div>

            {{-- Peringatan Harga Jual < Harga Pokok --}}
            <div id="peringatan-harga" class="hidden p-3.5 bg-amber-50 border border-amber-300 rounded-lg text-amber-900 text-base font-medium">
                Peringatan: Harga Jual lebih rendah dari Harga Pokok
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="tutupModal('modal-harga')" class="touch-target px-5 py-2.5 border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
                <button type="submit" id="btn-simpan-harga" class="touch-target px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg text-base shadow-sm transition-colors">Simpan Harga</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 4: Hapus Data Barang --}}
<div id="modal-hapus" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden my-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50">
            <h2 class="text-xl font-bold text-gray-900">Konfirmasi Hapus Barang</h2>
            <button type="button" onclick="tutupModal('modal-hapus')" class="touch-target px-3 py-1 text-gray-500 hover:text-gray-800 text-base font-semibold">Tutup</button>
        </div>

        <form method="POST" action="{{ route('admin.barang.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('DELETE')
            <input type="hidden" name="kode_item" id="hapus-kode-hidden">

            <p class="text-base text-gray-700 leading-relaxed">
                Yakin hapus barang ini? Tindakan ini tidak bisa dibatalkan.
            </p>

            <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <span class="text-sm text-red-700 block">Item yang akan dihapus:</span>
                <span id="hapus-info-barang" class="text-base font-bold text-red-900"></span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="tutupModal('modal-hapus')" class="touch-target px-5 py-2.5 border border-gray-300 rounded-lg text-base font-medium text-gray-700 hover:bg-gray-100 transition-colors">Batal</button>
                <button type="submit" class="touch-target px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg text-base shadow-sm transition-colors">Hapus Data</button>
            </div>
        </form>
    </div>
</div>

