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

                    {{-- CHECKLIST KONDISI BARANG (MOVED OUTSIDE MAIN FORM) --}}
                    @if($sarpras->tipe === 'asset')
                    <div class="mt-8 pt-8 border-t-4 border-gray-100">
                        <div class="border border-purple-200 rounded-lg p-6 bg-purple-50">
                            <h4 class="font-semibold text-purple-900 mb-3 flex items-center text-lg">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                                Checklist Kondisi (Serah Terima & Pengembalian)
                            </h4>
                            <p class="text-sm text-purple-600 mb-6">
                                Daftar item di bawah ini akan digunakan oleh Petugas untuk memeriksa kondisi barang saat:
                                <br>1. <strong>Serah Terima</strong> (Barang keluar)
                                <br>2. <strong>Pengembalian</strong> (Barang masuk)
                            </p>

                            {{-- Current Checklist Items --}}
                            @if($sarpras->checklistTemplates->isNotEmpty())
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6 border border-purple-100">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-purple-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">No</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-purple-800 uppercase tracking-wider">Item Checklist</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-purple-800 uppercase tracking-wider">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($sarpras->checklistTemplates as $index => $template)
                                                <tr class="hover:bg-purple-50 transition">
                                                    <td class="px-6 py-3 text-sm text-gray-500 font-mono">{{ $index + 1 }}</td>
                                                    <td class="px-6 py-3 text-sm text-gray-900 font-medium">
                                                        <span class="inline-flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                                            </svg>
                                                            {{ $template->item_label }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <form action="{{ route('sarpras.checklist.destroy', [$sarpras, $template]) }}" method="POST" class="inline" 
                                                              onsubmit="return confirm('Hapus item checklist ini?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium transition tooltip p-2 hover:bg-red-50 rounded-full" title="Hapus">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="bg-white rounded-lg p-8 border-2 border-dashed border-purple-200 text-center mb-6">
                                    <svg class="w-12 h-12 text-purple-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-gray-500 font-medium">Belum ada item checklist.</p>
                                    <p class="text-gray-400 text-sm mt-1">Tambahkan item untuk mempermudah pengecekan kondisi barang.</p>
                                </div>
                            @endif

                            {{-- Add New Checklist Item --}}
                            <div class="mt-4 pt-4 border-t border-purple-200">
                                <h5 class="text-sm font-bold text-purple-900 mb-3">Tambah Item Checklist Baru</h5>
                                <form action="{{ route('sarpras.checklist.store', $sarpras) }}" method="POST" class="bg-white p-4 rounded-lg border border-purple-200 shadow-sm flex flex-col md:flex-row gap-3 items-end">
                                    @csrf
                                    <div class="flex-1 w-full">
                                        <label for="item_label" class="block text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">Nama Item</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </div>
                                            <input type="text" name="item_label" id="item_label" required
                                                   placeholder="Contoh: Lampu indikator menyala, Kabel power tidak terkelupas..."
                                                   class="pl-10 w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500 py-2">
                                        </div>
                                    </div>
                                    <button type="submit" class="w-full md:w-auto px-6 py-2 bg-purple-600 text-white rounded-lg text-sm font-bold hover:bg-purple-700 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        Tambah Item
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
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
