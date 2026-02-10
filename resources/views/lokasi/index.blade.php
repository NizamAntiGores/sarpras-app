<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Lokasi</h2>
            <a href="{{ route('lokasi.create') }}"
                class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold uppercase hover:bg-blue-700 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Lokasi
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                    {{ session('error') }}
                </div>
            @endif
             @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('lokasi.bulk-update') }}" method="POST" id="bulk-form">
                        @csrf
                        
                        {{-- Bulk Actions Bar --}}
                        <div class="flex items-center justify-between mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-600">Aksi Massal:</span>
                                
                                <button type="submit" name="action" value="set_storefront" 
                                    onclick="return confirm('Aktifkan lokasi terpilih sebagai Storefront?')"
                                    class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-xs font-semibold rounded hover:bg-purple-700 shadow-sm transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Set Storefront
                                </button>
    
                                <button type="submit" name="action" value="remove_storefront" 
                                    onclick="return confirm('Nonaktifkan Storefront untuk lokasi terpilih?')"
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-xs font-semibold rounded hover:bg-gray-700 shadow-sm transition">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                    Unset Storefront
                                </button>
                            </div>
                            <span class="text-xs text-gray-400">* Pilih lokasi dengan checkbox</span>
                        </div>

                        <div class="overflow-x-auto rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center w-10">
                                            <input type="checkbox" id="check-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lokasi</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jumlah Unit</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($lokasi as $lok)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="px-4 py-4 text-center">
                                                <input type="checkbox" name="ids[]" value="{{ $lok->id }}" class="check-item rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 cursor-pointer">
                                            </td>
                                            <td class="px-4 py-4 font-medium text-gray-900">
                                                {{ $lok->nama_lokasi }}
                                            </td>
                                            <td class="px-4 py-4 text-sm text-gray-500">
                                                {{ $lok->keterangan ?? '-' }}
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                @if($lok->is_storefront)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        Storefront
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Standard
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <a href="{{ route('sarpras.index', ['lokasi_id' => $lok->id]) }}"
                                                    class="inline-block hover:opacity-80 transition" title="Lihat Sarpras">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-md {{ $lok->units_count > 0 ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ $lok->units_count }} Unit
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="px-4 py-4 text-center space-x-2 whitespace-nowrap">
                                                <a href="{{ route('lokasi.edit', $lok) }}" class="text-blue-600 hover:text-blue-900 inline-block p-1 hover:bg-blue-50 rounded transition" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <button type="button" onclick="confirmDelete('{{ $lok->id }}', '{{ $lok->nama_lokasi }}')" class="text-red-600 hover:text-red-900 inline-block p-1 hover:bg-red-50 rounded transition" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic bg-gray-50">
                                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span class="block text-gray-600">Belum ada data lokasi.</span>
                                                <a href="{{ route('lokasi.create') }}" class="text-blue-600 hover:underline mt-1 block">Tambah lokasi baru</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    {{-- Pagination --}}
                    @if ($lokasi->hasPages())
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            {{ $lokasi->links() }}
                        </div>
                    @endif

                    {{-- Hidden Forms for Delete Actions --}}
                    @foreach ($lokasi as $lok)
                        <form id="delete-form-{{ $lok->id }}" action="{{ route('lokasi.destroy', $lok) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for Interactivity --}}
    <script>
        // Check All / Uncheck All
        document.getElementById('check-all').addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.check-item');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        });

        // Confirm Delete
        function confirmDelete(id, name) {
            if (confirm('Apakah Anda yakin ingin menghapus lokasi "' + name + '"?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
</x-app-layout>