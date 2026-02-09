<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Maintenance</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $maintenance->sarprasUnit->kode_unit }} - {{ $maintenance->sarprasUnit->sarpras->nama_barang }}</p>
            </div>
            <a href="{{ route('maintenance.index') }}" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase hover:bg-gray-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('maintenance.update', $maintenance) }}" onsubmit="return confirm('Simpan perubahan maintenance ini?');">
                        @csrf
                        @method('PUT')

                        {{-- Unit (Read-only) --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unit</label>
                            <input type="text" value="{{ $maintenance->sarprasUnit->kode_unit }} - {{ $maintenance->sarprasUnit->sarpras->nama_barang }}" disabled
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        </div>

                        {{-- Jenis --}}
                        <div class="mb-6">
                            <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis Maintenance</label>
                            <select name="jenis" id="jenis" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="perbaikan" {{ old('jenis', $maintenance->jenis) == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                <option value="servis_rutin" {{ old('jenis', $maintenance->jenis) == 'servis_rutin' ? 'selected' : '' }}>Servis Rutin</option>
                                <option value="kalibrasi" {{ old('jenis', $maintenance->jenis) == 'kalibrasi' ? 'selected' : '' }}>Kalibrasi</option>
                                <option value="penggantian_komponen" {{ old('jenis', $maintenance->jenis) == 'penggantian_komponen' ? 'selected' : '' }}>Penggantian Komponen</option>
                            </select>
                            @error('jenis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Deskripsi Awal (Read-only) --}}
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Kerusakan / Permintaan</label>
                            <p class="text-gray-900 text-sm whitespace-pre-wrap">{{ $maintenance->deskripsi }}</p>
                        </div>

                        {{-- Tanggal Selesai (only show if status = selesai) --}}
                        <div class="mb-6" id="tanggal_selesai_wrapper" style="display: none;">
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" 
                                   value="{{ old('tanggal_selesai', $maintenance->tanggal_selesai?->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Isi tanggal selesai jika maintenance sudah selesai</p>
                            @error('tanggal_selesai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Biaya (only show if status = selesai) --}}
                        <div class="mb-6" id="biaya_wrapper" style="display: none;">
                            <label for="biaya" class="block text-sm font-medium text-gray-700 mb-2">Biaya Aktual (Rp)</label>
                            <input type="number" name="biaya" id="biaya" value="{{ old('biaya', $maintenance->biaya) }}" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('biaya') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Laporan Pengerjaan (only show if status = selesai) --}}
                        <div class="mb-6" id="deskripsi_wrapper" style="display: none;">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Laporan Pengerjaan / Solusi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Jelaskan tindakan perbaikan yang dilakukan...">{{ old('deskripsi', $maintenance->status == 'selesai' ? $maintenance->deskripsi : '') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Deskripsi ini akan menggantikan deskripsi awal sebagai laporan akhir.</p>
                            @error('deskripsi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="sedang_berlangsung" {{ old('status', $maintenance->status) == 'sedang_berlangsung' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                <option value="selesai" {{ old('status', $maintenance->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kondisi Setelah Maintenance (only show if status = selesai) --}}
                        <div class="mb-6" id="kondisi_setelah_wrapper" style="display: none;">
                            <label for="kondisi_setelah" class="block text-sm font-medium text-gray-700 mb-2">Kondisi Unit Setelah Maintenance</label>
                            <select name="kondisi_setelah" id="kondisi_setelah"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                            @error('kondisi_setelah') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg text-sm font-semibold uppercase hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('status').addEventListener('change', function() {
            const kondisiWrapper = document.getElementById('kondisi_setelah_wrapper');
            const tanggalSelesaiWrapper = document.getElementById('tanggal_selesai_wrapper');
            const biayaWrapper = document.getElementById('biaya_wrapper');
            const deskripsiWrapper = document.getElementById('deskripsi_wrapper');
            
            if (this.value === 'selesai') {
                kondisiWrapper.style.display = 'block';
                tanggalSelesaiWrapper.style.display = 'block';
                biayaWrapper.style.display = 'block';
                deskripsiWrapper.style.display = 'block';
            } else {
                kondisiWrapper.style.display = 'none';
                tanggalSelesaiWrapper.style.display = 'none';
                biayaWrapper.style.display = 'none';
                deskripsiWrapper.style.display = 'none';
            }
        });

        // Initial check
        if (document.getElementById('status').value === 'selesai') {
            document.getElementById('kondisi_setelah_wrapper').style.display = 'block';
            document.getElementById('tanggal_selesai_wrapper').style.display = 'block';
            document.getElementById('biaya_wrapper').style.display = 'block';
            document.getElementById('deskripsi_wrapper').style.display = 'block';
        }
    </script>
</x-app-layout>
