<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('sarpras.show', $sarpras) }}" class="text-gray-500 hover:text-gray-700 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kelola Unit: {{ $sarpras->nama_barang }}
                </h2>
            </div>
            <div>
                <a href="{{ route('sarpras.units.create', $sarpras) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Unit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filter & Stats --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
                        <div class="flex gap-4 w-full md:w-auto overflow-x-auto pb-2 md:pb-0">
                            <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-center min-w-[100px]">
                                <span class="block text-xs text-blue-600 font-bold uppercase">Total</span>
                                <span class="block text-xl font-bold text-blue-800">{{ $stats['total_unit'] }}</span>
                            </div>
                            <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-center min-w-[100px]">
                                <span class="block text-xs text-green-600 font-bold uppercase">Tersedia</span>
                                <span class="block text-xl font-bold text-green-800">{{ $stats['tersedia'] }}</span>
                            </div>
                            <div class="px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-lg text-center min-w-[100px]">
                                <span class="block text-xs text-yellow-600 font-bold uppercase">Dipinjam</span>
                                <span class="block text-xl font-bold text-yellow-800">{{ $stats['dipinjam'] }}</span>
                            </div>
                        </div>

                        <form method="GET" class="flex flex-wrap gap-2 w-full md:w-auto">
                            <select name="status" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="dihapusbukukan" {{ request('status') == 'dihapusbukukan' ? 'selected' : '' }}>Dihapusbukukan</option>
                            </select>
                            <select name="kondisi" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                <option value="">Semua Kondisi</option>
                                <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                                <option value="rusak_ringan" {{ request('kondisi') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="rusak_berat" {{ request('kondisi') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Unit..." 
                                class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="px-3 py-2 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-700">Cari</button>
                            @if(request()->hasAny(['status', 'kondisi', 'search']))
                                <a href="{{ route('sarpras.units.index', $sarpras) }}" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Reset</a>
                            @endif
                        </form>
                    </div>

                    {{-- BULK ACTION FORM --}}
                    <form id="bulkActionForm" action="{{ route('sarpras.units.bulk-action', $sarpras) }}" method="POST">
                        @csrf
                        
                        {{-- Sticky Bulk Action Bar --}}
                        <div id="bulkActionBar" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-full shadow-2xl border border-gray-200 px-6 py-3 z-50 flex items-center gap-4 transition-all duration-300 animate-bounce-in">
                            <div class="flex items-center gap-2 border-r border-gray-200 pr-4">
                                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold" id="selectedCount">0</span>
                                <span class="text-sm font-medium text-gray-700">Dipilih</span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <select name="action_type" id="actionType" class="rounded-full border-gray-300 text-sm py-1 pl-4 pr-8 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="" disabled selected>Pilih Aksi...</option>
                                    <option value="update_lokasi">📍 Pindah Lokasi</option>
                                    <option value="update_kondisi">🔧 Ubah Kondisi</option>
                                    <option value="delete">🗑️ Hapus (Hapus Buku)</option>
                                </select>

                                {{-- Specific inputs for actions (hidden by default) --}}
                                <select name="lokasi_id" id="lokasiInput" class="hidden rounded-full border-gray-300 text-sm py-1 pl-4 pr-8 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="" disabled selected>Pilih Lokasi Baru...</option>
                                    @foreach($lokasis as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                                
                                <select name="kondisi" id="kondisiInput" class="hidden rounded-full border-gray-300 text-sm py-1 pl-4 pr-8 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="" disabled selected>Pilih Kondisi Baru...</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak_ringan">Rusak Ringan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                </select>

                                <button type="submit" id="submitBulkBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-full text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    Terapkan
                                </button>
                                
                                <button type="button" id="cancelBulkBtn" class="text-gray-500 hover:text-gray-700 ml-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left">
                                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($units as $unit)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="unit_ids[]" value="{{ $unit->id }}" class="unit-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('sarpras.units.show', [$sarpras, $unit]) }}" class="text-blue-600 font-mono font-medium hover:underline">
                                                    {{ $unit->kode_unit }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                {{ $unit->lokasi->nama_lokasi ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($unit->kondisi == 'baik')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Baik</span>
                                                @elseif($unit->kondisi == 'rusak_ringan')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Rusak Ringan</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rusak Berat</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($unit->status == 'tersedia')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Tersedia</span>
                                                @elseif($unit->status == 'dipinjam')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Dipinjam</span>
                                                @elseif($unit->status == 'maintenance')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Maintenance</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Dihapus</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('sarpras.units.edit', [$sarpras, $unit]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                                <a href="{{ route('sarpras.units.show', [$sarpras, $unit]) }}" class="text-gray-600 hover:text-gray-900">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                Belum ada unit barang.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="mt-4">
                        {{ $units->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.unit-checkbox');
            const bulkActionBar = document.getElementById('bulkActionBar');
            const selectedCount = document.getElementById('selectedCount');
            const actionType = document.getElementById('actionType');
            const lokasiInput = document.getElementById('lokasiInput');
            const kondisiInput = document.getElementById('kondisiInput');
            const cancelBtn = document.getElementById('cancelBulkBtn');
            const submitBtn = document.getElementById('submitBulkBtn');

            function updateBulkBar() {
                const checked = document.querySelectorAll('.unit-checkbox:checked');
                selectedCount.innerText = checked.length;
                
                if (checked.length > 0) {
                    bulkActionBar.classList.remove('hidden');
                    bulkActionBar.classList.add('flex');
                } else {
                    bulkActionBar.classList.add('hidden');
                    bulkActionBar.classList.remove('flex');
                }
            }

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkBar();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateBulkBar();
                });
            });

            cancelBtn.addEventListener('click', function() {
                checkboxes.forEach(cb => cb.checked = false);
                selectAll.checked = false;
                updateBulkBar();
            });

            actionType.addEventListener('change', function() {
                // Reset specific inputs
                lokasiInput.classList.add('hidden');
                kondisiInput.classList.add('hidden');
                
                // Show relevant input
                if (this.value === 'update_lokasi') {
                    lokasiInput.classList.remove('hidden');
                    lokasiInput.required = true;
                    kondisiInput.required = false;
                } else if (this.value === 'update_kondisi') {
                    kondisiInput.classList.remove('hidden');
                    kondisiInput.required = true;
                    lokasiInput.required = false;
                } else {
                    lokasiInput.required = false;
                    kondisiInput.required = false;
                }
            });
            
            // Client-side confirmation
            submitBtn.addEventListener('click', function(e) {
                if (actionType.value === 'delete') {
                    if (!confirm('Apakah Anda yakin ingin menghapus (menghapusbukukan) item yang dipilih? Tindakan ini tidak dapat dibatalkan.')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
    <style>
        @keyframes bounce-in {
            0% { transform: translate(-50%, 100%); opacity: 0; }
            60% { transform: translate(-50%, -10%); opacity: 1; }
            100% { transform: translate(-50%, 0); }
        }
        .animate-bounce-in {
            animation: bounce-in 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
    </style>
    @endpush
</x-app-layout>
