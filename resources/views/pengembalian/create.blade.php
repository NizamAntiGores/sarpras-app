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
                    <div class="mb-6 bg-blue-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-800 mb-3 border-b border-indigo-200 pb-2">Info Peminjaman</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500 text-sm">Peminjam</p>
                                <p class="font-medium">{{ $peminjaman->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Barang</p>
                                <p class="font-medium">{{ $peminjaman->details->first()?->sarprasUnit?->sarpras?->nama_barang ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">Jumlah Dipinjam</p>
                                <p class="font-bold text-xl text-blue-600">{{ $peminjaman->jumlah_pinjam }}</p>
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

                        <div class="space-y-8">
                            
                            {{-- Tanggal Kembali --}}
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label for="tgl_kembali_aktual" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Pengembalian <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="tgl_kembali_aktual" id="tgl_kembali_aktual" 
                                       value="{{ old('tgl_kembali_aktual', date('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       class="w-full md:w-1/3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <hr class="border-gray-200">

                            <h3 class="font-semibold text-gray-800 text-lg">Inspeksi Kondisi Barang</h3>

                            @foreach ($peminjaman->details as $index => $detail)
                                <div class="bg-white border rounded-xl overflow-hidden shadow-sm">
                                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                        <div>
                                            <span class="font-bold text-gray-700">Unit #{{ $index + 1 }}</span>
                                            <span class="mx-2 text-gray-300">|</span>
                                            <span class="font-mono text-blue-600 font-medium">{{ $detail->sarprasUnit->kode_unit }}</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Kondisi Awal: {{ ucfirst(str_replace('_', ' ', $detail->sarprasUnit->kondisi)) }}
                                        </div>
                                    </div>

                                    <div class="p-6 space-y-6">
                                        {{-- Kondisi Radio --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Kondisi Akhir <span class="text-red-500">*</span></label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                @php $unitId = $detail->sarpras_unit_id; @endphp
                                                
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="baik" class="peer sr-only" required {{ old("kondisi_{$unitId}") == 'baik' ? 'checked' : '' }}>
                                                    <div class="p-3 border rounded-lg text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">✅</div>
                                                        <div class="font-medium text-sm text-green-700">Baik</div>
                                                    </div>
                                                </label>
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="rusak_ringan" class="peer sr-only" {{ old("kondisi_{$unitId}") == 'rusak_ringan' ? 'checked' : '' }}>
                                                    <div class="p-3 border rounded-lg text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">⚠️</div>
                                                        <div class="font-medium text-sm text-yellow-700">Rusak Ringan</div>
                                                    </div>
                                                </label>
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="rusak_berat" class="peer sr-only" {{ old("kondisi_{$unitId}") == 'rusak_berat' ? 'checked' : '' }}>
                                                    <div class="p-3 border rounded-lg text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">🔧</div>
                                                        <div class="font-medium text-sm text-orange-700">Rusak Berat</div>
                                                    </div>
                                                </label>
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="hilang" class="peer sr-only" {{ old("kondisi_{$unitId}") == 'hilang' ? 'checked' : '' }}>
                                                    <div class="p-3 border rounded-lg text-center peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">❌</div>
                                                        <div class="font-medium text-sm text-red-700">Hilang</div>
                                                    </div>
                                                </label>
                                            </div>
                                            @error("kondisi_{$unitId}")
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            {{-- Catatan --}}
                                            <div>
                                                <label for="catatan_{{ $unitId }}" class="block text-sm font-medium text-gray-700 mb-2">Catatan / Keterangan</label>
                                                <textarea name="catatan_{{ $unitId }}" id="catatan_{{ $unitId }}" rows="2" 
                                                          class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                          placeholder="Keterangan kondisi...">{{ old("catatan_{$unitId}") }}</textarea>
                                            </div>

                                            {{-- Upload Foto --}}
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti (Opsional)</label>
                                                <input type="file" name="foto_{{ $unitId }}" accept="image/*"
                                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="mt-8 pt-6 border-t flex justify-end items-center space-x-3">
                            <a href="{{ route('peminjaman.show', $peminjaman) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 shadow-md transition transform hover:-translate-y-0.5">
                                Simpan Pengembalian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
