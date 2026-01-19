<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('sarpras.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Barang: {{ $sarpras->nama_barang }}</h2>
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

                    {{-- Info Card --}}
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-700">
                            <strong>Total Aset Saat Ini:</strong> {{ $sarpras->stok + $sarpras->stok_rusak }} unit
                            ({{ $sarpras->stok }} tersedia + {{ $sarpras->stok_rusak }} rusak)
                        </p>
                    </div>

                    <form action="{{ route('sarpras.update', $sarpras) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="kode_barang" class="block text-sm font-medium text-gray-700 mb-1">Kode Barang <span class="text-red-500">*</span></label>
                                <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang', $sarpras->kode_barang) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang', $sarpras->nama_barang) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                       onchange="previewImage(this)">
                                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, WebP. Maksimal 2MB.</p>
                                <div id="preview-container" class="mt-3 hidden">
                                    <p class="text-sm text-gray-600 mb-1">Preview foto baru:</p>
                                    <img id="preview-image" src="" alt="Preview" class="max-h-40 rounded-lg border border-gray-200">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                                <select name="kategori_id" id="kategori_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($kategori as $kat)
                                        <option value="{{ $kat->id }}" {{ old('kategori_id', $sarpras->kategori_id) == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="lokasi_id" class="block text-sm font-medium text-gray-700 mb-1">Lokasi <span class="text-red-500">*</span></label>
                                <select name="lokasi_id" id="lokasi_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach ($lokasi as $lok)
                                        <option value="{{ $lok->id }}" {{ old('lokasi_id', $sarpras->lokasi_id) == $lok->id ? 'selected' : '' }}>{{ $lok->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- STOK SECTION --}}
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="font-semibold text-gray-800 mb-4">Manajemen Stok</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="stok" class="block text-sm font-medium text-gray-700 mb-1">
                                        Stok Tersedia (Bagus) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="stok" id="stok" value="{{ old('stok', $sarpras->stok) }}" min="0"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Jumlah barang yang siap dipinjam</p>
                                </div>
                                <div>
                                    <label for="stok_rusak" class="block text-sm font-medium text-gray-700 mb-1">
                                        Stok Rusak
                                    </label>
                                    <input type="number" name="stok_rusak" id="stok_rusak" value="{{ old('stok_rusak', $sarpras->stok_rusak) }}" min="0"
                                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Jumlah barang rusak/maintenance</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="kondisi_awal" class="block text-sm font-medium text-gray-700 mb-1">Kondisi Awal (saat pertama kali dicatat)</label>
                            <select name="kondisi_awal" id="kondisi_awal" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="baik" {{ old('kondisi_awal', $sarpras->kondisi_awal) == 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak" {{ old('kondisi_awal', $sarpras->kondisi_awal) == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('sarpras.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase hover:bg-gray-50">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-lg text-xs font-semibold text-white uppercase hover:bg-indigo-700">
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
