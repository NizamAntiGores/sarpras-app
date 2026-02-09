<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('sarpras.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Barang: {{ $sarpras->nama_barang }}</h2>
            </div>
            <a href="{{ route('sarpras.units.index', $sarpras) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-teal-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Kelola Unit
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <ul class="text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('sarpras.update', $sarpras) }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return confirm('Simpan perubahan data barang ini?');">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="kode_barang" class="block text-sm font-medium text-gray-700 mb-1">Kode Barang <span class="text-red-500">*</span></label>
                                <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang', $sarpras->kode_barang) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Kode ini digunakan sebagai prefix kode unit</p>
                            </div>
                            <div>
                                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $sarpras->nama_barang) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- TIPE BARANG (NEW) --}}
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="font-semibold text-gray-800 mb-3">Tipe Barang</h4>
                            <div class="flex space-x-4">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="tipe" value="asset" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                           {{ old('tipe', $sarpras->tipe ?? 'asset') === 'asset' ? 'checked' : '' }}>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900 block">Aset / Inventaris</span>
                                        <span class="text-xs text-gray-500">Barang modal yang memiliki nomor seri unik (Contoh: Laptop, Proyektor).</span>
                                    </div>
                                </label>
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="radio" name="tipe" value="bahan" class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                                           {{ old('tipe', $sarpras->tipe ?? '') === 'bahan' ? 'checked' : '' }}>
                                    <div class="text-sm">
                                        <span class="font-medium text-gray-900 block">Bahan Habis Pakai</span>
                                        <span class="text-xs text-gray-500">Barang sekali pakai atau quantity-based (Contoh: Spidol, Tisu, Kertas).</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- FOTO BARANG --}}
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="font-semibold text-gray-800 mb-3">Foto Barang</h4>
                            
                            @if ($sarpras->foto)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Foto Saat Ini:</p>
                                    <img src="{{ Storage::url($sarpras->foto) }}" alt="{{ $sarpras->nama_barang }}" 
                                         class="max-h-40 rounded-lg border border-gray-200">
                                    <div class="mt-2">
                                        <label class="inline-flex items-center text-sm text-red-600 cursor-pointer">
                                            <input type="checkbox" name="hapus_foto" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                            <span class="ml-2">Hapus foto ini</span>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            
                            <div>
                                <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $sarpras->foto ? 'Ganti Foto' : 'Upload Foto' }}
                                </label>
                                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                       onchange="previewImage(this)">
                                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, WebP. Maksimal 2MB.</p>
                                <div id="preview-container" class="mt-3 hidden">
                                    <p class="text-sm text-gray-600 mb-1">Preview foto baru:</p>
                                    <img id="preview-image" src="" alt="Preview" class="max-h-40 rounded-lg border border-gray-200">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="kategori_id" id="kategori_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach ($kategori as $kat)
                                    <option value="{{ $kat->id }}" {{ old('kategori_id', $sarpras->kategori_id) == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Deskripsi barang (opsional)..."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('deskripsi', $sarpras->deskripsi) }}</textarea>
                        </div>

                        {{-- STOK (KHUSUS BAHAN HABIS PAKAI) --}}
                        @if($sarpras->tipe === 'bahan')
                            <div class="border border-indigo-200 rounded-lg p-4 bg-indigo-50">
                                <h4 class="font-semibold text-indigo-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    Stok per Lokasi
                                </h4>
                                
                                {{-- List Current Stocks --}}
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse($sarpras->stocks as $stock)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $stock->lokasi->nama_lokasi }}</td>
                                                    <td class="px-4 py-2 text-sm text-gray-900 font-bold">{{ $stock->quantity }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="px-4 py-2 text-sm text-gray-500 text-center">Belum ada stok.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td class="px-4 py-2 text-sm font-bold text-gray-700">Total</td>
                                                <td class="px-4 py-2 text-sm font-bold text-indigo-700">{{ $sarpras->stocks->sum('quantity') }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                {{-- Quick Add Stock Form --}}
                                <div class="mt-4 pt-4 border-t border-indigo-200">
                                    <h5 class="text-sm font-semibold text-indigo-900 mb-3">Tambah Stok Cepat</h5>
                                    <form action="{{ route('sarpras.add-stock', $sarpras) }}" method="POST" class="bg-white p-3 rounded-lg border border-indigo-100 shadow-sm" onsubmit="return confirm('Tambahkan stok ini? Pastikan jumlah dan lokasi benar.');">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                                            <div>
                                                <label for="qa_lokasi_id" class="block text-xs font-medium text-gray-700 mb-1">Lokasi</label>
                                                <select name="lokasi_id" id="qa_lokasi_id" required class="w-full rounded border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    @foreach ($lokasi as $loc)
                                                        <option value="{{ $loc->id }}">{{ $loc->nama_lokasi }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label for="qa_quantity" class="block text-xs font-medium text-gray-700 mb-1">Jumlah Tambahan</label>
                                                <input type="number" name="quantity" id="qa_quantity" min="1" value="1" required 
                                                       class="w-full rounded border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded text-sm font-semibold hover:bg-indigo-700 transition">
                                                    + Tambah Stok
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('sarpras.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase hover:bg-gray-50">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 rounded-lg text-xs font-semibold text-white uppercase hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview-image');
            const container = document.getElementById('preview-container');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
