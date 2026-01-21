<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Unit</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $sarpras->nama_barang }} ({{ $sarpras->kode_barang }})</p>
            </div>
            <a href="{{ route('sarpras.units.index', $sarpras) }}" class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold uppercase hover:bg-gray-300">
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
                    <form method="POST" action="{{ route('sarpras.units.store', $sarpras) }}">
                        @csrf

                        {{-- Jumlah Unit --}}
                        <div class="mb-6">
                            <label for="jumlah_unit" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Unit yang Ditambahkan</label>
                            <input type="number" name="jumlah_unit" id="jumlah_unit" value="{{ old('jumlah_unit', 1) }}" min="1" max="100"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Kode unit akan digenerate otomatis: {{ $sarpras->kode_barang }}-001, {{ $sarpras->kode_barang }}-002, dst.</p>
                            @error('jumlah_unit') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Lokasi --}}
                        <div class="mb-6">
                            <label for="lokasi_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Penyimpanan</label>
                            <select name="lokasi_id" id="lokasi_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ old('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                                        {{ $lokasi->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lokasi_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kondisi --}}
                        <div class="mb-6">
                            <label for="kondisi" class="block text-sm font-medium text-gray-700 mb-2">Kondisi Awal</label>
                            <select name="kondisi" id="kondisi" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="baik" {{ old('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak_ringan" {{ old('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak_berat" {{ old('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                            @error('kondisi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Perolehan --}}
                        <div class="mb-6">
                            <label for="tanggal_perolehan" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" id="tanggal_perolehan" value="{{ old('tanggal_perolehan', date('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('tanggal_perolehan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nilai Perolehan --}}
                        <div class="mb-6">
                            <label for="nilai_perolehan" class="block text-sm font-medium text-gray-700 mb-2">Nilai Perolehan (Rp)</label>
                            <input type="number" name="nilai_perolehan" id="nilai_perolehan" value="{{ old('nilai_perolehan') }}" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Opsional">
                            @error('nilai_perolehan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg text-sm font-semibold uppercase hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
