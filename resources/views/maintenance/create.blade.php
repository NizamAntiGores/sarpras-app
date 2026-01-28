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

                        {{-- Unit Selection Section --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Unit Barang</label>
                            
                            {{-- Controls: Condition Filter & Search --}}
                            <div class="flex flex-col md:flex-row justify-between gap-4 mb-4">
                                {{-- Condition Buttons --}}
                                <div class="flex flex-wrap gap-2">
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

                                {{-- Search Input --}}
                                <div class="relative w-full md:w-64">
                                    <input type="text" id="searchUnit" placeholder="Cari Kode / Nama..." 
                                           class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-9">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Current Selection Display --}}
                            <div id="selection-display" class="mb-3 hidden">
                                <span class="text-sm text-gray-700">Terpilih: </span>
                                <span id="selected-unit-text" class="font-bold text-blue-600 text-sm"></span>
                            </div>

                            {{-- Accordion List --}}
                            <div class="border border-gray-200 rounded-lg max-h-96 overflow-y-auto bg-gray-50" id="unitListContainer">
                                @forelse ($sarprasList as $sarpras)
                                    @if(isset($availableUnits[$sarpras->id]))
                                    <div class="sarpras-group border-b border-gray-200 last:border-b-0 bg-white" 
                                         data-nama="{{ strtolower($sarpras->nama_barang) }}">
                                        
                                        {{-- Header --}}
                                        <button type="button" class="w-full flex items-center justify-between p-3 hover:bg-gray-50 transition sarpras-header" onclick="toggleAccordion(this)">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400 transform transition-transform chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                                <div class="text-left">
                                                    <span class="font-medium text-gray-800 text-sm block">{{ $sarpras->nama_barang }}</span>
                                                    <span class="text-xs text-gray-400">{{ $sarpras->kode_barang }}</span>
                                                </div>
                                            </div>
                                            <span class="count-badge text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                                {{ $availableUnits[$sarpras->id]->count() }} unit
                                            </span>
                                        </button>

                                        {{-- Content --}}
                                        <div class="sarpras-content hidden border-t border-gray-100 p-3 bg-gray-50">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach ($availableUnits[$sarpras->id] as $unit)
                                                    <label class="unit-item relative flex items-start p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-400 bg-white transition"
                                                           data-kondisi="{{ $unit->kondisi }}"
                                                           data-kode="{{ strtolower($unit->kode_unit) }}">
                                                        <input type="radio" name="sarpras_unit_id" value="{{ $unit->id }}" 
                                                               class="mt-1 border-gray-300 text-blue-600 focus:ring-blue-500"
                                                               {{ old('sarpras_unit_id', $selectedUnit?->id) == $unit->id ? 'checked' : '' }}
                                                               onchange="updateSelectionDisplay(this, '{{ $unit->kode_unit }}', '{{ $sarpras->nama_barang }}')">
                                                        <div class="ml-3">
                                                            <span class="block text-sm font-medium text-gray-900">{{ $unit->kode_unit }}</span>
                                                            <span class="block text-xs text-gray-500 mt-0.5 capitalize">
                                                                @if ($unit->kondisi === 'baik')
                                                                    <span class="text-green-600">●</span> Baik
                                                                @elseif ($unit->kondisi === 'rusak_ringan')
                                                                    <span class="text-yellow-600">●</span> Rusak Ringan
                                                                @elseif ($unit->kondisi === 'rusak_berat')
                                                                    <span class="text-red-600">●</span> Rusak Berat
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <div class="p-8 text-center text-gray-500">
                                        Tidak ada unit yang tersedia untuk maintenance.
                                    </div>
                                @endforelse
                            </div>
                            @error('sarpras_unit_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <script>
                            let currentKondisi = 'all';

                            function toggleAccordion(btn) {
                                const content = btn.nextElementSibling;
                                const chevron = btn.querySelector('.chevron');
                                content.classList.toggle('hidden');
                                chevron.classList.toggle('rotate-90');
                            }

                            function updateSelectionDisplay(radio, kode, nama) {
                                const display = document.getElementById('selection-display');
                                const text = document.getElementById('selected-unit-text');
                                display.classList.remove('hidden');
                                text.textContent = `${kode} - ${nama}`;
                                
                                // Highlight active card logic if needed
                                document.querySelectorAll('.unit-item').forEach(el => {
                                    el.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500', 'bg-blue-50');
                                    el.classList.add('border-gray-200', 'bg-white');
                                });
                                const label = radio.closest('label');
                                label.classList.remove('border-gray-200', 'bg-white');
                                label.classList.add('ring-2', 'ring-blue-500', 'border-blue-500', 'bg-blue-50');
                            }

                            function applyFilters() {
                                const search = document.getElementById('searchUnit').value.toLowerCase();
                                const groups = document.querySelectorAll('.sarpras-group');

                                groups.forEach(group => {
                                    const items = group.querySelectorAll('.unit-item');
                                    const groupNama = group.dataset.nama;
                                    let visibleItemsCount = 0;

                                    items.forEach(item => {
                                        const itemKondisi = item.dataset.kondisi;
                                        const itemKode = item.dataset.kode;
                                        
                                        // Filter Logic
                                        const matchKondisi = (currentKondisi === 'all' || itemKondisi === currentKondisi);
                                        const matchSearch = (itemKode.includes(search) || groupNama.includes(search));

                                        if (matchKondisi && matchSearch) {
                                            item.classList.remove('hidden');
                                            visibleItemsCount++;
                                        } else {
                                            item.classList.add('hidden');
                                        }
                                    });

                                    // Update Badge Count for Visible Items
                                    const badge = group.querySelector('.count-badge');
                                    badge.textContent = visibleItemsCount + ' unit';

                                    // Show/Hide Group based on visible items
                                    if (visibleItemsCount > 0) {
                                        group.classList.remove('hidden');
                                        // Auto expand if searching specific thing
                                        if (search.length > 2) {
                                            group.querySelector('.sarpras-content').classList.remove('hidden');
                                            group.querySelector('.chevron').classList.add('rotate-90');
                                        }
                                    } else {
                                        group.classList.add('hidden');
                                    }
                                });
                            }

                            function filterUnits(kondisi) {
                                currentKondisi = kondisi;
                                
                                // Update Button Styles
                                document.querySelectorAll('.filter-btn').forEach(btn => {
                                    btn.classList.remove('bg-blue-600', 'text-white');
                                    btn.classList.add('bg-gray-100', 'text-gray-600');
                                });
                                const activeBtn = document.getElementById('btn-' + kondisi);
                                if(activeBtn) {
                                    activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
                                    activeBtn.classList.add('bg-blue-600', 'text-white');
                                }

                                applyFilters();
                            }

                            // Event Listeners
                            document.addEventListener('DOMContentLoaded', () => {
                                document.getElementById('searchUnit').addEventListener('input', applyFilters);
                                
                                // Initial State: Check if old value exists to highlight
                                const checkedRadio = document.querySelector('input[name="sarpras_unit_id"]:checked');
                                if(checkedRadio) {
                                    // Trigger display update manually or just highlight styling
                                    // But we don't have the data-params easily here without parsing parent.
                                    // Just simpler highlight:
                                    const label = checkedRadio.closest('label');
                                    label.classList.remove('border-gray-200', 'bg-white');
                                    label.classList.add('ring-2', 'ring-blue-500', 'border-blue-500', 'bg-blue-50');
                                    
                                    // Auto expand that group
                                    const group = checkedRadio.closest('.sarpras-group');
                                    group.querySelector('.sarpras-content').classList.remove('hidden');
                                    group.querySelector('.chevron').classList.add('rotate-90');
                                    
                                    // Update Text
                                    const code = label.dataset.kode.toUpperCase();
                                    const name = group.dataset.nama.toUpperCase(); // rough fallback
                                    document.getElementById('selection-display').classList.remove('hidden');
                                    document.getElementById('selected-unit-text').textContent = code;
                                }

                                // Apply initial filters (e.g. from URL param handled by Controller -> View -> JS?)
                                // The controller passes 'selectedKondisi'
                                const initKondisi = "{{ $selectedKondisi ?? 'all' }}";
                                filterUnits(initKondisi);
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
