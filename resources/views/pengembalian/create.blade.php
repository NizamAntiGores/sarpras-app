<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('peminjaman.show', $peminjaman) }}" class="text-gray-500 hover:text-gray-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Proses Pengembalian</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Messages --}}
            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    {{-- Info Peminjaman --}}
                    <div class="mb-6 bg-indigo-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-3 border-b border-indigo-200 pb-2">Info Peminjaman</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 text-sm">Peminjam</p>
                                <p class="font-medium">{{ $peminjaman->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Barang</p>
                                <p class="font-medium">{{ $peminjaman->sarpras->nama_barang }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Jumlah Dipinjam</p>
                                <p class="font-bold text-xl text-indigo-600">{{ $peminjaman->jumlah_pinjam }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Tanggal Pinjam</p>
                                <p class="font-medium">{{ $peminjaman->tgl_pinjam->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Rencana Kembali</p>
                                <p class="font-medium">{{ $peminjaman->tgl_kembali_rencana->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Status Keterlambatan</p>
                                @if (now()->gt($peminjaman->tgl_kembali_rencana))
                                    <p class="font-medium text-red-600">Terlambat {{ now()->diffInDays($peminjaman->tgl_kembali_rencana) }} hari</p>
                                @else
                                    <p class="font-medium text-green-600">Tepat waktu</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Form Inspeksi --}}
                    <form action="{{ route('pengembalian.store', $peminjaman) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            {{-- Kondisi Akhir --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Kondisi Akhir Barang <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="kondisi_akhir" value="baik" class="peer sr-only" required {{ old('kondisi_akhir') == 'baik' ? 'checked' : '' }}>
                                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition">
                                            <div class="text-3xl mb-2">✅</div>
                                            <div class="font-medium text-green-700">Baik</div>
                                            <div class="text-xs text-gray-500">Kondisi normal</div>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="kondisi_akhir" value="rusak_ringan" class="peer sr-only" {{ old('kondisi_akhir') == 'rusak_ringan' ? 'checked' : '' }}>
                                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50 transition">
                                            <div class="text-3xl mb-2">⚠️</div>
                                            <div class="font-medium text-yellow-700">Rusak Ringan</div>
                                            <div class="text-xs text-gray-500">Masih bisa diperbaiki</div>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="kondisi_akhir" value="rusak_berat" class="peer sr-only" {{ old('kondisi_akhir') == 'rusak_berat' ? 'checked' : '' }}>
                                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50 transition">
                                            <div class="text-3xl mb-2">🔧</div>
                                            <div class="font-medium text-orange-700">Rusak Berat</div>
                                            <div class="text-xs text-gray-500">Sulit diperbaiki</div>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="kondisi_akhir" value="hilang" class="peer sr-only" {{ old('kondisi_akhir') == 'hilang' ? 'checked' : '' }}>
                                        <div class="p-4 border-2 rounded-lg text-center peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition">
                                            <div class="text-3xl mb-2">❌</div>
                                            <div class="font-medium text-red-700">Hilang</div>
                                            <div class="text-xs text-gray-500">Tidak dikembalikan</div>
                                        </div>
                                    </label>
                                </div>
                                @error('kondisi_akhir')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upload Foto --}}
                            <div>
                                <label for="foto_kondisi" class="block text-sm font-medium text-gray-700 mb-2">
                                    Foto Dokumentasi Kondisi
                                    <span class="text-gray-400 font-normal">(Opsional, max 2MB)</span>
                                </label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition" id="dropzone">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="foto_kondisi" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500">
                                                <span>Upload foto</span>
                                                <input id="foto_kondisi" name="foto_kondisi" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg">
                                            </label>
                                            <p class="pl-1">atau drag & drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG hingga 2MB</p>
                                    </div>
                                </div>
                                <div id="preview-container" class="mt-3 hidden">
                                    <img id="preview-image" class="max-h-48 rounded-lg mx-auto" src="" alt="Preview">
                                    <button type="button" id="remove-image" class="mt-2 text-sm text-red-600 hover:text-red-800">Hapus gambar</button>
                                </div>
                                @error('foto_kondisi')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan / Keterangan
                                    <span id="keterangan-required" class="text-red-500 hidden">* (Wajib diisi)</span>
                                </label>
                                <textarea id="keterangan" name="keterangan" rows="3" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jelaskan kondisi barang (wajib jika rusak/hilang)...">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="mt-8 pt-6 border-t flex justify-between items-center">
                            <a href="{{ route('peminjaman.show', $peminjaman) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Proses Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for image preview and keterangan required toggle --}}
    <script>
        const input = document.getElementById('foto_kondisi');
        const previewContainer = document.getElementById('preview-container');
        const previewImage = document.getElementById('preview-image');
        const removeBtn = document.getElementById('remove-image');

        input.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        removeBtn.addEventListener('click', function() {
            input.value = '';
            previewContainer.classList.add('hidden');
            previewImage.src = '';
        });

        // Toggle keterangan required based on kondisi
        const kondisiRadios = document.querySelectorAll('input[name="kondisi_akhir"]');
        const keteranganRequired = document.getElementById('keterangan-required');
        const keteranganTextarea = document.getElementById('keterangan');

        function updateKeteranganRequired() {
            const selected = document.querySelector('input[name="kondisi_akhir"]:checked');
            if (selected && selected.value !== 'baik') {
                keteranganRequired.classList.remove('hidden');
                keteranganTextarea.setAttribute('required', 'required');
            } else {
                keteranganRequired.classList.add('hidden');
                keteranganTextarea.removeAttribute('required');
            }
        }

        kondisiRadios.forEach(radio => {
            radio.addEventListener('change', updateKeteranganRequired);
        });

        // Initial check
        updateKeteranganRequired();
    </script>
</x-app-layout>
