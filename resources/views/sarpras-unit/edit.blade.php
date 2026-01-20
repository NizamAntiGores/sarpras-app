<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Unit: {{ $unit->kode_unit }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $sarpras->nama_barang }}</p>
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
                    <form method="POST" action="{{ route('sarpras.units.update', [$sarpras, $unit]) }}">
                        @csrf
                        @method('PUT')

                        {{-- Kode Unit (Read-only) --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kode Unit</label>
                            <input type="text" value="{{ $unit->kode_unit }}" disabled
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm">
                        </div>

                        {{-- Lokasi --}}
                        <div class="mb-6">
                            <label for="lokasi_id" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Penyimpanan</label>
                            <select name="lokasi_id" id="lokasi_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}" {{ old('lokasi_id', $unit->lokasi_id) == $lokasi->id ? 'selected' : '' }}>
                                        {{ $lokasi->nama_lokasi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('lokasi_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kondisi --}}
                        <div class="mb-6">
                            <label for="kondisi" class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                            <select name="kondisi" id="kondisi" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="baik" {{ old('kondisi', $unit->kondisi) == 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak_ringan" {{ old('kondisi', $unit->kondisi) == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak_berat" {{ old('kondisi', $unit->kondisi) == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                            @error('kondisi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    {{ $unit->status === 'dipinjam' ? 'disabled' : '' }}>
                                <option value="tersedia" {{ old('status', $unit->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="dipinjam" {{ old('status', $unit->status) == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="maintenance" {{ old('status', $unit->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="dihapusbukukan" {{ old('status', $unit->status) == 'dihapusbukukan' ? 'selected' : '' }}>Dihapusbukukan</option>
                            </select>
                            @if ($unit->status === 'dipinjam')
                                <p class="mt-1 text-xs text-orange-600">Unit sedang dipinjam, status tidak dapat diubah manual.</p>
                                <input type="hidden" name="status" value="{{ $unit->status }}">
                            @endif
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Perolehan --}}
                        <div class="mb-6">
                            <label for="tanggal_perolehan" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Perolehan</label>
                            <input type="date" name="tanggal_perolehan" id="tanggal_perolehan" 
                                   value="{{ old('tanggal_perolehan', $unit->tanggal_perolehan?->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('tanggal_perolehan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nilai Perolehan --}}
                        <div class="mb-6">
                            <label for="nilai_perolehan" class="block text-sm font-medium text-gray-700 mb-2">Nilai Perolehan (Rp)</label>
                            <input type="number" name="nilai_perolehan" id="nilai_perolehan" 
                                   value="{{ old('nilai_perolehan', $unit->nilai_perolehan) }}" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('nilai_perolehan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg text-sm font-semibold uppercase hover:bg-indigo-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
