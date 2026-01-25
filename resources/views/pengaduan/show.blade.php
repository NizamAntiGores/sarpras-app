<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pengaduan') }} #{{ $pengaduan->id }}
            </h2>
            @if(in_array(auth()->user()->role, ['admin', 'petugas']))
                <a href="{{ route('pengaduan.edit', $pengaduan) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Update Status
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Main Info --}}
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">{{ $pengaduan->judul }}</h3>
                            @php
                                $statusColors = [
                                    'belum_ditindaklanjuti' => 'bg-red-100 text-red-800',
                                    'sedang_diproses' => 'bg-yellow-100 text-yellow-800',
                                    'selesai' => 'bg-green-100 text-green-800',
                                    'ditutup' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusLabel = [
                                    'belum_ditindaklanjuti' => 'Belum Ditindaklanjuti',
                                    'sedang_diproses' => 'Sedang Diproses',
                                    'selesai' => 'Selesai',
                                    'ditutup' => 'Ditutup',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusColors[$pengaduan->status] }}">
                                {{ $statusLabel[$pengaduan->status] }}
                            </span>
                        </div>
                        
                        <div class="prose max-w-none text-gray-700 mb-6">
                            <p>{{ $pengaduan->deskripsi }}</p>
                        </div>

                        @if($pengaduan->foto)
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Foto Bukti:</h4>
                                <img src="{{ asset('storage/' . $pengaduan->foto) }}" alt="Foto Bukti" class="rounded-lg max-h-96 w-auto shadow-md">
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic mb-6">Tidak ada foto bukti.</p>
                        @endif

                        {{-- Metadata --}}
                        <div class="grid grid-cols-2 gap-4 border-t pt-4">
                            <div>
                                <span class="text-xs text-gray-500 block">Tanggal Lapor</span>
                                <span class="text-sm font-medium">{{ $pengaduan->created_at->format('d F Y, H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Lokasi</span>
                                <span class="text-sm font-medium">{{ $pengaduan->lokasi->nama_lokasi ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Pelapor</span>
                                <span class="text-sm font-medium">{{ $pengaduan->user->name }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Jenis Sarpras</span>
                                <span class="text-sm font-medium">{{ $pengaduan->sarpras->nama_barang ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Petugas Section (If Exists or Admin) --}}
                    @if($pengaduan->catatan_petugas)
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Catatan Petugas ({{ $pengaduan->petugas->name ?? 'Admin' }})</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>{{ $pengaduan->catatan_petugas }}</p>
                                    </div>
                                    <div class="mt-2 text-xs text-blue-600">
                                        Diperbarui: {{ $pengaduan->updated_at->format('d M Y, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar / Timeline maybe? For now just simple info --}}
                <div class="space-y-6">
                    <div class="bg-white p-6 rounded-lg shadow-sm">
                        <h4 class="font-semibold text-gray-900 mb-4">Informasi Bantuan</h4>
                        <ul class="text-sm text-gray-600 space-y-2">
                            <li>• Pengaduan yang masuk akan diverifikasi oleh petugas.</li>
                            <li>• Status "Sedang Diproses" berarti perbaikan atau pengecekan sedang berlangsung.</li>
                            <li>• Cek berkala halaman ini untuk melihat update catatan dari petugas.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
