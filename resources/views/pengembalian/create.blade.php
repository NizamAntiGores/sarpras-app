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

                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-semibold text-gray-800 text-lg">Inspeksi Kondisi Barang</h3>
                                <button type="button" onclick="checkAllGood()" class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm hover:bg-green-100 flex items-center gap-2 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    Pilih Semua "Baik"
                                </button>
                            </div>

                            <script>
                                function checkAllGood() {
                                    // Select all radio buttons with value 'baik' and trigger change
                                    document.querySelectorAll('input.kondisi-radio[value="baik"]').forEach(radio => {
                                        radio.checked = true;
                                        // Dispatch change event for Alpine.js to pick up
                                        radio.dispatchEvent(new Event('change'));
                                    });
                                }
                            </script>

                            @foreach ($peminjaman->details as $index => $detail)
                                @php $unitId = $detail->sarpras_unit_id; @endphp
                                <div class="bg-white border rounded-xl overflow-hidden shadow-sm" 
                                     x-data="{ kondisi: '{{ old("kondisi_{$unitId}", '') }}' }">
                                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                                        <div>
                                            <span class="font-bold text-gray-700">Unit #{{ $index + 1 }}</span>
                                            <span class="mx-2 text-gray-300">|</span>
                                            <span class="font-mono text-blue-600 font-medium">{{ $detail->sarprasUnit->kode_unit }}</span>
                                            <span class="ml-2 text-sm text-gray-500">{{ $detail->sarprasUnit?->sarpras?->nama_barang ?? '' }}</span>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Kondisi Awal: 
                                            <span class="font-medium {{ $detail->sarprasUnit->kondisi === 'baik' ? 'text-green-600' : 'text-yellow-600' }}">
                                                {{ ucfirst(str_replace('_', ' ', $detail->sarprasUnit->kondisi)) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-6 space-y-4">
                                        {{-- Kondisi Radio --}}
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-3">Kondisi Akhir <span class="text-red-500">*</span></label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="baik" 
                                                           class="peer sr-only kondisi-radio" required 
                                                           x-on:change="kondisi = 'baik'"
                                                           {{ old("kondisi_{$unitId}") == 'baik' ? 'checked' : '' }}>
                                                    <div class="p-3 border-2 rounded-lg text-center peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">✅</div>
                                                        <div class="font-medium text-sm text-green-700">Baik</div>
                                                    </div>
                                                </label>
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="rusak_ringan" 
                                                           class="peer sr-only kondisi-radio" 
                                                           x-on:change="kondisi = 'rusak_ringan'"
                                                           {{ old("kondisi_{$unitId}") == 'rusak_ringan' ? 'checked' : '' }}>
                                                    <div class="p-3 border-2 rounded-lg text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">⚠️</div>
                                                        <div class="font-medium text-sm text-yellow-700">Rusak Ringan</div>
                                                    </div>
                                                </label>
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="rusak_berat" 
                                                           class="peer sr-only kondisi-radio"
                                                           x-on:change="kondisi = 'rusak_berat'"
                                                           {{ old("kondisi_{$unitId}") == 'rusak_berat' ? 'checked' : '' }}>
                                                    <div class="p-3 border-2 rounded-lg text-center peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">🔧</div>
                                                        <div class="font-medium text-sm text-orange-700">Rusak Berat</div>
                                                    </div>
                                                </label>
                                                <label class="relative cursor-pointer">
                                                    <input type="radio" name="kondisi_{{ $unitId }}" value="hilang" 
                                                           class="peer sr-only kondisi-radio"
                                                           x-on:change="kondisi = 'hilang'"
                                                           {{ old("kondisi_{$unitId}") == 'hilang' ? 'checked' : '' }}>
                                                    <div class="p-3 border-2 rounded-lg text-center peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition">
                                                        <div class="text-2xl mb-1">❌</div>
                                                        <div class="font-medium text-sm text-red-700">Hilang</div>
                                                    </div>
                                                </label>
                                            </div>
                                            @error("kondisi_{$unitId}")
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Catatan & Foto - HANYA muncul jika ada masalah --}}
                                        <div x-show="kondisi && kondisi !== 'baik'" x-transition x-cloak>
                                            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                                <p class="text-sm font-semibold text-red-700 mb-3 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                    </svg>
                                                    Ada masalah dengan unit ini - Harap isi detail berikut:
                                                </p>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    {{-- Catatan --}}
                                                    <div>
                                                        <label for="catatan_{{ $unitId }}" class="block text-sm font-medium text-gray-700 mb-2">
                                                            Catatan / Keterangan <span class="text-red-500">*</span>
                                                        </label>
                                                        <textarea name="catatan_{{ $unitId }}" id="catatan_{{ $unitId }}" rows="2" 
                                                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                                  placeholder="Jelaskan masalah yang ditemukan...">{{ old("catatan_{$unitId}") }}</textarea>
                                                    </div>

                                                    {{-- Upload Foto --}}
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                                            Foto Bukti <span class="text-red-500">*</span>
                                                        </label>
                                                        <input type="file" name="foto_{{ $unitId }}" accept="image/*"
                                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition">
                                                        <p class="mt-1 text-xs text-gray-500">Upload foto kerusakan/bukti</p>
                                                    </div>
                                                </div>
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
