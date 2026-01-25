<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Maintenance</h2>
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
                    <form method="POST" action="{{ route('maintenance.store') }}">
                        @csrf

                        {{-- Unit --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kondisi Barang</label>
                            <div class="flex space-x-2 mb-3">
                                <button type="button" onclick="filterUnits('all')" class="filter-btn px-3 py-1.5 text-xs font-medium rounded-full bg-blue-600 text-white hover:bg-blue-700 transition" id="btn-all">
                                    Semua
                                </button>
                                <button type="button" onclick="filterUnits('rusak_berat')" class="filter-btn px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition" id="btn-rusak_berat">
                                    💀 Rusak Berat
                                </button>
                                <button type="button" onclick="filterUnits('rusak_ringan')" class="filter-btn px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition" id="btn-rusak_ringan">
                                    ⚠️ Rusak Ringan
                                </button>
                                <button type="button" onclick="filterUnits('baik')" class="filter-btn px-3 py-1.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition" id="btn-baik">
                                    ✅ Baik
                                </button>
                            </div>

                            <label for="sarpras_unit_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Unit <span class="text-gray-400 text-xs font-normal ml-1" id="unit-count-label"></span></label>
                            <select name="sarpras_unit_id" id="sarpras_unit_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">-- Pilih Unit --</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" 
                                            data-kondisi="{{ $unit->kondisi }}"
                                            {{ old('sarpras_unit_id', $selectedUnit?->id) == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->kode_unit }} - {{ $unit->sarpras->nama_barang }} ({{ ucfirst(str_replace('_', ' ', $unit->kondisi)) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('sarpras_unit_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <script>
                            function filterUnits(kondisi) {
                                // 1. Update Button Styles
                                document.querySelectorAll('.filter-btn').forEach(btn => {
                                    btn.classList.remove('bg-blue-600', 'text-white');
                                    btn.classList.add('bg-gray-100', 'text-gray-600');
                                });
                                const activeBtn = document.getElementById('btn-' + kondisi);
                                if(activeBtn) {
                                    activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
                                    activeBtn.classList.add('bg-blue-600', 'text-white');
                                }

                                // 2. Filter Select Options
                                const select = document.getElementById('sarpras_unit_id');
                                const options = select.querySelectorAll('option:not([value=""])'); // Exclude placeholder
                                let visibleCount = 0;

                                options.forEach(option => {
                                    if (kondisi === 'all' || option.getAttribute('data-kondisi') === kondisi) {
                                        option.style.display = ''; // Show
                                        option.disabled = false;   // Enable
                                        visibleCount++;
                                    } else {
                                        option.style.display = 'none'; // Hide visual
                                        option.disabled = true;        // Disable functionality
                                    }
                                });

                                // Reset selection if hidden
                                if (select.selectedOptions[0].style.display === 'none' || select.selectedOptions[0].disabled) {
                                    select.value = "";
                                }
                                
                                // Update Label
                                document.getElementById('unit-count-label').textContent = `(${visibleCount} unit tersedia)`;
                            }

                            // Auto-filter on load if parameter exists
                            document.addEventListener('DOMContentLoaded', () => {
                                const urlParams = new URLSearchParams(window.location.search);
                                const kondisiParam = urlParams.get('kondisi');
                                if (kondisiParam) {
                                    filterUnits(kondisiParam);
                                } else {
                                    filterUnits('all');
                                }
                            });
                        </script>

                        {{-- Jenis --}}
                        <div class="mb-6">
                            <label for="jenis" class="block text-sm font-medium text-gray-700 mb-2">Jenis Maintenance</label>
                            <select name="jenis" id="jenis" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="perbaikan" {{ old('jenis') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                <option value="servis_rutin" {{ old('jenis') == 'servis_rutin' ? 'selected' : '' }}>Servis Rutin</option>
                                <option value="kalibrasi" {{ old('jenis') == 'kalibrasi' ? 'selected' : '' }}>Kalibrasi</option>
                                <option value="penggantian_komponen" {{ old('jenis') == 'penggantian_komponen' ? 'selected' : '' }}>Penggantian Komponen</option>
                            </select>
                            @error('jenis') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-6">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Jelaskan detail kerusakan atau pekerjaan yang akan dilakukan...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div class="mb-6">
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('tanggal_mulai') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Biaya --}}
                        <div class="mb-6">
                            <label for="biaya" class="block text-sm font-medium text-gray-700 mb-2">Estimasi Biaya (Rp)</label>
                            <input type="number" name="biaya" id="biaya" value="{{ old('biaya') }}" min="0"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Opsional">
                            @error('biaya') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg text-sm font-semibold uppercase hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Mulai Maintenance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
