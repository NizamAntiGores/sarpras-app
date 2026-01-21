<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('sarpras.index') }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Barang Baru</h2>
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
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Sistem Unit-Based</h3>
                                <p class="mt-1 text-sm text-blue-700">
                                    Setelah membuat master barang, Anda akan diarahkan untuk menambahkan unit individual. 
                                    Setiap unit memiliki kode unik untuk tracking per item.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('sarpras.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="kode_barang" class="block text-sm font-medium text-gray-700 mb-1">Kode Barang <span class="text-red-500">*</span></label>
                                <input type="text" name="kode_barang" id="kode_barang" value="{{ old('kode_barang') }}" placeholder="CAM, LCD, MIC"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Kode singkat untuk identifikasi jenis barang. Digunakan sebagai prefix kode unit.</p>
                            </div>
                            <div>
                                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_barang" id="nama_barang" value="{{ old('nama_barang') }}" placeholder="Kamera DSLR Canon"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- FOTO BARANG --}}
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h4 class="font-semibold text-gray-800 mb-3">Foto Barang</h4>
                            <div>
                                <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Upload Foto</label>
                                <input type="file" name="foto" id="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                       onchange="previewImage(this)">
                                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, WebP. Maksimal 2MB.</p>
                                <div id="preview-container" class="mt-3 hidden">
                                    <img id="preview-image" src="" alt="Preview" class="max-h-40 rounded-lg border border-gray-200">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="kategori_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                            <select name="kategori_id" id="kategori_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategori as $kat)
                                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Deskripsi barang (opsional)..."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('sarpras.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-700 uppercase hover:bg-gray-50">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 rounded-lg text-xs font-semibold text-white uppercase hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan & Tambah Unit
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
